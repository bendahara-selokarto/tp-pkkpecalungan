<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PdfHeaderComponentTest extends TestCase
{
    public function test_header_component_renders_correctly(): void
    {
        $view = $this->view('pdf.partials._report_header', [
            'headerTitle' => 'BUKU AGENDA',
            'headerRole' => 'SEKRETARIS PKK DESA',
            'headerVillage' => 'Selokarto',
            'headerKecamatan' => 'Pecalungan',
            'headerYear' => 2026,
            'headerLampiran' => 'LAMPIRAN 4.10'
        ]);

        $view->assertSee('LAMPIRAN 4.10');
        $view->assertSee('BUKU AGENDA SEKRETARIS PKK DESA');
        $view->assertSee('Desa: Selokarto');
        $view->assertSee('Kecamatan: Pecalungan');
        $view->assertSee('Tahun Anggaran: 2026');
    }

    public function test_header_component_renders_without_village_for_kecamatan(): void
    {
        $view = $this->view('pdf.partials._report_header', [
            'headerTitle' => 'BUKU AGENDA',
            'headerRole' => 'SEKRETARIS PKK KECAMATAN',
            'headerKecamatan' => 'Pecalungan',
            'headerYear' => 2026,
            'headerLampiran' => 'LAMPIRAN 4.10'
        ]);

        $view->assertSee('LAMPIRAN 4.10');
        $view->assertSee('BUKU AGENDA SEKRETARIS PKK KECAMATAN');
        $view->assertDontSee('Desa:');
        $view->assertSee('Kecamatan: Pecalungan');
        $view->assertSee('Tahun Anggaran: 2026');
    }
}
