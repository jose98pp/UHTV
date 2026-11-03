<?php

namespace Tests\Feature;

use App\Models\Transmision;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class HerenciaAdquiridaTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_loads_herencia_invisible_page_successfully()
    {
        $response = $this->get(route('herencia.index'));

        $response->assertStatus(200);
        $response->assertSee('Herencia');
        $response->assertSee('Invisible');
        $response->assertSee('Cinema Player');
        $response->assertSee('Catálogo Completo');
        $response->assertSee('Todos los Contenidos');
    }

    /** @test */
    public function it_redirects_legacy_herencia_slugs_to_herencia_invisible()
    {
        $responseHerencia = $this->get('/herencia');
        $responseHerencia->assertStatus(301);
        $responseHerencia->assertRedirect('/herencia-invisible');

        $responseAdquirida = $this->get('/herencia-adquirida');
        $responseAdquirida->assertStatus(301);
        $responseAdquirida->assertRedirect('/herencia-invisible');
    }

    /** @test */
    public function it_displays_registered_transmisiones_in_herencia_invisible()
    {
        $podcast = Transmision::create([
            'titulo' => 'Episodio Especial Podcast Herencia',
            'descripcion' => 'Debate exclusivo sobre economia boliviana',
            'tipo' => 'podcast',
            'plataforma' => 'youtube',
            'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'activo' => true,
        ]);

        $response = $this->get(route('herencia.index', ['tipo' => 'podcast']));

        $response->assertStatus(200);
        $response->assertSee('Episodio Especial Podcast Herencia');
    }

    /** @test */
    public function it_filters_herencia_contents_by_type()
    {
        $clip = Transmision::create([
            'titulo' => 'Momento Clave Declaraciones',
            'tipo' => 'clip',
            'plataforma' => 'youtube',
            'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'activo' => true,
        ]);

        $podcast = Transmision::create([
            'titulo' => 'Podcast Charla Profunda',
            'tipo' => 'podcast',
            'plataforma' => 'youtube',
            'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'activo' => true,
        ]);

        $responseClip = $this->get(route('herencia.index', ['tipo' => 'clip']));
        $responseClip->assertStatus(200);
        $responseClip->assertSee('Momento Clave Declaraciones');

        $responsePodcast = $this->get(route('herencia.index', ['tipo' => 'podcast']));
        $responsePodcast->assertStatus(200);
        $responsePodcast->assertSee('Podcast Charla Profunda');
    }

    /** @test */
    public function it_filters_herencia_contents_by_search_query()
    {
        $itemA = Transmision::create([
            'titulo' => 'Exclusiva Censo y Demografia Bolivia',
            'descripcion' => 'Analisis detallado',
            'tipo' => 'programa',
            'plataforma' => 'youtube',
            'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'activo' => true,
        ]);

        $itemB = Transmision::create([
            'titulo' => 'Deportes Fin de Semana',
            'descripcion' => 'Resumen de futbol',
            'tipo' => 'clip',
            'plataforma' => 'youtube',
            'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'activo' => true,
        ]);

        $response = $this->get(route('herencia.index', ['q' => 'Censo']));

        $response->assertStatus(200);
        $response->assertSee('Exclusiva Censo y Demografia Bolivia');
        
        $contents = $response->viewData('allContents');
        $this->assertTrue($contents->contains('id', $itemA->id));
        $this->assertFalse($contents->contains('id', $itemB->id));
    }

    /** @test */
    public function header_and_multimedia_strip_link_to_herencia_invisible()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee(route('herencia.index'));
        $response->assertSee('Herencia Invisible');
        
        // El botón del navbar fue removido según solicitado
        $response->assertDontSee('Herencia Adquirida');
    }
}
