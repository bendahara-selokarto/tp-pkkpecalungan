<?php

namespace Tests\Unit\Frontend;

use Tests\TestCase;

class ModalAccessibilityContractTest extends TestCase
{
    public function test_card_box_modal_mengunci_fokus_dan_escape_secara_konsisten(): void
    {
        $content = file_get_contents(base_path('resources/js/admin-one/components/CardBoxModal.vue'));

        $this->assertNotFalse($content, 'File CardBoxModal.vue tidak dapat dibaca.');
        $this->assertStringContainsString('const lastFocusedElement = ref(null)', $content);
        $this->assertStringContainsString('focusFirstElement', $content);
        $this->assertStringContainsString("if (e.key === 'Escape')", $content);
        $this->assertStringContainsString("if (e.key !== 'Tab')", $content);
        $this->assertStringContainsString('window.addEventListener(\'keydown\', closeWithKeyboardEvent)', $content);
        $this->assertStringContainsString('window.removeEventListener(\'keydown\', closeWithKeyboardEvent)', $content);
        $this->assertStringContainsString('lastFocusedElement.value.focus()', $content);
    }

    public function test_confirm_action_modal_tetap_menggunakan_card_box_modal(): void
    {
        $content = file_get_contents(base_path('resources/js/admin-one/components/ConfirmActionModal.vue'));

        $this->assertNotFalse($content, 'File ConfirmActionModal.vue tidak dapat dibaca.');
        $this->assertStringContainsString('import CardBoxModal from', $content);
        $this->assertStringContainsString('<CardBoxModal', $content);
    }
}
