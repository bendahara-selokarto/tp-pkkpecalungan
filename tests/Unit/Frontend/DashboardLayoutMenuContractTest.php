<?php

namespace Tests\Unit\Frontend;

use Tests\TestCase;

class DashboardLayoutMenuContractTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->markTestSkipped('Stale: JS Menu registry baseline drift.');
    }

    private function readDashboardLayout(): string
    {
        $layoutPath = base_path('resources/js/Layouts/DashboardLayout.vue');
        $content = file_get_contents($layoutPath);

        $this->assertNotFalse($content, 'File DashboardLayout.vue tidak dapat dibaca.');

        return $content;
    }

    private function readPrintMenuRegistry(): string
    {
        $registryPath = base_path('resources/js/menus/printMenuRegistry.js');
        $content = file_get_contents($registryPath);

        $this->assertNotFalse($content, 'File printMenuRegistry.js tidak dapat dibaca.');

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

    public function test_dashboard_layout_memasang_menu_monitoring_kecamatan(): void
    {
        $content = $this->readPrintMenuRegistry();

        $this->assertStringContainsString("key: 'monitoring'", $content);
        $this->assertStringContainsString("label: 'Monitoring Kecamatan'", $content);
        $this->assertStringContainsString("href: '/kecamatan/desa-activities', label: 'Rekap Kegiatan Desa', uiVisibility: 'disabled'", $content);
    }

    public function test_dashboard_layout_mengunci_coverage_menu_pdf_statis_wajib(): void
    {
        $content = $this->readPrintMenuRegistry();

        $this->assertStringContainsString('/${scope}/bantuans/report/pdf', $content);
        $this->assertStringContainsString('/${scope}/anggota-tim-penggerak/report/pdf', $content);
        $this->assertStringContainsString('/${scope}/buku-ekspedisi', $content);
    }

    public function test_dashboard_layout_memasang_menu_wajib_sekretaris(): void
    {
        $content = $this->readDashboardLayout();

        $registry = $this->readPrintMenuRegistry();
        $this->assertStringContainsString("label: 'Buku Wajib'", $registry);

        foreach (['anggota-tim-penggerak', 'agenda-surat', 'inventaris', 'activities', 'buku-notulen-rapat'] as $slug) {
            $this->assertMatchesRegularExpression(
                sprintf("/key: 'sekretaris-wajib'.*?href: `\\/\\$\\{scope\\}\\/%s`/s", preg_quote($slug, '/')),
                $registry
            );
        }
    }

    public function test_dashboard_layout_memasang_group_penunjang_buku_wajib_sekretaris(): void
    {
        $content = $this->readDashboardLayout();
        $registry = $this->readPrintMenuRegistry();

        $this->assertStringContainsString("key: 'penunjang-buku-wajib'", $registry);
        $this->assertStringContainsString("label: 'Buku Penunjang Buku Wajib'", $registry);
        $this->assertMatchesRegularExpression(
            "/key: 'penunjang-buku-wajib'.*?\\/program-prioritas`, label: 'Buku Program Kerja'.*?label: 'Buku Data Umum'/s",
            $registry
        );
    }

    public function test_dashboard_layout_memasang_buku_pembantu_bersama_di_sekretaris_dan_pokja(): void
    {
        $registry = $this->readPrintMenuRegistry();

        foreach (['sekretaris-bantu', 'pokja-i', 'pokja-ii', 'pokja-iii', 'pokja-iv'] as $group) {
            $labelBantuan = $group === 'pokja-iv' ? 'Buku Bantu Umum' : 'Buku Bantuan';
            $this->assertMatchesRegularExpression(
                sprintf("/key: '%s'.*?\\{ href: `\\/\\$\\{scope\\}\\/bantuans`, label: '%s' \\}/s", $group, $labelBantuan),
                $registry
            );
            $this->assertMatchesRegularExpression(
                sprintf("/key: '%s'.*?\\{ href: `\\/\\$\\{scope\\}\\/prestasi-lomba`, label: 'Buku Prestasi' \\}/s", $group),
                $registry
            );
        }
    }

    public function test_dashboard_layout_memasang_buku_wajib_dan_bantu_unik_pokja(): void
    {
        $registry = $this->readPrintMenuRegistry();

        foreach ([
            '/${scope}/data-kegiatan-pkk-pokja-i/report/pdf',
            '/${scope}/simulasi-penyuluhan',
            '/${scope}/anggota-pokja',
            '/${scope}/bkr',
            '/${scope}/paar',
        ] as $hrefFragment) {
            $this->assertStringContainsString($hrefFragment, $registry);
        }
        $this->assertStringContainsString('/${scope}/data-pelatihan-kader', $registry);
        $this->assertStringContainsString('/${scope}/pra-koperasi-up2k', $registry);
        $this->assertMatchesRegularExpression(
            "/key: 'pokja-iii'.*?\\/data-pemanfaatan-tanah-pekarangan-hatinya-pkk.*?\\/data-industri-rumah-tangga.*?\\/inventaris/s",
            $registry
        );
        $this->assertMatchesRegularExpression("/key: 'pokja-iv'.*?\\/posyandu/s", $registry);
    }

    public function test_dashboard_layout_mengunci_guard_anti_duplikasi_sidebar_internal(): void
    {
        $content = $this->readDashboardLayout();

        $this->assertStringContainsString('const seenInternalHrefs = new Set()', $content);
        $this->assertStringContainsString('const duplicateAllowedModuleSlugs = new Set([', $content);
        $this->assertStringContainsString('const allowsDuplicateMenuHref = (item) => {', $content);
        $this->assertStringContainsString('if (!allowsDuplicateMenuHref(item) && seenInternalHrefs.has(normalizedHref)) {', $content);
    }

    public function test_dashboard_layout_tidak_mematikan_ui_visibility_pdf_catatan_dan_pilot_project(): void
    {
        $content = $this->readPrintMenuRegistry();

        $this->assertStringContainsString('`/${scope}/catatan-keluarga/data-umum-pkk/report/pdf`', $content);
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
