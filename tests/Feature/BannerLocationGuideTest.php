<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Banner;
use App\Models\User;
use App\Services\BannerLocationService;

class BannerLocationGuideTest extends TestCase
{
    protected function getAdminUser()
    {
        $user = User::where('email', 'bryan.costas@ultimahoratv.com')->first()
            ?? User::where('role', 'admin')->first();

        if (!$user) {
            $user = User::factory()->create([
                'email' => 'admin_banners_' . uniqid() . '@example.com',
                'role' => 'admin',
                'email_verified_at' => now(),
            ]);
        }

        return $user;
    }

    /** @test */
    public function every_location_exposes_the_metadata_the_admin_needs()
    {
        $all = BannerLocationService::all();

        $this->assertCount(9, $all, 'Deben existir las 9 ubicaciones del sitio');

        foreach ($all as $key => $def) {
            $this->assertArrayHasKey('label', $def);
            $this->assertArrayHasKey('select_label', $def);
            $this->assertArrayHasKey('description', $def);
            $this->assertArrayHasKey('dimensions', $def);
            $this->assertArrayHasKey('rendered_in', $def);
            $this->assertArrayHasKey('pages', $def);
            $this->assertArrayHasKey('mockup', $def);
            $this->assertNotEmpty($def['mockup'], "La ubicacion $key debe traer su maqueta");
            $this->assertGreaterThan(0, BannerLocationService::mockupRatio($key));
        }
    }

    /** @test */
    public function mockup_declares_a_slot_for_every_banner_row()
    {
        foreach (BannerLocationService::all() as $key => $def) {
            $collect = function (array $rows) use (&$collect, $key) {
                foreach ($rows as $row) {
                    if (($row['type'] ?? null) === 'banner' || ($row['type'] ?? null) === 'overlay') {
                        $this->assertSame(
                            $key,
                            $row['key'],
                            "La maqueta de $key debe marcar su propio hueco"
                        );
                    }
                    if (($row['type'] ?? null) === 'split') {
                        $collect($row['main']);
                        $collect($row['side']);
                    }
                }
            };

            $collect($def['mockup']);
        }
    }

    /** @test */
    public function admin_banners_index_renders_a_mockup_per_location()
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->get(route('admin.banners.index'));
        $response->assertStatus(200);

        foreach (BannerLocationService::keys() as $key) {
            $response->assertSee('uhtv-mm', false);
            $response->assertSee($key);
        }

        // Cada maqueta resalta su propio hueco. portada_middle aparece dos veces
        // porque en la portada ese bloque se dibuja dos veces.
        $countActiveSlots = function (array $rows) use (&$countActiveSlots) {
            $total = 0;
            foreach ($rows as $row) {
                if (in_array($row['type'] ?? null, ['banner', 'overlay'], true)) {
                    $total++;
                }
                if (($row['type'] ?? null) === 'split') {
                    $total += $countActiveSlots($row['main']);
                    $total += $countActiveSlots($row['side']);
                }
            }
            return $total;
        };

        $expected = 0;
        foreach (BannerLocationService::all() as $def) {
            $expected += $countActiveSlots($def['mockup']);
        }

        $this->assertSame(
            $expected,
            substr_count($response->getContent(), '<span class="uhtv-mm-slot-tag">'),
            'Cada hueco de cada maqueta debe quedar resaltado'
        );
    }

    /** @test */
    public function admin_banners_create_shows_a_live_preview_per_location()
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->get(route('admin.banners.create'));
        $response->assertStatus(200);

        $response->assertSee('Dónde aparecerá este banner');
        $response->assertSee('uhtv-loc-preview', false);

        foreach (BannerLocationService::keys() as $key) {
            $response->assertSee('data-location="' . $key . '"', false);
        }
    }

    /** @test */
    public function admin_banners_edit_shows_the_current_image_in_the_mockup()
    {
        $admin = $this->getAdminUser();

        $banner = Banner::create([
            'title' => 'Banner de prueba de ubicacion',
            'image_path' => 'images/banners/test.png',
            'location' => 'footer',
            'position' => 0,
            'active' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.banners.edit', $banner->id));
        $response->assertStatus(200);

        $response->assertSee('Dónde aparecerá este banner');
        $response->assertSee(asset('images/banners/test.png'), false);
        $this->assertMatchesRegularExpression(
            '/data-location="footer"\s+style=""/',
            $response->getContent(),
            'La ubicacion actual debe quedar preseleccionada en la vista previa'
        );
        $this->assertMatchesRegularExpression(
            '/data-location="sidebar"\s+style="display:none"/',
            $response->getContent(),
            'Las demas ubicaciones deben quedar ocultas'
        );

        $banner->delete();
    }

    /** @test */
    public function every_highlighted_slot_declares_its_aspect_ratio()
    {
        $admin = $this->getAdminUser();

        $html = $this->actingAs($admin)->get(route('admin.banners.index'))->getContent();

        preg_match_all('/<div class="uhtv-mm-slot uhtv-mm-slot-active" style="([^"]*)">/', $html, $m);

        $this->assertNotEmpty($m[1], 'Debe haber huecos resaltados con estilo propio');
        foreach ($m[1] as $style) {
            $this->assertMatchesRegularExpression(
                '/aspect-ratio:\s*[\d.]+/',
                $style,
                'Sin aspect-ratio el hueco colapsaria a altura cero'
            );
        }
    }

    /** @test */
    public function mockup_stylesheet_is_emitted_only_once()
    {
        $admin = $this->getAdminUser();

        $html = $this->actingAs($admin)->get(route('admin.banners.index'))->getContent();

        $this->assertSame(
            1,
            substr_count($html, '.uhtv-mm-slot-active {'),
            'La hoja de estilos del mockup debe incluirse una sola vez'
        );
    }

    /** @test */
    public function popup_location_renders_as_an_overlay_not_inline()
    {
        $admin = $this->getAdminUser();

        $html = $this->actingAs($admin)
            ->get(route('admin.banners.create'))
            ->getContent();

        preg_match('/data-location="popup".*?uhtv-mm-overlay/s', $html, $m);
        $this->assertNotEmpty($m, 'El popup debe dibujarse como overlay sobre la pagina');
        $this->assertStringContainsString('uhtv-mm-overlay-x', $html, 'El overlay debe traer la X de cierre');
    }

    /** @test */
    public function sidebar_location_is_drawn_inside_the_right_column()
    {
        $admin = $this->getAdminUser();

        $html = $this->actingAs($admin)
            ->get(route('admin.banners.create'))
            ->getContent();

        $this->assertMatchesRegularExpression(
            '/uhtv-mm-col uhtv-mm-col-side.*?uhtv-mm-slot uhtv-mm-slot-active/s',
            $html,
            'El banner de sidebar debe quedar dentro de la columna lateral'
        );
    }

    /** @test */
    public function unknown_locations_are_rejected()
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->post(route('admin.banners.store'), [
            'title' => 'Ubicacion invalida',
            'location' => 'no_existe',
            'position' => 0,
        ]);

        $response->assertSessionHasErrors('location');
    }
}
