<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Category;
use App\Models\Noticia;

class UrlStructureTest extends TestCase
{
    /**
     * Verificar que la URL de una categoría se genera con su slug /categoria
     */
    public function test_category_url_generation()
    {
        $category = Category::whereNotNull('slug')->first();
        $this->assertNotNull($category, 'Debe existir al menos una categoría con slug');

        $expectedUrl = url('/' . $category->slug);
        $this->assertEquals($expectedUrl, $category->url);
    }

    /**
     * Verificar que la URL de una noticia se genera con formato /{categoria}/{slug}_{id}
     */
    public function test_news_url_generation()
    {
        $noticia = Noticia::with('category')->where('publicada', true)->first();
        $this->assertNotNull($noticia, 'Debe existir al menos una noticia publicada');

        $categorySlug = $noticia->category ? $noticia->category->slug : 'general';
        $expectedUrl = url('/' . $categorySlug . '/' . $noticia->slug . '_' . $noticia->id);

        $this->assertEquals($expectedUrl, $noticia->url);
    }

    /**
     * Verificar que la página de categoría responde 200 con la URL amigable
     */
    public function test_category_page_accessible_via_slug()
    {
        $category = Category::whereNotNull('slug')->first();
        $this->assertNotNull($category);

        $response = $this->get('/' . $category->slug);

        $response->assertStatus(200);
        $response->assertViewIs('categoria.noticias');
        $response->assertSee($category->name);
    }

    /**
     * Verificar que el detalle de la noticia responde 200 con la URL amigable
     */
    public function test_news_page_accessible_via_seo_url()
    {
        $noticia = Noticia::with('category')->where('publicada', true)->first();
        $this->assertNotNull($noticia);

        $response = $this->get($noticia->url);

        $response->assertStatus(200);
        $response->assertViewIs('show');
        $response->assertSee($noticia->titulo);
    }

    /**
     * Verificar que la ruta antigua /noticia/{id} redirige con 301 a la nueva URL
     */
    public function test_legacy_news_redirects_301()
    {
        $noticia = Noticia::with('category')->where('publicada', true)->first();
        $this->assertNotNull($noticia);

        $response = $this->get('/noticia/' . $noticia->id);

        $response->assertStatus(301);
        $response->assertRedirect($noticia->url);
    }

    /**
     * Verificar que la ruta antigua /categoria/{id} redirige con 301 a /{categoria}
     */
    public function test_legacy_category_redirects_301()
    {
        $category = Category::whereNotNull('slug')->first();
        $this->assertNotNull($category);

        $response = $this->get('/categoria/' . $category->id);

        $response->assertStatus(301);
        $response->assertRedirect($category->url);
    }

    /**
     * Verificar redirección canónica 301 si el slug o categoría no coinciden pero el ID existe
     */
    public function test_canonical_news_redirect_on_mismatched_slug()
    {
        $noticia = Noticia::with('category')->where('publicada', true)->first();
        $this->assertNotNull($noticia);

        // URL con categoría inventada y slug alterado pero mismo ID
        $response = $this->get('/otra-categoria/slug-antiguo-o-modificado_' . $noticia->id);

        $response->assertStatus(301);
        $response->assertRedirect($noticia->url);
    }

    /**
     * Verificar que una categoría inexistente devuelva 404
     */
    public function test_non_existent_category_returns_404()
    {
        $response = $this->get('/seccion-totalmente-inexistente-xyz-999');

        $response->assertStatus(404);
    }
}
