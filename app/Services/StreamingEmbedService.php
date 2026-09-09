<?php

namespace App\Services;

use Illuminate\Support\Str;

class StreamingEmbedService
{
    /**
     * Detectar la plataforma según la URL proporcionada
     */
    public function detectPlatform(string $input): string
    {
        $input = trim($input);

        // Si es un iframe, buscar dentro del src
        if (Str::contains($input, '<iframe') && preg_match('/src=["\']([^"\']+)["\']/', $input, $match)) {
            $input = $match[1];
        }

        if (Str::contains($input, ['youtube.com', 'youtu.be'])) {
            return 'youtube';
        }

        if (Str::contains($input, ['facebook.com', 'fb.watch'])) {
            return 'facebook';
        }

        if (Str::contains($input, ['tiktok.com'])) {
            return 'tiktok';
        }

        if (Str::contains($input, ['twitch.tv'])) {
            return 'twitch';
        }

        return 'otro';
    }

    /**
     * Generar la URL de embed interactiva y segura para la plataforma
     */
    public function generateEmbedUrl(string $input, ?string $platform = null): string
    {
        $input = trim($input);

        // 1. Si el usuario pegó un código iframe completo, extraer el src
        if (Str::contains($input, '<iframe') && preg_match('/src=["\']([^"\']+)["\']/', $input, $matches)) {
            return htmlspecialchars_decode($matches[1]);
        }

        if (!$platform || $platform === 'otro') {
            $platform = $this->detectPlatform($input);
        }

        switch ($platform) {
            case 'youtube':
                return $this->buildYouTubeEmbed($input);

            case 'facebook':
                return $this->buildFacebookEmbed($input);

            case 'tiktok':
                return $this->buildTikTokEmbed($input);

            case 'twitch':
                return $this->buildTwitchEmbed($input);

            default:
                return $input;
        }
    }

    /**
     * Generar miniatura automática si es posible (especialmente para YouTube)
     */
    public function generateThumbnailUrl(string $input, ?string $platform = null): ?string
    {
        if (!$platform || $platform === 'otro') {
            $platform = $this->detectPlatform($input);
        }

        if ($platform === 'youtube') {
            $id = $this->extractYouTubeId($input);
            if ($id) {
                return "https://img.youtube.com/vi/{$id}/hqdefault.jpg";
            }
        }

        return null;
    }

    /**
     * Extraer ID de video de YouTube
     */
    public function extractYouTubeId(string $url): ?string
    {
        if (preg_match('/(?:youtu\.be\/|v=|\/embed\/|\/live\/|\/shorts\/)([a-zA-Z0-9_-]{11})/', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Construir Embed de YouTube
     */
    private function buildYouTubeEmbed(string $url): string
    {
        // Si ya es un embed directo
        if (Str::contains($url, 'youtube.com/embed/')) {
            return $url;
        }

        // Si es una lista de reproducción
        if (Str::contains($url, 'list=')) {
            if (preg_match('/list=([a-zA-Z0-9_-]+)/', $url, $matches)) {
                return "https://www.youtube-nocookie.com/embed?listType=playlist&list={$matches[1]}&autoplay=1";
            }
        }

        $id = $this->extractYouTubeId($url);
        if ($id) {
            return "https://www.youtube-nocookie.com/embed/{$id}?autoplay=1&rel=0";
        }

        return $url;
    }

    /**
     * Construir Embed de Facebook
     */
    private function buildFacebookEmbed(string $url): string
    {
        if (Str::contains($url, 'facebook.com/plugins/video.php')) {
            return $url;
        }

        $encodedUrl = urlencode($url);
        return "https://www.facebook.com/plugins/video.php?href={$encodedUrl}&show_text=false&autoplay=1";
    }

    /**
     * Construir Embed de TikTok
     */
    private function buildTikTokEmbed(string $url): string
    {
        if (Str::contains($url, 'tiktok.com/embed/v2/')) {
            return $url;
        }

        // Extraer el ID numérico del video de TikTok
        if (preg_match('/video\/(\d+)/', $url, $matches)) {
            return "https://www.tiktok.com/embed/v2/{$matches[1]}";
        }

        return $url;
    }

    /**
     * Construir Embed de Twitch
     */
    private function buildTwitchEmbed(string $url): string
    {
        if (Str::contains($url, 'player.twitch.tv')) {
            return $url;
        }

        // Extraer nombre de canal
        if (preg_match('/twitch\.tv\/([a-zA-Z0-9_]+)/', $url, $matches)) {
            $channel = $matches[1];
            $host = request()->getHost() ?: 'localhost';
            return "https://player.twitch.tv/?channel={$channel}&parent={$host}&parent=localhost&parent=127.0.0.1&autoplay=true";
        }

        return $url;
    }
}
