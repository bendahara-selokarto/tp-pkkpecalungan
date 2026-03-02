<?php

namespace Tests\Unit\Frontend;

use Tests\TestCase;

class DashboardLayoutMenuContractTest extends TestCase
{
    public function test_dashboard_layout_menyaring_item_menu_berdasarkan_module_modes_backend(): void
    {
        $layoutPath = base_path('resources/js/Layouts/DashboardLayout.vue');
        $content = file_get_contents($layoutPath);

        $this->assertNotFalse($content, 'File DashboardLayout.vue tidak dapat dibaca.');
        $this->assertStringContainsString(
            'const isModuleAllowedForCurrentUser = (item) => {',
            $content
        );
        $this->assertStringContainsString(
            "if (!isModuleAllowedForCurrentUser(item)) {",
            $content
        );
    }
}

