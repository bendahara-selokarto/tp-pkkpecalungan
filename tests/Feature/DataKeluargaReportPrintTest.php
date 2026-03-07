<?php

namespace Tests\Feature;

use App\Domains\Wilayah\DataKeluarga\Models\DataKeluarga;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\Feature\Concerns\AssertsPdfReportHeaders;
use Tests\TestCase;

class DataKeluargaReportPrintTest extends TestCase
{
    use AssertsPdfReportHeaders;
    use RefreshDatabase;

    private const ACTIVE_BUDGET_YEAR = 2026;

    protected Area $kecamatanA;

    protected Area $kecamatanB;

    protected Area $desaA;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin-desa']);
        Role::create(['name' => 'admin-kecamatan']);

        $this->kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $this->kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);
        $this->desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $this->kecamatanA->id]);
    }

    public function test_header_kolom_pdf_data_keluarga_tetap_sesuai_pedoman(): void
    {
        $this->assertPdfReportHeadersInOrder('pdf.data_keluarga_report', [
            'NO',
            'KATEGORI KELUARGA',
            'JUMLAH KELUARGA',
            'KETERANGAN',
        ]);
    }

    public function test_admin_desa_dapat_mencetak_laporan_pdf_data_keluarga_desanya_sendiri(): void
    {
        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $this->desaA->id,
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('admin-desa');

        DataKeluarga::create([
            'kategori_keluarga' => 'Sejahtera I',
            'jumlah_keluarga' => 25,
            'keterangan' => 'Rekap desa',
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('desa.data-keluarga.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_admin_kecamatan_dapat_mencetak_laporan_pdf_data_keluarga_kecamatannya_sendiri(): void
    {
        $user = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('admin-kecamatan');

        DataKeluarga::create([
            'kategori_keluarga' => 'Sejahtera II',
            'jumlah_keluarga' => 40,
            'keterangan' => 'Rekap kecamatan',
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('kecamatan.data-keluarga.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_laporan_pdf_data_keluarga_tetap_aman_saat_scope_metadata_tidak_sinkron(): void
    {
        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $this->kecamatanB->id,
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('admin-desa');

        $response = $this->actingAs($user)->get(route('desa.data-keluarga.report'));

        $response->assertStatus(403);
    }
}
