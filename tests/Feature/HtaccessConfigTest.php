<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\File;

class HtaccessConfigTest extends TestCase
{
    /** @test */
    public function htaccess_contains_compression_and_cache_directives()
    {
        $htaccessPath = public_path('.htaccess');
        $this->assertFileExists($htaccessPath);

        $content = File::get($htaccessPath);

        // Compresión
        $this->assertStringContainsString('mod_deflate.c', $content);
        $this->assertStringContainsString('AddOutputFilterByType DEFLATE', $content);

        // Expiración de caché
        $this->assertStringContainsString('mod_expires.c', $content);
        $this->assertStringContainsString('ExpiresByType image/webp', $content);
        $this->assertStringContainsString('ExpiresByType text/css', $content);

        // Cabeceras
        $this->assertStringContainsString('mod_headers.c', $content);
        $this->assertStringContainsString('Cache-Control', $content);
        $this->assertStringContainsString('X-Content-Type-Options', $content);
    }

    /** @test */
    public function application_responds_successfully_with_configured_htaccess()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }
}
