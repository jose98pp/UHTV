<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;

class ImageUrlHelper
{
    /**
     * Generar URL de imagen con fallback automático
     *
     * @param string|null $imagePath
     * @param string|null $size
     * @return string
     */
    public static function getImageUrl(?string $imagePath, ?string $size = null): string
    {
        // Si no hay imagen, devolver imagen por defecto
        if (empty($imagePath)) {
            return self::getDefaultImageUrl();
        }

        // Verificar si la imagen existe
        if (!Storage::disk('public')->exists($imagePath)) {
            return self::getDefaultImageUrl();
        }

        // Si hay CDN configurado, usarlo
        if (Config::get('images.cdn.enabled')) {
            return self::getCdnUrl($imagePath, $size);
        }

        // URL local estándar
        return asset('storage/' . $imagePath);
    }

    /**
     * Obtener URL de imagen por defecto
     *
     * @return string
     */
    public static function getDefaultImageUrl(): string
    {
        $defaultImage = Config::get('images.fallback.default_image', 'images/default-news.svg');
        return asset($defaultImage);
    }

    /**
     * Generar URL de CDN
     *
     * @param string $imagePath
     * @param string|null $size
     * @return string
     */
    private static function getCdnUrl(string $imagePath, ?string $size = null): string
    {
        $cdnBaseUrl = Config::get('images.cdn.base_url');
        
        if (!$cdnBaseUrl) {
            return asset('storage/' . $imagePath);
        }

        $url = rtrim($cdnBaseUrl, '/') . '/' . ltrim($imagePath, '/');
        
        // Agregar parámetros de tamaño si se especifica
        if ($size && Config::get('images.performance.responsive_images')) {
            $sizes = Config::get('images.performance.thumbnail_sizes', []);
            if (isset($sizes[$size])) {
                [$width, $height] = $sizes[$size];
                $url .= "?w={$width}&h={$height}&fit=crop";
            }
        }

        return $url;
    }

    /**
     * Generar múltiples tamaños de imagen para responsive design
     *
     * @param string|null $imagePath
     * @return array
     */
    public static function getResponsiveImageUrls(?string $imagePath): array
    {
        if (empty($imagePath) || !Storage::disk('public')->exists($imagePath)) {
            $defaultUrl = self::getDefaultImageUrl();
            return [
                'small' => $defaultUrl,
                'medium' => $defaultUrl,
                'large' => $defaultUrl,
                'original' => $defaultUrl,
            ];
        }

        return [
            'small' => self::getImageUrl($imagePath, 'small'),
            'medium' => self::getImageUrl($imagePath, 'medium'),
            'large' => self::getImageUrl($imagePath, 'large'),
            'original' => self::getImageUrl($imagePath),
        ];
    }

    /**
     * Generar atributos srcset para imágenes responsive
     *
     * @param string|null $imagePath
     * @return string
     */
    public static function getSrcSet(?string $imagePath): string
    {
        $urls = self::getResponsiveImageUrls($imagePath);
        
        $srcset = [];
        $srcset[] = $urls['small'] . ' 150w';
        $srcset[] = $urls['medium'] . ' 300w';
        $srcset[] = $urls['large'] . ' 600w';
        $srcset[] = $urls['original'] . ' 1200w';
        
        return implode(', ', $srcset);
    }

    /**
     * Generar HTML de imagen con lazy loading y responsive
     *
     * @param string|null $imagePath
     * @param string $alt
     * @param array $attributes
     * @return string
     */
    public static function generateImageHtml(?string $imagePath, string $alt = '', array $attributes = []): string
    {
        $url = self::getImageUrl($imagePath);
        $srcset = self::getSrcSet($imagePath);
        
        $defaultAttributes = [
            'src' => $url,
            'alt' => $alt,
            'loading' => 'lazy',
            'decoding' => 'async',
        ];

        if (Config::get('images.performance.responsive_images')) {
            $defaultAttributes['srcset'] = $srcset;
            $defaultAttributes['sizes'] = '(max-width: 300px) 150px, (max-width: 600px) 300px, (max-width: 1200px) 600px, 1200px';
        }

        $attributes = array_merge($defaultAttributes, $attributes);
        
        $attributeString = '';
        foreach ($attributes as $key => $value) {
            $attributeString .= ' ' . $key . '="' . htmlspecialchars($value) . '"';
        }

        return '<img' . $attributeString . '>';
    }

    /**
     * Verificar si una imagen necesita optimización
     *
     * @param string $imagePath
     * @return bool
     */
    public static function needsOptimization(string $imagePath): bool
    {
        if (!Storage::disk('public')->exists($imagePath)) {
            return false;
        }

        $fullPath = Storage::disk('public')->path($imagePath);
        $fileSize = filesize($fullPath);
        $maxSize = Config::get('images.validation.max_file_size', 5 * 1024 * 1024);

        return $fileSize > $maxSize;
    }

    /**
     * Obtener información de imagen para debugging
     *
     * @param string|null $imagePath
     * @return array
     */
    public static function getImageDebugInfo(?string $imagePath): array
    {
        if (empty($imagePath)) {
            return [
                'exists' => false,
                'using_default' => true,
                'url' => self::getDefaultImageUrl(),
            ];
        }

        $exists = Storage::disk('public')->exists($imagePath);
        
        return [
            'path' => $imagePath,
            'exists' => $exists,
            'using_default' => !$exists,
            'url' => self::getImageUrl($imagePath),
            'size' => $exists ? Storage::disk('public')->size($imagePath) : null,
            'last_modified' => $exists ? Storage::disk('public')->lastModified($imagePath) : null,
            'responsive_urls' => self::getResponsiveImageUrls($imagePath),
        ];
    }
}