<?php

namespace Tests\Feature;

use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Models\DataPemanfaatanTanahPekaranganHatinyaPkk;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\Feature\Concerns\AssertsPdfReportHeaders;
use Tests\TestCase;

class DataPemanfaatanTanahPekaranganHatinyaPkkReportPrintTest extends TestCase
{
    use RefreshDatabase;
    use AssertsPdfReportHeaders;

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

    public function test_header_kolom_pdf_data_pemanfaatan_tanah_pekarangan_hatinya_pkk_tetap_sesuai_pedoman(): void
    {
        $this->assertPdfReportHeadersInOrder('pdf.data_pemanfaatan_tanah_pekarangan_hatinya_pkk_report', [
            'NO',
            'KATEGORI JENIS PEMANFAATAN LAHAN',
            'KOMODITI DIBUDIDAYAKAN',
            'JUMLAH KOMODITI DIBUDIDAYAKAN',
        ]);
    }

    public function test_admin_desa_dapat_mencetak_laporan_pdf_data_pemanfaatan_tanah_pekarangan_hatinya_pkk_desanya_sendiri(): void
    {
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $this->desaA->id]);
        $user->assignRole('admin-desa');

        DataPemanfaatanTanahPekaranganHatinyaPkk::create([
            'kategori_pemanfaatan_lahan' => 'Peternakan',
            'komoditi' => 'Ayam',
            'jumlah_komoditi' => '25 ekor',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('desa.data-pemanfaatan-tanah-pekarangan-hatinya-pkk.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_admin_kecamatan_dapat_mencetak_laporan_pdf_data_pemanfaatan_tanah_pekarangan_hatinya_pkk_kecamatannya_sendiri(): void
    {
        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $this->kecamatanA->id]);
        $user->assignRole('admin-kecamatan');

        DataPemanfaatanTanahPekaranganHatinyaPkk::create([
            'kategori_pemanfaatan_lahan' => 'Perikanan',
            'komoditi' => 'Lele',
            'jumlah_komoditi' => '40 kolam',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('kecamatan.data-pemanfaatan-tanah-pekarangan-hatinya-pkk.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_laporan_pdf_data_pemanfaatan_tanah_pekarangan_hatinya_pkk_tetap_aman_saat_scope_metadata_tidak_sinkron(): void
    {
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $this->kecamatanB->id]);
        $user->assignRole('admin-desa');

        $response = $this->actingAs($user)->get(route('desa.data-pemanfaatan-tanah-pekarangan-hatinya-pkk.report'));

        $response->assertStatus(403);
    }
}


