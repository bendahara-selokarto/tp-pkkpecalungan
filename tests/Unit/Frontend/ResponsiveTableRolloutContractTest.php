<?php

namespace Tests\Unit\Frontend;

use Tests\TestCase;

class ResponsiveTableRolloutContractTest extends TestCase
{
    /**
     * @return array<string, array{path: string}>
     */
    public static function rolloutPagesProvider(): array
    {
        return [
            'dashboard' => ['path' => 'resources/js/Pages/Dashboard.vue'],
            'super-admin-users' => ['path' => 'resources/js/Pages/SuperAdmin/Users/Index.vue'],
            'arsip-index' => ['path' => 'resources/js/Pages/Arsip/Index.vue'],
        ];
    }

    /**
     * @dataProvider rolloutPagesProvider
     */
    public function test_rollout_pages_menggunakan_kontrak_responsive_table_v2(string $path): void
    {
        $content = file_get_contents(base_path($path));

        $this->assertNotFalse($content, "File {$path} tidak dapat dibaca.");
        $this->assertStringContainsString(
            "import.meta.env.VITE_UI_RESPONSIVE_TABLE_V2 !== 'false'",
            $content,
            "Feature flag rollback V2 wajib ada di {$path}."
        );
        $this->assertStringContainsString(
            'ResponsiveDataTable',
            $content,
            "Komponen ResponsiveDataTable wajib dipakai di {$path}."
        );
        $this->assertStringContainsString(
            'mobileLabel',
            $content,
            "Metadata mobileLabel wajib ada di {$path}."
        );
    }
}

