<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Transmision;

class HeaderBcbExchangeTest extends TestCase
{
    /** @test */
    public function topbar_date_and_social_div_is_removed()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        // El antiguo topbar no debe existir
        $response->assertDontSee('id="topbar-date-text"', false);
        $response->assertDontSee('Topbar estilo El Deber', false);
    }

    /** @test */
    public function bcb_currency_exchange_widget_is_present_in_header_right()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        // Debe contener el widget de tipo de cambio BCB
        $response->assertSee('id="widget-tipo-cambio-bcb"', false);
        $response->assertSee('id="bcb-ticker-display"', false);
        $response->assertSee('BCB', false);
        $response->assertSee('USD', false);
        $response->assertSee('11.00', false);
        $response->assertSee('11.10', false);
        $response->assertSee('EUR', false);
        $response->assertSee('11.85', false);
        $response->assertSee('UFV', false);
        $response->assertSee('2.54', false);
        $response->assertSee('Cotización Oficial', false);
        $response->assertSee('Banco Central de Bolivia', false);
    }

    /** @test */
    public function bcb_widget_displays_alongside_live_button_when_active()
    {
        // Crear transmisión activa
        $transmision = Transmision::create([
            'titulo' => 'Transmisión BCB Header Test',
            'plataforma' => 'youtube',
            'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'embed_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            'activa' => true,
            'en_vivo' => true,
            'tipo' => 'en_vivo',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        // Ambos elementos deben estar presentes en el header
        $response->assertSee('id="widget-tipo-cambio-bcb"', false);
        $response->assertSee('data-open-live-modal', false);
        $response->assertSee('En Vivo', false);

        // Limpiar registro de prueba
        $transmision->delete();
    }
}
