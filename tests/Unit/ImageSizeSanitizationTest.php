<?php

namespace Tests\Unit;

use App\Services\ContentSanitizationService;
use Tests\TestCase;

class ImageSizeSanitizationTest extends TestCase
{
    /** @test */
    public function it_preserves_editor_image_frame_and_dimensions()
    {
        $service = new ContentSanitizationService();

        $clean = $service->sanitizeContent(
            '<p><span class="editor-image-frame"><img src="/storage/images/test.jpg" alt="Imagen" class="editor-image" width="407" height="300"></span></p>'
        );

        $this->assertStringContainsString('editor-image-frame', $clean);
        $this->assertStringContainsString('width="407"', $clean);
        $this->assertStringContainsString('height="300"', $clean);
    }
}
