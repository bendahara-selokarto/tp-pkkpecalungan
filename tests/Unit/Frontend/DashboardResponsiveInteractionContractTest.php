<?php

namespace Tests\Unit\Frontend;

use Tests\TestCase;

class DashboardResponsiveInteractionContractTest extends TestCase
{
    public function test_dashboard_memiliki_toggle_responsive_table_dengan_fallback_legacy(): void
    {
        $content = file_get_contents(base_path('resources/js/Pages/Dashboard.vue'));

        $this->assertNotFalse($content, 'File Dashboard.vue tidak dapat dibaca.');
        $this->assertStringContainsString(
            "const isResponsiveTableV2Enabled = computed(() => import.meta.env.VITE_UI_RESPONSIVE_TABLE_V2 !== 'false')",
            $content
        );
        $this->assertStringContainsString(
            '<ResponsiveDataTable',
            $content
        );
        $this->assertStringContainsString(
            'v-if="isResponsiveTableV2Enabled"',
            $content
        );
        $this->assertStringContainsString(
            '<div v-else class="mt-4 overflow-x-auto rounded-md border border-slate-200 dark:border-slate-700">',
            $content
        );
    }

    public function test_dashboard_filter_utama_memenuhi_target_sentuh_minimum_44px(): void
    {
        $content = file_get_contents(base_path('resources/js/Pages/Dashboard.vue'));

        $this->assertNotFalse($content, 'File Dashboard.vue tidak dapat dibaca.');
        $this->assertStringContainsString(
            'min-h-[44px] w-full rounded-md border border-slate-300 px-3 py-2 text-sm',
            $content
        );
        $this->assertStringContainsString(
            'min-h-[44px] w-full rounded-md bg-emerald-600 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-700',
            $content
        );
    }
}

