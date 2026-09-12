<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Noticia;
use App\Models\Category;
use App\Models\User;

class AdminImprovementsTest extends TestCase
{
    protected function getAdminUser()
    {
        return User::firstOrCreate(
            ['email' => 'bryan.costas@ultimahoratv.com'],
            [
                'name' => 'Bryan Costas',
                'password' => bcrypt('password123'),
                'role' => 'admin',
            ]
        );
    }

    /** @test */
    public function admin_can_sort_noticias_by_views_and_date()
    {
        $admin = $this->getAdminUser();

        $responseViews = $this->actingAs($admin)->get(route('admin.noticias.index', ['sort' => 'views_desc']));
        $responseViews->assertStatus(200);
        $responseViews->assertSee('Ordenar por');
        $responseViews->assertSee('Más vistas (Popularidad)');

        $responseOldest = $this->actingAs($admin)->get(route('admin.noticias.index', ['sort' => 'oldest']));
        $responseOldest->assertStatus(200);

        $responseTitle = $this->actingAs($admin)->get(route('admin.noticias.index', ['sort' => 'title_asc']));
        $responseTitle->assertStatus(200);
    }

    /** @test */
    public function admin_can_toggle_noticia_status_quickly()
    {
        $admin = $this->getAdminUser();
        $categoria = Category::first() ?? Category::create(['name' => 'General Cat', 'slug' => 'general-cat']);

        $noticia = Noticia::create([
            'titulo' => 'Noticia para Test Toggle Status',
            'contenido' => '<p>Contenido para toggle de estado rápido.</p>',
            'category_id' => $categoria->id,
            'user_id' => $admin->id,
            'publicada' => false,
        ]);

        $this->assertFalse((bool)$noticia->publicada);

        // 1. Alternar a publicada (petición JSON)
        $responseJson = $this->actingAs($admin)->postJson(route('admin.noticias.toggle-status', $noticia->id));
        $responseJson->assertStatus(200);
        $responseJson->assertJson(['success' => true, 'publicada' => true]);

        $noticia->refresh();
        $this->assertTrue((bool)$noticia->publicada);

        // 2. Alternar nuevamente a borrador
        $responseJson2 = $this->actingAs($admin)->postJson(route('admin.noticias.toggle-status', $noticia->id));
        $responseJson2->assertStatus(200);
        $responseJson2->assertJson(['success' => true, 'publicada' => false]);

        $noticia->refresh();
        $this->assertFalse((bool)$noticia->publicada);

        $noticia->delete();
    }

    /** @test */
    public function admin_can_execute_bulk_actions_publish_unpublish_delete()
    {
        $admin = $this->getAdminUser();
        $categoria = Category::first() ?? Category::create(['name' => 'General Cat', 'slug' => 'general-cat']);

        $n1 = Noticia::create([
            'titulo' => 'Noticia Lote 1',
            'contenido' => '<p>Contenido lote 1</p>',
            'category_id' => $categoria->id,
            'user_id' => $admin->id,
            'publicada' => false,
        ]);

        $n2 = Noticia::create([
            'titulo' => 'Noticia Lote 2',
            'contenido' => '<p>Contenido lote 2</p>',
            'category_id' => $categoria->id,
            'user_id' => $admin->id,
            'publicada' => false,
        ]);

        $ids = [$n1->id, $n2->id];

        // 1. Publicar en lote
        $responsePub = $this->actingAs($admin)->postJson(route('admin.noticias.bulk-action'), [
            'action' => 'publish',
            'ids' => $ids
        ]);
        $responsePub->assertStatus(200);
        $responsePub->assertJson(['success' => true, 'action' => 'publish', 'count' => 2]);

        $this->assertTrue((bool)Noticia::find($n1->id)->publicada);
        $this->assertTrue((bool)Noticia::find($n2->id)->publicada);

        // 2. Despublicar en lote
        $responseUnpub = $this->actingAs($admin)->postJson(route('admin.noticias.bulk-action'), [
            'action' => 'unpublish',
            'ids' => $ids
        ]);
        $responseUnpub->assertStatus(200);
        $responseUnpub->assertJson(['success' => true, 'action' => 'unpublish', 'count' => 2]);

        $this->assertFalse((bool)Noticia::find($n1->id)->publicada);
        $this->assertFalse((bool)Noticia::find($n2->id)->publicada);

        // 3. Eliminar en lote
        $responseDel = $this->actingAs($admin)->postJson(route('admin.noticias.bulk-action'), [
            'action' => 'delete',
            'ids' => $ids
        ]);
        $responseDel->assertStatus(200);
        $responseDel->assertJson(['success' => true, 'action' => 'delete', 'count' => 2]);

        $this->assertNull(Noticia::find($n1->id));
        $this->assertNull(Noticia::find($n2->id));
    }

    /** @test */
    public function admin_dashboard_renders_transmisiones_and_banners_stats()
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('En Vivo');
        $response->assertSee('Banners');
        $response->assertSee(route('admin.transmisiones.index'));
        $response->assertSee(route('admin.banners.index'));
    }

    /** @test */
    public function create_and_edit_news_views_include_live_preview_and_counters()
    {
        $admin = $this->getAdminUser();

        $responseCreate = $this->actingAs($admin)->get(route('admin.noticias.create'));
        $responseCreate->assertStatus(200);
        $responseCreate->assertSee('livePreviewModal');
        $responseCreate->assertSee('Vista Previa en Vivo');
        $responseCreate->assertSee('titulo-char-count');
        $responseCreate->assertSee('content-word-count');

        $noticia = Noticia::first();
        if ($noticia) {
            $responseEdit = $this->actingAs($admin)->get(route('admin.noticias.edit', $noticia->id));
            $responseEdit->assertStatus(200);
            $responseEdit->assertSee('livePreviewModal');
            $responseEdit->assertSee('Vista Previa en Vivo');
            $responseEdit->assertSee('titulo-char-count');
            $responseEdit->assertSee('content-word-count');
        }
    }
}
