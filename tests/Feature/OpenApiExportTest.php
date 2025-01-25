<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OpenApiExportTest extends TestCase
{
    protected string $baseDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->baseDir = realpath(__DIR__ . '/../../');
    }

    #[Test]
    public function test_artisan_lists_command() {
        Artisan::call('list');
        $output = Artisan::output();

        $this->assertStringContainsString('openapi:generate', $output);
    }

    #[Test]
    public function test_export_to_default_path()
    {
        Artisan::call('openapi:generate');

        $this->assertFileExists('openapi.yml');
    }

    #[Test]
    public function test_export_to_custom_path()
    {
        $customPath = 'openapi.custom.yml';
        Artisan::call('openapi:generate', ['--output' => $customPath]);

        $this->assertFileExists($customPath);
    }

    #[Test]
    public function test_export_in_json_format()
    {
        $customPath = 'openapi.json';
        Artisan::call('openapi:generate', ['--format' => 'json']);

        $this->assertFileExists($customPath);
        $this->assertJson(file_get_contents($customPath));
    }

    #[Test]
    public function test_export_with_raw_option()
    {
        Artisan::call('openapi:generate', ['--raw' => true]);

        $this->assertFileExists('openapi.yml');
    }
}
