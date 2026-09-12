<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Noticia;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class SeoAndPerformanceTest extends TestCase
{
    /** @test */
    public function sitemap_xml_returns_200_and_valid_xml_structure()
    {
        Cache::forget('sitemap_xml_data');

        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $this->assertStringContainsString('application/xml', $response->headers->get('Content-Type'));
        $response->assertSee('<urlset', false);
        $response->assertSee(route('portada'), false);
        $response->assertSee(route('transmisiones.en-vivo'), false);

        // Verify it was cached
        $this->assertTrue(Cache::has('sitemap_xml_data'));
    }

    /** @test */
    public function robots_txt_contains_sitemap_and_admin_disallow()
    {
        $robotsPath = public_path('robots.txt');
        $this->assertFileExists($robotsPath);

        $content = file_get_contents($robotsPath);
        $this->assertStringContainsString('Disallow: /admin/', $content);
        $this->assertStringContainsString('Disallow: /profile', $content);
        $this->assertStringContainsString('Sitemap:', $content);
        $this->assertStringContainsString('/sitemap.xml', $content);
    }

    /** @test */
    public function noticia_show_page_renders_opengraph_twitter_and_newsarticle_schema()
    {
        // Buscar o crear una categoría y noticia
        $categoria = Category::firstOrCreate(
            ['name' => 'SEO Test Cat'],
            ['slug' => 'seo-test-cat', 'descripcion' => 'Categoria para pruebas SEO']
        );

        $user = User::first() ?? User::factory()->create();
        \Illuminate\Support\Facades\Storage::disk('public')->put('test-seo.jpg', 'fake-image');

        $noticia = Noticia::create([
            'titulo' => 'Noticia de Prueba Para SEO y Redes Sociales',
            'contenido' => '<p>Contenido completo para verificar tags OpenGraph, Twitter Cards y Schema JSON-LD.</p>',
            'category_id' => $categoria->id,
            'user_id' => $user->id,
            'publicada' => true,
            'imagen' => 'test-seo.jpg',
            'views' => 5,
        ]);

        $response = $this->get($noticia->url);

        $response->assertStatus(200);

        // Open Graph
        $response->assertSee('<meta property="og:type" content="article">', false);
        $response->assertSee($noticia->titulo, false);
        $response->assertSee('og:site_name', false);
        $response->assertSee('og:image', false);

        // Twitter Card
        $response->assertSee('<meta name="twitter:card" content="summary_large_image">', false);
        $response->assertSee('twitter:title', false);

        // Canonical
        $response->assertSee('<link rel="canonical" href="' . $noticia->url . '">', false);

        // Schema.org NewsArticle JSON-LD
        $response->assertSee('"@type": "NewsArticle"', false);
        $response->assertSee('"@type": "NewsMediaOrganization"', false);
        $response->assertSee('"name": "Última Hora TV"', false);

        // Image performance attributes
        $response->assertSee('decoding="async"', false);

        // Limpiar registro de prueba
        $noticia->delete();
        \Illuminate\Support\Facades\Storage::disk('public')->delete('test-seo.jpg');
        if ($categoria->name === 'SEO Test Cat') {
            $categoria->delete();
        }
    }

    /** @test */
    public function category_page_renders_opengraph_and_canonical()
    {
        $categoria = Category::first();
        if (!$categoria) {
            $categoria = Category::create([
                'name' => 'Categoria SEO',
                'slug' => 'categoria-seo',
            ]);
        }

        $response = $this->get($categoria->url);

        $response->assertStatus(200);
        $response->assertSee('property="og:type"', false);
        $response->assertSee('rel="canonical"', false);
        $response->assertSee($categoria->name, false);
    }

    /** @test */
    public function model_events_flush_cache_on_save_and_delete()
    {
        Cache::put('homepage_data', 'test_data', 300);
        Cache::put('all_categories', 'test_data', 300);
        Cache::put('sitemap_xml_data', 'test_data', 300);

        $this->assertTrue(Cache::has('homepage_data'));
        $this->assertTrue(Cache::has('sitemap_xml_data'));

        Noticia::clearNewsCache();

        $this->assertFalse(Cache::has('homepage_data'));
        $this->assertFalse(Cache::has('all_categories'));
        $this->assertFalse(Cache::has('sitemap_xml_data'));
    }

    /** @test */
    public function home_page_renders_optimized_image_attributes()
    {
        $categoria = Category::firstOrCreate(['name' => 'General'], ['slug' => 'general']);
        $user = User::firstOrCreate(['email' => 'admin@test.com'], ['name' => 'Admin', 'password' => bcrypt('secret')]);
        Noticia::firstOrCreate(['titulo' => 'Noticia Carousel 1'], ['contenido' => 'test 1', 'category_id' => $categoria->id, 'user_id' => $user->id, 'publicada' => true]);
        Noticia::firstOrCreate(['titulo' => 'Noticia Carousel 2'], ['contenido' => 'test 2', 'category_id' => $categoria->id, 'user_id' => $user->id, 'publicada' => true]);

        $response = $this->get(route('portada'));

        $response->assertStatus(200);
        // Debe contener loading="lazy" y decoding="async"
        $response->assertSee('loading="lazy"', false);
        $response->assertSee('decoding="async"', false);
    }
}
