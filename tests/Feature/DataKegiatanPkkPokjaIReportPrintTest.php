<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\Feature\Concerns\AssertsPdfReportHeaders;
use Tests\TestCase;

class DataKegiatanPkkPokjaIReportPrintTest extends TestCase
{
    use RefreshDatabase;
    use AssertsPdfReportHeaders;

    protected Area $kecamatanA;
    protected Area $kecamatanB;
    protected Area $desaA;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'desa-pokja-i']);
        Role::create(['name' => 'kecamatan-pokja-i']);
        Role::create(['name' => 'desa-pokja-ii']);

        $this->kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $this->kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);
        $this->desaA = Area::create([
            'name' => 'Gombong',
            'level' => 'desa',
            'parent_id' => $this->kecamatanA->id,
        ]);
    }

    public function test_header_kolom_pdf_data_kegiatan_pkk_pokja_i_tetap_sesuai_pedoman(): void
    {
        $this->assertPdfReportHeadersInOrder('pdf.data_kegiatan_pkk_pokja_i_report', [
            'NO',
            'NAMA WILAYAH',
            'JML KADER',
            'PENGHAYATAN DAN PENGAMALAN PANCASILA DAN GOTONG ROYONG',
            'KISAH',
            'KRISAN',
            'KILAS',
            'KTIAT',
            'KISAK',
            'PKBN',
        ]);
    }

    public function test_desa_pokja_i_dapat_mencetak_laporan_pdf_data_kegiatan_pkk_pokja_i_desanya_sendiri(): void
    {
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $this->desaA->id]);
        $user->assignRole('desa-pokja-i');

        $response = $this->actingAs($user)->get(route('desa.data-kegiatan-pkk-pokja-i.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_kecamatan_pokja_i_dapat_mencetak_laporan_pdf_data_kegiatan_pkk_pokja_i_kecamatannya_sendiri(): void
    {
        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $this->kecamatanA->id]);
        $user->assignRole('kecamatan-pokja-i');

        $response = $this->actingAs($user)->get(route('kecamatan.data-kegiatan-pkk-pokja-i.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_role_tidak_valid_ditolak_mencetak_laporan_pdf_data_kegiatan_pkk_pokja_i(): void
    {
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $this->desaA->id]);
        $user->assignRole('desa-pokja-ii');

        $response = $this->actingAs($user)->get(route('desa.data-kegiatan-pkk-pokja-i.report'));

        $response->assertStatus(403);
    }

    public function test_laporan_pdf_data_kegiatan_pkk_pokja_i_tetap_aman_saat_scope_metadata_tidak_sinkron(): void
    {
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $this->kecamatanB->id]);
        $user->assignRole('desa-pokja-i');

        $response = $this->actingAs($user)->get(route('desa.data-kegiatan-pkk-pokja-i.report'));

        $response->assertStatus(403);
    }
}
