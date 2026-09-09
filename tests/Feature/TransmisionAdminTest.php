<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Transmision;

class TransmisionAdminTest extends TestCase
{
    protected function getAdminUser()
    {
        return User::where('email', 'bryan.costas@ultimahoratv.com')->first()
            ?? User::where('role', 'admin')->first();
    }

    /** @test */
    public function guests_cannot_access_admin_transmisiones()
    {
        $response = $this->get(route('admin.transmisiones.index'));
        $response->assertRedirect(route('admin.login'));
    }

    /** @test */
    public function admin_can_view_transmisiones_index()
    {
        $admin = $this->getAdminUser();
        $this->assertNotNull($admin, 'Admin user should exist');

        $response = $this->actingAs($admin)->get(route('admin.transmisiones.index'));
        $response->assertStatus(200);
        $response->assertSee('Transmisiones En Vivo, Podcasts y Clips');
        $response->assertSee('En Vivo Ahora');
    }

    /** @test */
    public function admin_can_create_youtube_transmision_with_auto_embed()
    {
        $admin = $this->getAdminUser();

        $data = [
            'titulo' => 'Transmisión de Prueba YouTube',
            'tipo' => 'en_vivo',
            'plataforma' => 'youtube',
            'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'descripcion' => 'Descripción de prueba para streaming',
            'en_vivo' => '1',
            'activo' => '1',
        ];

        $response = $this->actingAs($admin)->post(route('admin.transmisiones.store'), $data);
        $response->assertRedirect(route('admin.transmisiones.index'));

        $transmision = Transmision::where('titulo', 'Transmisión de Prueba YouTube')->first();
        $this->assertNotNull($transmision);
        $this->assertTrue($transmision->en_vivo);
        $this->assertStringContainsString('youtube-nocookie.com/embed/dQw4w9WgXcQ', $transmision->embed_url);
        $this->assertStringContainsString('img.youtube.com/vi/dQw4w9WgXcQ', $transmision->thumbnail_url);

        // Limpieza del registro de prueba
        $transmision->delete();
    }

    /** @test */
    public function admin_can_toggle_live_status()
    {
        $admin = $this->getAdminUser();

        $transmision = Transmision::create([
            'titulo' => 'Test Toggle Live',
            'tipo' => 'en_vivo',
            'plataforma' => 'youtube',
            'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'en_vivo' => false,
            'activo' => true,
        ]);

        $this->assertFalse($transmision->en_vivo);

        // Toggle to Live
        $response = $this->actingAs($admin)->post(route('admin.transmisiones.toggle-live', $transmision->id));
        $transmision->refresh();
        $this->assertTrue($transmision->en_vivo);

        // Toggle back to not Live
        $response = $this->actingAs($admin)->post(route('admin.transmisiones.toggle-live', $transmision->id));
        $transmision->refresh();
        $this->assertFalse($transmision->en_vivo);

        $transmision->delete();
    }

    /** @test */
    public function admin_can_create_podcast_or_clip()
    {
        $admin = $this->getAdminUser();

        $data = [
            'titulo' => 'Podcast Episodio 1 - Bolivia Política',
            'tipo' => 'podcast',
            'plataforma' => 'youtube',
            'url' => 'https://youtu.be/dQw4w9WgXcQ',
            'duracion' => '42:15',
            'activo' => '1',
        ];

        $response = $this->actingAs($admin)->post(route('admin.transmisiones.store'), $data);
        $response->assertRedirect(route('admin.transmisiones.index'));

        $podcast = Transmision::where('titulo', 'Podcast Episodio 1 - Bolivia Política')->first();
        $this->assertNotNull($podcast);
        $this->assertEquals('podcast', $podcast->tipo);
        $this->assertEquals('42:15', $podcast->duracion);

        $podcast->delete();
    }

    /** @test */
    public function admin_can_create_facebook_and_tiktok_transmision()
    {
        $admin = $this->getAdminUser();

        // Facebook
        $fbData = [
            'titulo' => 'Transmisión Facebook Live',
            'tipo' => 'en_vivo',
            'plataforma' => 'facebook',
            'url' => 'https://www.facebook.com/ultimahoratv/videos/1234567890/',
            'activo' => '1',
        ];
        $fbResp = $this->actingAs($admin)->post(route('admin.transmisiones.store'), $fbData);
        $fbResp->assertRedirect(route('admin.transmisiones.index'));
        $fbTrans = Transmision::where('titulo', 'Transmisión Facebook Live')->first();
        $this->assertNotNull($fbTrans);
        $this->assertStringContainsString('facebook.com/plugins/video.php', $fbTrans->embed_url);
        $fbTrans->delete();

        // TikTok
        $ttData = [
            'titulo' => 'Clip Noticioso TikTok',
            'tipo' => 'clip',
            'plataforma' => 'tiktok',
            'url' => 'https://www.tiktok.com/@uhtv/video/7123456789012345678',
            'activo' => '1',
        ];
        $ttResp = $this->actingAs($admin)->post(route('admin.transmisiones.store'), $ttData);
        $ttResp->assertRedirect(route('admin.transmisiones.index'));
        $ttTrans = Transmision::where('titulo', 'Clip Noticioso TikTok')->first();
        $this->assertNotNull($ttTrans);
        $this->assertStringContainsString('tiktok.com/embed/v2/7123456789012345678', $ttTrans->embed_url);
        $ttTrans->delete();
    }
}
