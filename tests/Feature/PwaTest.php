<?php

namespace Tests\Feature;

use Tests\TestCase;

class PwaTest extends TestCase
{
    /** @test */
    public function manifest_json_returns_200_and_valid_pwa_configuration()
    {
        $response = $this->get('/manifest.json');

        $response->assertStatus(200);

        $json = json_decode($response->getContent(), true);
        $this->assertIsArray($json);
        $this->assertEquals('Última Hora TV', $json['name']);
        $this->assertEquals('UHTV', $json['short_name']);
        $this->assertEquals('standalone', $json['display']);
        $this->assertEquals('/?source=pwa', $json['start_url']);
        $this->assertEquals('#4f46e5', $json['theme_color']);
        $this->assertNotEmpty($json['icons']);
        $this->assertNotEmpty($json['shortcuts']);
    }

    /** @test */
    public function sw_js_returns_200_and_contains_cache_strategy()
    {
        $response = $this->get('/sw.js');

        $response->assertStatus(200);
        $content = $response->getContent();
        $this->assertStringContainsString('uhtv-cache-v1', $content);
        $this->assertStringContainsString('/offline.html', $content);
        $this->assertStringContainsString('addEventListener(\'install\'', $content);
        $this->assertStringContainsString('addEventListener(\'fetch\'', $content);
    }

    /** @test */
    public function offline_html_fallback_is_accessible()
    {
        $response = $this->get('/offline.html');

        $response->assertStatus(200);
        $response->assertSee('Sin Conexión a Internet');
        $response->assertSee('Reintentar Conexión');
        $response->assertSee('ÚLTIMA HORA TV');
    }

    /** @test */
    public function main_layout_renders_pwa_meta_tags_and_service_worker_registration()
    {
        $response = $this->get(route('portada'));

        $response->assertStatus(200);

        // Manifest & iOS meta tags
        $response->assertSee('<link rel="manifest" href="/manifest.json">', false);
        $response->assertSee('<meta name="apple-mobile-web-app-capable" content="yes">', false);
        $response->assertSee('apple-touch-icon', false);

        // PWA Install UI banner & Service Worker script
        $response->assertSee('id="pwa-install-banner"', false);
        $response->assertSee('navigator.serviceWorker.register(\'/sw.js\')', false);
        $response->assertSee('beforeinstallprompt', false);
    }

    /** @test */
    public function pwa_icons_exist_on_filesystem()
    {
        $this->assertFileExists(public_path('images/icons/icon-192x192.png'));
        $this->assertFileExists(public_path('images/icons/icon-512x512.png'));
        $this->assertFileExists(public_path('images/icons/icon-maskable-512x512.png'));
        $this->assertFileExists(public_path('images/icons/apple-touch-icon.png'));
        $this->assertFileExists(public_path('images/icons/icon.svg'));
    }
}
