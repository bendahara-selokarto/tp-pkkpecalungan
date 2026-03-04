<?php

namespace Tests\Unit\Frontend;

use Tests\TestCase;

class DashboardLayoutMenuContractTest extends TestCase
{
    private function readDashboardLayout(): string
    {
        $layoutPath = base_path('resources/js/Layouts/DashboardLayout.vue');
        $content = file_get_contents($layoutPath);

        $this->assertNotFalse($content, 'File DashboardLayout.vue tidak dapat dibaca.');

        return $content;
    }

    public function test_dashboard_layout_menyaring_item_menu_berdasarkan_module_modes_backend(): void
    {
        $content = $this->readDashboardLayout();
        $this->assertStringContainsString(
            'const isModuleAllowedForCurrentUser = (item) => {',
            $content
        );
        $this->assertStringContainsString(
            "if (!isModuleAllowedForCurrentUser(item)) {",
            $content
        );
    }

    public function test_dashboard_layout_mengunci_coverage_menu_pdf_statis_wajib(): void
    {
        $content = $this->readDashboardLayout();

        $this->assertStringContainsString('/${scope}/bantuans/report/pdf', $content);
        $this->assertStringContainsString('/${scope}/anggota-tim-penggerak-kader/report/pdf', $content);
        $this->assertStringContainsString('/${scope}/agenda-surat/ekspedisi/report/pdf', $content);
        $this->assertStringContainsString('/${scope}/catatan-keluarga/data-kegiatan-pkk-pokja-iv/report/pdf', $content);
    }

    public function test_dashboard_layout_memasang_menu_inventaris_di_semua_group_pokja(): void
    {
        $content = $this->readDashboardLayout();

        $this->assertMatchesRegularExpression(
            "/key: 'pokja-i'.*?\\{ href: `\\/\\$\\{scope\\}\\/inventaris`, label: 'Buku Inventaris' \\}/s",
            $content
        );
        $this->assertMatchesRegularExpression(
            "/key: 'pokja-ii'.*?\\{ href: `\\/\\$\\{scope\\}\\/inventaris`, label: 'Buku Inventaris' \\}/s",
            $content
        );
        $this->assertMatchesRegularExpression(
            "/key: 'pokja-iii'.*?\\{ href: `\\/\\$\\{scope\\}\\/inventaris`, label: 'Buku Inventaris' \\}/s",
            $content
        );
        $this->assertMatchesRegularExpression(
            "/key: 'pokja-iv'.*?\\{ href: `\\/\\$\\{scope\\}\\/inventaris`, label: 'Buku Inventaris' \\}/s",
            $content
        );
    }

    public function test_dashboard_layout_memasang_menu_buku_tamu_di_semua_group_pokja(): void
    {
        $content = $this->readDashboardLayout();

        $this->assertMatchesRegularExpression(
            "/key: 'pokja-i'.*?\\{ href: `\\/\\$\\{scope\\}\\/buku-tamu`, label: 'Buku Tamu' \\}/s",
            $content
        );
        $this->assertMatchesRegularExpression(
            "/key: 'pokja-ii'.*?\\{ href: `\\/\\$\\{scope\\}\\/buku-tamu`, label: 'Buku Tamu' \\}/s",
            $content
        );
        $this->assertMatchesRegularExpression(
            "/key: 'pokja-iii'.*?\\{ href: `\\/\\$\\{scope\\}\\/buku-tamu`, label: 'Buku Tamu' \\}/s",
            $content
        );
        $this->assertMatchesRegularExpression(
            "/key: 'pokja-iv'.*?\\{ href: `\\/\\$\\{scope\\}\\/buku-tamu`, label: 'Buku Tamu' \\}/s",
            $content
        );
    }

    public function test_dashboard_layout_mengunci_guard_anti_duplikasi_sidebar_internal(): void
    {
        $content = $this->readDashboardLayout();

        $this->assertStringContainsString('const seenInternalHrefs = new Set()', $content);
        $this->assertStringContainsString('if (!isExternalItem(item) && seenInternalHrefs.has(item.href)) {', $content);
    }

    public function test_dashboard_layout_tidak_mematikan_ui_visibility_pdf_catatan_dan_pilot_project(): void
    {
        $content = $this->readDashboardLayout();

        $this->assertStringContainsString('{ href: `/${scope}/catatan-keluarga`, label: \'Catatan Keluarga\' }', $content);
        $this->assertStringContainsString('{ href: `/${scope}/pilot-project-naskah-pelaporan`, label: \'Naskah Pelaporan Pilot Project Pokja IV\' }', $content);
        $this->assertStringContainsString('{ href: `/${scope}/pilot-project-keluarga-sehat`, label: \'Laporan Pelaksanaan Pilot Project Gerakan Keluarga Sehat Tanggap dan Tangguh Bencana\' }', $content);
        $this->assertStringNotContainsString('href: `/${scope}/catatan-keluarga`, label: \'Catatan Keluarga\', uiVisibility: \'disabled\'', $content);
        $this->assertStringNotContainsString('href: `/${scope}/pilot-project-naskah-pelaporan`, label: \'Naskah Pelaporan Pilot Project Pokja IV\', uiVisibility: \'disabled\'', $content);
        $this->assertStringNotContainsString('href: `/${scope}/pilot-project-keluarga-sehat`, label: \'Laporan Pelaksanaan Pilot Project Gerakan Keluarga Sehat Tanggap dan Tangguh Bencana\', uiVisibility: \'disabled\'', $content);
    }

    public function test_dashboard_layout_mengunci_active_state_item_dan_persistensi_collapse_sidebar(): void
    {
        $content = $this->readDashboardLayout();

        $this->assertStringContainsString('const isItemActive = (item) => !isExternalItem(item) && isActive(item.href)', $content);
        $this->assertStringContainsString('const sidebarCollapsedKey = \'admin-one-sidebar-collapsed\'', $content);
        $this->assertStringContainsString('const persistSidebarCollapsedPreference = (collapsed) => {', $content);
        $this->assertStringContainsString('localStorage.setItem(sidebarCollapsedKey, collapsed ? \'1\' : \'0\')', $content);
    }

    public function test_dashboard_layout_menyembunyikan_menu_domain_untuk_super_admin(): void
    {
        $content = $this->readDashboardLayout();

        $this->assertStringContainsString(
            '<div v-if="!isProfilePage && !hasRole(\'super-admin\')" class="space-y-1">',
            $content
        );
    }
}
