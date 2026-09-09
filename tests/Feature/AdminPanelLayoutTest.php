<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;

class AdminPanelLayoutTest extends TestCase
{
    protected function getAdminUser()
    {
        return User::where('email', 'bryan.costas@ultimahoratv.com')->first()
            ?? User::where('role', 'admin')->first();
    }

    /** @test */
    public function admin_dashboard_renders_with_sticky_sidebar_and_profile()
    {
        $admin = $this->getAdminUser();
        $this->assertNotNull($admin, 'Admin user should exist in database');

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('admin-sidebar');
        $response->assertSee('UHTV Admin');
        $response->assertSee('Mi Perfil');
        $response->assertSee('Administrar Mi Perfil');
        $response->assertSee($admin->name);
        $response->assertSee('Notificaciones');
    }

    /** @test */
    public function admin_profile_page_loads_successfully()
    {
        $admin = $this->getAdminUser();
        $response = $this->actingAs($admin)->get(route('admin.profile.index'));

        $response->assertStatus(200);
        $response->assertSee('Mi Perfil');
        $response->assertSee($admin->name);
    }

    /** @test */
    public function admin_sections_render_with_admin_layout()
    {
        $admin = $this->getAdminUser();

        // Noticias
        $noticiasResp = $this->actingAs($admin)->get(route('admin.noticias.index'));
        $noticiasResp->assertStatus(200);
        $noticiasResp->assertSee('admin-sidebar');

        // Categorías
        $categoriasResp = $this->actingAs($admin)->get(route('admin.categorias.index'));
        $categoriasResp->assertStatus(200);
        $categoriasResp->assertSee('admin-sidebar');

        // Banners
        $bannersResp = $this->actingAs($admin)->get(route('admin.banners.index'));
        $bannersResp->assertStatus(200);
        $bannersResp->assertSee('admin-sidebar');
    }

    /** @test */
    public function notifications_composer_provides_valid_data()
    {
        $admin = $this->getAdminUser();
        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertViewHas('adminNotifications');
        $response->assertViewHas('headerTotalNews');
        $response->assertViewHas('headerPublishedNews');
    }
}
