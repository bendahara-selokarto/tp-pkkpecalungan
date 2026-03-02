<?php

namespace Tests\Unit\Frontend;

use Tests\TestCase;

class ResponsiveTableStateContractTest extends TestCase
{
    public function test_responsive_data_table_menyediakan_state_loading_error_disabled(): void
    {
        $content = file_get_contents(base_path('resources/js/admin-one/components/ResponsiveDataTable.vue'));

        $this->assertNotFalse($content, 'File ResponsiveDataTable.vue tidak dapat dibaca.');
        $this->assertStringContainsString('state:', $content);
        $this->assertStringContainsString('loadingText:', $content);
        $this->assertStringContainsString('errorText:', $content);
        $this->assertStringContainsString('disabledText:', $content);
        $this->assertStringContainsString('const normalizedState = computed(() =>', $content);
        $this->assertStringContainsString("if (['loading', 'error', 'disabled'].includes(value))", $content);
        $this->assertStringContainsString('v-if="normalizedState !== \'ready\'"', $content);
    }
}
