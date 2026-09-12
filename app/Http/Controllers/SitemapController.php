<?php

namespace App\Http\Controllers;

use App\Models\Noticia;
use App\Models\Category;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    /**
     * Generar dinámicamente el archivo sitemap.xml
     */
    public function index(): Response
    {
        $xml = Cache::remember('sitemap_xml_data', 3600, function () {
            return $this->buildSitemapXml();
        });

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'X-Robots-Tag' => 'noindex', // El propio sitemap no necesita ser indexado como página web
        ]);
    }

    /**
     * Construir la estructura XML compatible con Google, Bing y Yahoo
     */
    private function buildSitemapXml(): string
    {
        $baseUrl = config('app.url') ?: url('/');
        $baseUrl = rtrim($baseUrl, '/');

        $noticias = Noticia::with('category')
            ->where('publicada', true)
            ->latest('updated_at')
            ->take(1000)
            ->get();

        $categorias = Category::has('noticias')->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" '
              . 'xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" '
              . 'xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">' . "\n";

        // 1. Portada principal
        $xml .= "  <url>\n";
        $xml .= "    <loc>" . htmlspecialchars(route('portada')) . "</loc>\n";
        $xml .= "    <lastmod>" . now()->tz('UTC')->toAtomString() . "</lastmod>\n";
        $xml .= "    <changefreq>hourly</changefreq>\n";
        $xml .= "    <priority>1.0</priority>\n";
        $xml .= "  </url>\n";

        // 2. Canal de Transmisiones En Vivo y Podcasts
        $xml .= "  <url>\n";
        $xml .= "    <loc>" . htmlspecialchars(route('transmisiones.en-vivo')) . "</loc>\n";
        $xml .= "    <lastmod>" . now()->tz('UTC')->toAtomString() . "</lastmod>\n";
        $xml .= "    <changefreq>daily</changefreq>\n";
        $xml .= "    <priority>0.9</priority>\n";
        $xml .= "  </url>\n";

        // 3. Categorías activas
        foreach ($categorias as $cat) {
            $catUrl = $cat->url;
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($catUrl) . "</loc>\n";
            $xml .= "    <lastmod>" . $cat->updated_at->tz('UTC')->toAtomString() . "</lastmod>\n";
            $xml .= "    <changefreq>daily</changefreq>\n";
            $xml .= "    <priority>0.8</priority>\n";
            $xml .= "  </url>\n";
        }

        // 4. Noticias publicadas
        foreach ($noticias as $noticia) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($noticia->url) . "</loc>\n";
            $xml .= "    <lastmod>" . $noticia->updated_at->tz('UTC')->toAtomString() . "</lastmod>\n";
            $xml .= "    <changefreq>weekly</changefreq>\n";
            $xml .= "    <priority>0.7</priority>\n";

            // Imagen asociada para Google Images
            if (!empty($noticia->imagen)) {
                $imgUrl = str_starts_with($noticia->imagen, 'http') ? $noticia->imagen : asset($noticia->imagen);
                $xml .= "    <image:image>\n";
                $xml .= "      <image:loc>" . htmlspecialchars($imgUrl) . "</image:loc>\n";
                $xml .= "      <image:title>" . htmlspecialchars($noticia->titulo) . "</image:title>\n";
                $xml .= "    </image:image>\n";
            }

            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return $xml;
    }
}
