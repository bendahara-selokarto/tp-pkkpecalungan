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
            'const showLegacyFallback = computed(() =>',
            $content
        );
        $this->assertStringContainsString(
            'Boolean(import.meta.env.DEV)',
            $content
        );
        $this->assertStringContainsString(
            '<template v-else-if="showLegacyFallback">',
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
