<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Category;
use App\Models\User;
use App\Models\Noticia;

class ImageOptimizationAttributesTest extends TestCase
{
    /** @test */
    public function portada_images_include_lazy_and_decoding_async()
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        $response->assertSee('loading="lazy"', false);
        $response->assertSee('decoding="async"', false);
    }

    /** @test */
    public function main_layout_renders_optimized_logo_and_pwa_image_tags()
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        // Sidebar logo and PWA icon have lazy + decoding async
        $response->assertSee('icon-192x192.png" alt="UHTV App" class="w-12 h-12 rounded-xl shadow-md border border-white/20 flex-shrink-0" loading="lazy" decoding="async"', false);
    }
}
