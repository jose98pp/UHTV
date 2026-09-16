<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Noticia;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class NewsCreationEnhancementsTest extends TestCase
{
    use DatabaseTransactions;

    protected $admin;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Enhancements',
            'email' => 'admin_enh_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->category = Category::create([
            'name' => 'Test Cat ' . uniqid(),
            'descripcion' => 'Descripción de prueba'
        ]);
    }

    /** @test */
    public function it_allows_titles_with_quotes_question_marks_and_special_characters()
    {
        $file = new UploadedFile(public_path('images/Logo.jpg'), 'Logo.jpg', 'image/jpeg', null, true);

        $specialTitle = '¿Crisis en Bolivia? Gobierno anuncia: "Nuevas medidas" del 10% y $50 Bs — ¡Histórico!';

        $response = $this->actingAs($this->admin)->post(route('admin.noticias.store'), [
            'titulo' => $specialTitle,
            'contenido' => '<p>Este es un contenido completo con formato y <a href="https://example.com" target="_blank">enlace seguro</a> para verificar la noticia.</p>',
            'category_id' => $this->category->id,
            'imagen' => $file,
            'publicada' => 1,
        ]);

        $response->assertRedirect(route('admin.noticias.index'));
        $this->assertDatabaseHas('noticias', [
            'titulo' => $specialTitle,
            'category_id' => $this->category->id,
        ]);

        $noticia = Noticia::where('titulo', $specialTitle)->first();
        $this->assertNotNull($noticia);
        $this->assertStringContainsString('href="https://example.com"', $noticia->contenido_sanitizado);
        $this->assertStringContainsString('enlace seguro', $noticia->contenido_sanitizado);
    }

    /** @test */
    public function it_can_store_and_retrieve_multimedia_gallery_images()
    {
        $cover = new UploadedFile(public_path('images/Logo.jpg'), 'Logo.jpg', 'image/jpeg', null, true);
        $extra1 = new UploadedFile(public_path('images/Logo.jpg'), 'extra1.jpg', 'image/jpeg', null, true);
        $extra2 = new UploadedFile(public_path('images/Logo.jpg'), 'extra2.jpg', 'image/jpeg', null, true);

        $response = $this->actingAs($this->admin)->post(route('admin.noticias.store'), [
            'titulo' => 'Noticia con Galería de Fotos ' . uniqid(),
            'contenido' => '<p>Reportaje con cobertura fotográfica completa del evento realizado hoy en la ciudad.</p>',
            'category_id' => $this->category->id,
            'imagen' => $cover,
            'galeria' => [$extra1, $extra2],
            'publicada' => 1,
        ]);

        $response->assertRedirect(route('admin.noticias.index'));

        $noticia = Noticia::where('category_id', $this->category->id)
            ->whereNotNull('galeria')
            ->latest('id')
            ->first();

        $this->assertNotNull($noticia);
        $this->assertIsArray($noticia->galeria);
        $this->assertCount(2, $noticia->galeria);
        $this->assertCount(2, $noticia->galeria_urls);
    }

    /** @test */
    public function it_renders_clickable_links_in_show_view()
    {
        $noticia = Noticia::create([
            'titulo' => 'Noticia con Enlaces Clicables ' . uniqid(),
            'contenido' => '<p>Para más información consulte <a href="https://ultimahora-tv.com/detalles" target="_blank">este enlace oficial</a> de la noticia.</p>',
            'category_id' => $this->category->id,
            'user_id' => $this->admin->id,
            'publicada' => true,
        ]);

        $response = $this->get($noticia->url);
        $response->assertStatus(200);
        // Debe contener el enlace como etiqueta HTML activa y no como texto escapado &lt;a
        $response->assertSee('href="https://ultimahora-tv.com/detalles"', false);
        $response->assertDontSee('&lt;a href=', false);
    }

    /** @test */
    public function admin_dashboard_includes_transmisiones_shortcuts()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee(route('admin.transmisiones.index'));
        $response->assertSee('En Vivo / Streams');
    }
}
