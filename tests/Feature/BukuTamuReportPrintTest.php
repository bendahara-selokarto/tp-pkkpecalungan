<?php

namespace Tests\Feature;

use App\Domains\Wilayah\BukuTamu\Models\BukuTamu;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\Feature\Concerns\AssertsPdfReportHeaders;
use Tests\TestCase;

class BukuTamuReportPrintTest extends TestCase
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

    public function test_header_kolom_pdf_buku_tamu_tetap_stabil(): void
    {
        $this->assertPdfReportHeadersInOrder('pdf.buku_tamu_report', [
            'NO',
            'TANGGAL',
            'NAMA TAMU',
            'KEPERLUAN',
            'INSTANSI',
            'KETERANGAN',
        ]);
    }

    public function test_admin_desa_dapat_mencetak_laporan_pdf_buku_tamu_desanya_sendiri(): void
    {
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $this->desaA->id]);
        $user->assignRole('admin-desa');

        BukuTamu::create([
            'visit_date' => '2026-02-27',
            'guest_name' => 'Siti Aminah',
            'purpose' => 'Konsultasi program',
            'institution' => 'TP PKK Desa Gombong',
            'description' => 'Kunjungan koordinasi bulanan.',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('desa.buku-tamu.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_admin_kecamatan_dapat_mencetak_laporan_pdf_buku_tamu_kecamatannya_sendiri(): void
    {
        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $this->kecamatanA->id]);
        $user->assignRole('admin-kecamatan');

        BukuTamu::create([
            'visit_date' => '2026-02-27',
            'guest_name' => 'Dewi Lestari',
            'purpose' => 'Koordinasi lintas desa',
            'institution' => 'TP PKK Kecamatan Pecalungan',
            'description' => 'Koordinasi program lintas desa.',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('kecamatan.buku-tamu.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_laporan_pdf_buku_tamu_tetap_aman_saat_role_dan_level_area_tidak_sinkron(): void
    {
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $this->kecamatanB->id]);
        $user->assignRole('admin-desa');

        $response = $this->actingAs($user)->get(route('desa.buku-tamu.report'));

        $response->assertStatus(403);
    }
}
