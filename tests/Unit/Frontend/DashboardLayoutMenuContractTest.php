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

    public function test_dashboard_layout_memasang_submenu_belum_ada_pemilik_kecamatan(): void
    {
        $content = $this->readDashboardLayout();

        $this->assertStringContainsString("key: 'belum-ada-pemilik'", $content);
        $this->assertStringContainsString("label: 'Belum Ada Pemilik'", $content);
        $this->assertStringContainsString("{ href: '/kecamatan/buku-keuangan', label: 'Buku Keuangan' }", $content);
        $this->assertStringContainsString("{ href: '/kecamatan/data-pelatihan-kader', label: 'Data Pelatihan Kader | 4.14.3' }", $content);
        $this->assertStringNotContainsString("{ href: '/kecamatan/catatan-keluarga', label: 'Catatan Keluarga | 4.15' }", $content);
    }

    public function test_dashboard_layout_mengunci_coverage_menu_pdf_statis_wajib(): void
    {
        $content = $this->readDashboardLayout();

        $this->assertStringContainsString('/${scope}/bantuans/report/pdf', $content);
        $this->assertStringContainsString('/${scope}/anggota-tim-penggerak-kader/report/pdf', $content);
        $this->assertStringContainsString('/${scope}/agenda-surat/ekspedisi/report/pdf', $content);
        $this->assertStringContainsString('/${scope}/catatan-keluarga/data-kegiatan-pkk-pokja-iv/report/pdf', $content);
    }

    public function test_dashboard_layout_memasang_menu_wajib_sekretaris(): void
    {
        $content = $this->readDashboardLayout();

        foreach (['anggota-tim-penggerak', 'agenda-surat', 'inventaris', 'activities', 'buku-notulen-rapat'] as $slug) {
            $this->assertMatchesRegularExpression(
                sprintf("/key: 'sekretaris-tpk'.*?href: `\\/\\$\\{scope\\}\\/%s`/s", preg_quote($slug, '/')),
                $content
            );
        }
    }

    public function test_dashboard_layout_memasang_group_penunjang_buku_wajib_sekretaris(): void
    {
        $content = $this->readDashboardLayout();

        $this->assertStringContainsString("key: 'penunjang-buku-wajib'", $content);
        $this->assertStringContainsString("label: 'Penunjang Buku Wajib'", $content);
        $this->assertMatchesRegularExpression(
            "/key: 'penunjang-buku-wajib'.*?label: 'Data Umum'.*?\\/program-prioritas`, label: 'Program Kerja'/s",
            $content
        );
    }

    public function test_dashboard_layout_memasang_buku_bantu_seragam_di_sekretaris_dan_pokja(): void
    {
        $content = $this->readDashboardLayout();

        foreach (['sekretaris-tpk', 'pokja-i', 'pokja-ii', 'pokja-iii', 'pokja-iv'] as $group) {
            $this->assertMatchesRegularExpression(
                sprintf("/key: '%s'.*?\\{ href: `\\/\\$\\{scope\\}\\/bantuans`, label: 'Buku Bantuan' \\}/s", $group),
                $content
            );
            $this->assertMatchesRegularExpression(
                sprintf("/key: '%s'.*?\\{ href: `\\/\\$\\{scope\\}\\/prestasi-lomba`, label: 'Buku Prestasi' \\}/s", $group),
                $content
            );
            $this->assertMatchesRegularExpression(
                sprintf("/key: '%s'.*?\\{ href: `\\/\\$\\{scope\\}\\/kader-khusus`, label: 'Buku Kader Khusus' \\}/s", $group),
                $content
            );
        }

        $this->assertStringNotContainsString("key: 'bendahara-tpk'", $content);
    }

    public function test_dashboard_layout_memasang_buku_wajib_dan_bantu_unik_pokja(): void
    {
        $content = $this->readDashboardLayout();

        $this->assertMatchesRegularExpression(
            "/key: 'pokja-i'.*?\\/data-kegiatan-pkk-pokja-i\\/report\\/pdf.*?\\/simulasi-penyuluhan.*?\\/bkr.*?\\/anggota-pokja.*?\\/paar/s",
            $content
        );
        $this->assertMatchesRegularExpression(
            "/key: 'pokja-ii'.*?\\/catatan-keluarga\\/data-kegiatan-pkk-pokja-ii\\/report\\/pdf.*?\\/pra-koperasi-up2k/s",
            $content
        );
        $this->assertMatchesRegularExpression(
            "/key: 'pokja-iii'.*?\\/catatan-keluarga\\/data-kegiatan-pkk-pokja-iii\\/report\\/pdf.*?\\/data-keluarga.*?\\/data-pemanfaatan-tanah-pekarangan-hatinya-pkk.*?\\/data-industri-rumah-tangga.*?\\/inventaris/s",
            $content
        );
        $this->assertMatchesRegularExpression(
            "/key: 'pokja-iv'.*?\\/catatan-keluarga\\/data-kegiatan-pkk-pokja-iv\\/report\\/pdf.*?\\/catatan-keluarga\\/data-umum-pkk\\/report\\/pdf.*?\\/posyandu/s",
            $content
        );
    }

    public function test_dashboard_layout_mengunci_guard_anti_duplikasi_sidebar_internal(): void
    {
        $content = $this->readDashboardLayout();

        $this->assertStringContainsString('const seenInternalHrefs = new Set()', $content);
        $this->assertStringContainsString("const duplicateAllowedModuleSlugs = new Set(['activities', 'bantuans', 'catatan-keluarga', 'inventaris', 'kader-khusus', 'prestasi-lomba', 'program-prioritas'])", $content);
        $this->assertStringContainsString('const allowsDuplicateMenuHref = (item) => {', $content);
        $this->assertStringContainsString('if (!isExternalItem(item) && !allowsDuplicateMenuHref(item) && seenInternalHrefs.has(item.href)) {', $content);
    }

    public function test_dashboard_layout_tidak_mematikan_ui_visibility_pdf_catatan_dan_pilot_project(): void
    {
        $content = $this->readDashboardLayout();

        $this->assertStringContainsString('href: `/${scope}/catatan-keluarga/data-kegiatan-pkk-pokja-iv/report/pdf`', $content);
        $this->assertStringContainsString('href: `/${scope}/catatan-keluarga/data-umum-pkk/report/pdf`', $content);
        $this->assertStringNotContainsString('href: `/${scope}/catatan-keluarga/data-kegiatan-pkk-pokja-iv/report/pdf`, label: \'Buku Data Kegiatan Pokja IV\', uiVisibility: \'disabled\'', $content);
        $this->assertStringNotContainsString('href: `/${scope}/catatan-keluarga/data-umum-pkk/report/pdf`, label: \'Data Umum Pokja IV\', uiVisibility: \'disabled\'', $content);
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
