<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Noticia;
use App\Models\Category;
use App\Models\User;
use App\Services\ImageStorageService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class NoticiaImageCleanupOnDeleteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /** @test */
    public function deleting_noticia_automatically_deletes_main_image_and_webp_from_storage()
    {
        // 1. Crear archivos simulados en public storage
        Storage::disk('public')->put('noticias/nacional/principal.jpg', 'fake-jpg-content');
        Storage::disk('public')->put('noticias/nacional/principal.webp', 'fake-webp-content');

        $this->assertTrue(Storage::disk('public')->exists('noticias/nacional/principal.jpg'));
        $this->assertTrue(Storage::disk('public')->exists('noticias/nacional/principal.webp'));

        // 2. Crear modelo Noticia
        $category = Category::firstOrCreate(['name' => 'Nacional', 'slug' => 'nacional']);
        $user = User::first() ?? User::factory()->create();

        $noticia = Noticia::create([
            'titulo' => 'Noticia para prueba de eliminación',
            'contenido' => 'Contenido de prueba',
            'category_id' => $category->id,
            'user_id' => $user->id,
            'imagen' => 'noticias/nacional/principal.jpg',
            'publicada' => true,
        ]);

        // 3. Eliminar la noticia
        $noticia->delete();

        // 4. Verificar que tanto la imagen principal como su versión WebP fueron eliminadas del storage
        $this->assertFalse(Storage::disk('public')->exists('noticias/nacional/principal.jpg'), 'La imagen principal debe ser eliminada del storage');
        $this->assertFalse(Storage::disk('public')->exists('noticias/nacional/principal.webp'), 'La versión WebP de la imagen principal debe ser eliminada del storage');
    }

    /** @test */
    public function deleting_noticia_automatically_deletes_gallery_images_and_webp_from_storage()
    {
        // 1. Crear fotos de galería en public storage
        Storage::disk('public')->put('noticias/deportes/foto1.jpg', 'galeria-1');
        Storage::disk('public')->put('noticias/deportes/foto1.webp', 'galeria-1-webp');
        Storage::disk('public')->put('noticias/deportes/foto2.png', 'galeria-2');
        Storage::disk('public')->put('noticias/deportes/foto2.webp', 'galeria-2-webp');

        $category = Category::firstOrCreate(['name' => 'Deportes', 'slug' => 'deportes']);
        $user = User::first() ?? User::factory()->create();

        $noticia = Noticia::create([
            'titulo' => 'Noticia con galería para prueba de eliminación',
            'contenido' => 'Contenido de prueba',
            'category_id' => $category->id,
            'user_id' => $user->id,
            'imagen' => null,
            'galeria' => [
                'noticias/deportes/foto1.jpg',
                'noticias/deportes/foto2.png',
            ],
            'publicada' => true,
        ]);

        // 2. Eliminar la noticia
        $noticia->delete();

        // 3. Verificar que todas las fotos de la galería y sus versiones WebP fueron eliminadas
        $this->assertFalse(Storage::disk('public')->exists('noticias/deportes/foto1.jpg'));
        $this->assertFalse(Storage::disk('public')->exists('noticias/deportes/foto1.webp'));
        $this->assertFalse(Storage::disk('public')->exists('noticias/deportes/foto2.png'));
        $this->assertFalse(Storage::disk('public')->exists('noticias/deportes/foto2.webp'));
    }

    /** @test */
    public function delete_image_method_normalizes_and_deletes_associated_webp()
    {
        Storage::disk('public')->put('noticias/test/sample.jpg', 'image-content');
        Storage::disk('public')->put('noticias/test/sample.webp', 'webp-content');

        $service = app(ImageStorageService::class);

        // Debe funcionar incluso si se pasa con prefijo /storage/
        $result = $service->deleteImage('/storage/noticias/test/sample.jpg');

        $this->assertTrue($result);
        $this->assertFalse(Storage::disk('public')->exists('noticias/test/sample.jpg'));
        $this->assertFalse(Storage::disk('public')->exists('noticias/test/sample.webp'));
    }

    /** @test */
    public function optimize_image_resizes_oversized_images_and_creates_webp()
    {
        if (!function_exists('imagecreatefromstring')) {
            $this->markTestSkipped('GD no disponible');
        }

        // Crear una imagen de 2000x1000 píxeles usando GD
        $img = imagecreatetruecolor(2000, 1000);
        $path = Storage::disk('public')->path('noticias/test/large.jpg');
        @mkdir(dirname($path), 0777, true);
        imagejpeg($img, $path, 100);
        imagedestroy($img);

        $service = app(ImageStorageService::class);
        $optimized = $service->optimizeImage('noticias/test/large.jpg', 1600, 1200, 85);

        $this->assertTrue($optimized);

        // Verificar que las dimensiones se redujeron proporcionalmente a max 1600px
        $info = getimagesize($path);
        $this->assertLessThanOrEqual(1600, $info[0]);
        $this->assertLessThanOrEqual(1200, $info[1]);

        // Verificar que se generó la versión WebP
        $webpPath = Storage::disk('public')->path('noticias/test/large.webp');
        $this->assertFileExists($webpPath);

        // Limpiar
        @unlink($path);
        @unlink($webpPath);
    }
}
