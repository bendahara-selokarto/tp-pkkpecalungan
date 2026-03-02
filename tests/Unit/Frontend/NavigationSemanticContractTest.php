<?php

namespace Tests\Unit\Frontend;

use Tests\TestCase;

class NavigationSemanticContractTest extends TestCase
{
    public function test_navbar_item_dropdown_menggunakan_button_semantik_dan_focus_visible(): void
    {
        $content = file_get_contents(base_path('resources/js/admin-one/components/NavBarItem.vue'));

        $this->assertNotFalse($content, 'File NavBarItem.vue tidak dapat dibaca.');
        $this->assertStringContainsString("if (props.item.menu) {\n    return 'button';", $content);
        $this->assertStringContainsString('focus-visible:outline-2', $content);
        $this->assertStringContainsString(":aria-expanded=\"item.menu ? String(isDropdownActive) : null\"", $content);
    }

    public function test_aside_menu_item_dropdown_menggunakan_button_semantik_dan_focus_visible(): void
    {
        $content = file_get_contents(base_path('resources/js/admin-one/components/AsideMenuItem.vue'));

        $this->assertNotFalse($content, 'File AsideMenuItem.vue tidak dapat dibaca.');
        $this->assertStringContainsString("if (hasDropdown.value) {\n    return 'button';", $content);
        $this->assertStringContainsString('focus-visible:outline-2', $content);
        $this->assertStringContainsString(":aria-expanded=\"hasDropdown ? String(isDropdownActive) : null\"", $content);
    }
}
