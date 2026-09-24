<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Transmision;

class TransmisionFrontendTest extends TestCase
{
    /** @test */
    public function public_user_can_access_en_vivo_page()
    {
        $response = $this->get(route('transmisiones.en-vivo'));

        $response->assertStatus(200);
        $response->assertSee('UHTV');
        $response->assertSee('Play');
        $response->assertSee('Todos los Videos');
    }

    /** @test */
    public function home_page_renders_videos_uhtv_and_no_ver_canal_de_transmisiones()
    {
        $response = $this->get(route('portada'));

        $response->assertStatus(200);
        $response->assertSee('Videos UHTV');
        $response->assertDontSee('Ver canal de transmisiones');
        $response->assertSee('liveStreamModal');
    }

    /** @test */
    public function live_alert_and_header_button_show_only_when_live_stream_is_active()
    {
        // 1. Cuando hay una transmisión activa
        $liveStream = Transmision::create([
            'titulo' => 'Transmisión Especial Alerta Activa',
            'tipo' => 'en_vivo',
            'plataforma' => 'youtube',
            'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'en_vivo' => true,
            'activo' => true,
        ]);

        $responseLive = $this->get(route('portada'));
        $responseLive->assertStatus(200);
        $responseLive->assertSee('liveStreamAlert');
        $responseLive->assertSee('EN VIVO AHORA');
        $responseLive->assertSee('Transmisión Especial Alerta Activa');

        // 2. Cuando no está en vivo
        $liveStream->update(['en_vivo' => false]);
        $responseOffAir = $this->get(route('portada'));
        $responseOffAir->assertStatus(200);
        $responseOffAir->assertDontSee('liveStreamAlert');

        $liveStream->delete();
    }

    /** @test */
    public function home_page_renders_multimedia_strip_below_category_navigation()
    {
        $podcast = Transmision::create([
            'titulo' => 'Podcast para franja de portada',
            'descripcion' => 'Resumen visible del podcast de prueba.',
            'tipo' => 'podcast',
            'plataforma' => 'youtube',
            'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'activo' => true,
            'destacado' => true,
        ]);

        $clip = Transmision::create([
            'titulo' => 'Clip para franja de portada',
            'descripcion' => 'Resumen visible del clip de prueba.',
            'tipo' => 'clip',
            'plataforma' => 'youtube',
            'url' => 'https://www.youtube.com/watch?v=M7lc1UVf-VE',
            'activo' => true,
        ]);

        $programa = Transmision::create([
            'titulo' => 'Programa grabado para franja de portada',
            'tipo' => 'programa',
            'plataforma' => 'youtube',
            'url' => 'https://www.youtube.com/watch?v=aqz-KE-bpKQ',
            'activo' => true,
        ]);

        $enVivo = Transmision::create([
            'titulo' => 'Transmisión En Vivo que no debe salir en franja',
            'tipo' => 'en_vivo',
            'plataforma' => 'youtube',
            'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'en_vivo' => true,
            'activo' => true,
        ]);

        $inactive = Transmision::create([
            'titulo' => 'Clip oculto de la franja',
            'tipo' => 'clip',
            'plataforma' => 'youtube',
            'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'activo' => false,
        ]);

        try {
            $response = $this->get(route('portada'));
            $response->assertStatus(200);

            $html = $response->getContent();
            $navigationPosition = strpos($html, 'id="category-navigation"');
            $stripPosition = strpos($html, 'id="multimedia-strip"');
            $mainPosition = strpos($html, '<main');

            $this->assertNotFalse($navigationPosition);
            $this->assertNotFalse($stripPosition);
            $this->assertNotFalse($mainPosition);
            $this->assertTrue(
                $navigationPosition < $stripPosition && $stripPosition < $mainPosition,
                'La franja multimedia debe quedar entre el navbar y el contenido principal.'
            );

            $stripHtml = substr($html, $stripPosition, $mainPosition - $stripPosition);
            $this->assertStringContainsString('data-stream-title="Podcast para franja de portada"', $stripHtml);
            $this->assertStringContainsString('data-stream-title="Clip para franja de portada"', $stripHtml);
            $this->assertStringContainsString('data-stream-title="Programa grabado para franja de portada"', $stripHtml);
            $this->assertStringNotContainsString('Transmisión En Vivo que no debe salir en franja', $stripHtml);
            $this->assertStringNotContainsString('Clip oculto de la franja', $stripHtml);

            $podcastPosition = strpos($stripHtml, 'Podcast para franja de portada');
            $clipPosition = strpos($stripHtml, 'Clip para franja de portada');
            $this->assertTrue($podcastPosition < $clipPosition, 'El contenido priorizado debe aparecer primero.');
        } finally {
            Transmision::whereIn('id', [
                $podcast->id,
                $clip->id,
                $programa->id,
                $enVivo->id,
                $inactive->id,
            ])->delete();
        }
    }

    /** @test */
    public function public_user_can_get_transmision_json()
    {
        $transmision = Transmision::create([
            'titulo' => 'JSON Stream Test',
            'tipo' => 'en_vivo',
            'plataforma' => 'youtube',
            'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'activo' => true,
        ]);

        $response = $this->get(route('transmisiones.json', $transmision->id));

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $transmision->id,
            'titulo' => 'JSON Stream Test',
            'plataforma' => 'youtube',
        ]);

        $transmision->delete();
    }
}
