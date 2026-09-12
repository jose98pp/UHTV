<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\File;

class CiCdAndLegacyCleanupTest extends TestCase
{
    /** @test */
    public function legacy_webpack_mix_files_are_deleted()
    {
        $this->assertFileDoesNotExist(base_path('webpack.mix.js'), 'webpack.mix.js should be deleted');
        $this->assertFileDoesNotExist(public_path('mix-manifest.json'), 'mix-manifest.json should be deleted');
    }

    /** @test */
    public function github_actions_workflow_exists()
    {
        $workflowPath = base_path('.github/workflows/deploy.yml');
        $this->assertFileExists($workflowPath, 'CI/CD workflow file should exist');

        $content = File::get($workflowPath);
        $this->assertStringContainsString('npm run build', $content);
        $this->assertStringContainsString('config:cache', $content);
        $this->assertStringContainsString('route:cache', $content);
        $this->assertStringContainsString('view:cache', $content);
    }
}
