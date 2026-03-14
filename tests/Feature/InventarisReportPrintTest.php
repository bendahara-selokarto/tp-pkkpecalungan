<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Inventaris\Models\Inventaris;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\Feature\Concerns\AssertsPdfReportHeaders;
use Tests\TestCase;

class InventarisReportPrintTest extends TestCase
{
    use RefreshDatabase;
    use AssertsPdfReportHeaders;

    private const ACTIVE_BUDGET_YEAR = 2026;

    protected Area $kecamatanA;

    protected Area $kecamatanB;

    protected Area $desaA;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'desa-sekretaris']);
        Role::firstOrCreate(['name' => 'kecamatan-sekretaris']);

        $this->kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $this->kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);
        $this->desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $this->kecamatanA->id]);
    }

    public function test_header_kolom_pdf_inventaris_tetap_stabil(): void
    {
        $this->assertPdfReportHeadersInOrder('pdf.inventaris_report', [
            'NO',
            'NAMA BARANG',
            'ASAL BARANG',
            'TANGGAL PENERIMAAN/PEMBELIAN',
            'JUMLAH',
            'TEMPAT PENYIMPANAN',
            'KONDISI BARANG',
            'KETERANGAN',
        ]);
    }

    public function test_admin_desa_dapat_mencetak_laporan_pdf_inventaris_desanya_sendiri(): void
    {
        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $this->desaA->id,
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('desa-sekretaris');

        Inventaris::create([
            'name' => 'Lemari Arsip',
            'description' => 'Arsip utama',
            'quantity' => 2,
            'unit' => 'unit',
            'condition' => 'baik',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $response = $this->actingAs($user)->get(route('desa.inventaris.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_admin_kecamatan_dapat_mencetak_laporan_pdf_inventaris_kecamatannya_sendiri(): void
    {
        $user = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('kecamatan-sekretaris');

        Inventaris::create([
            'name' => 'Printer Operasional',
            'description' => 'Untuk sekretariat',
            'quantity' => 1,
            'unit' => 'unit',
            'condition' => 'baik',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $response = $this->actingAs($user)->get(route('kecamatan.inventaris.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_laporan_pdf_inventaris_tetap_aman_saat_scope_metadata_tidak_sinkron(): void
    {
        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $this->kecamatanB->id,
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('desa-sekretaris');

        $response = $this->actingAs($user)->get(route('desa.inventaris.report'));

        $response->assertStatus(403);
    }
}
