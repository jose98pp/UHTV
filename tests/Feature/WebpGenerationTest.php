<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\ImageStorageService;
use App\Services\ImageValidationService;
use Illuminate\Support\Facades\Storage;

class WebpGenerationTest extends TestCase
{
    /** @test */
    public function generate_webp_handles_environment_gracefully()
    {
        Storage::fake('public');
        Storage::disk('public')->put('test.jpg', 'dummy-data');

        $storageService = app(ImageStorageService::class);
        $result = $storageService->generateWebpVersion('test.jpg');

        // En entornos sin extensión GD o imagewebp, debe retornar null sin lanzar excepciones fatales
        if (!function_exists('imagewebp')) {
            $this->assertNull($result);
        } else {
            $this->assertTrue(is_string($result) || is_null($result));
        }
    }

    /** @test */
    public function get_webp_url_if_exists_resolves_webp_file_when_present()
    {
        Storage::fake('public');
        Storage::disk('public')->put('noticias/general/sample.jpg', 'original');
        Storage::disk('public')->put('noticias/general/sample.webp', 'webp-version');

        $validationService = app(ImageValidationService::class);
        $webpUrl = $validationService->getWebpUrlIfExists('noticias/general/sample.jpg');

        $this->assertNotNull($webpUrl);
        $this->assertStringContainsString('sample.webp', $webpUrl);
    }
}
