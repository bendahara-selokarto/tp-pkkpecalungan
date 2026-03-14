<?php

namespace Tests\Feature;

use App\Domains\Wilayah\AnggotaTimPenggerak\Models\AnggotaTimPenggerak;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\Feature\Concerns\AssertsPdfReportHeaders;
use Tests\TestCase;

class AnggotaTimPenggerakReportPrintTest extends TestCase
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

    public function test_header_kolom_pdf_anggota_tim_penggerak_tetap_stabil(): void
    {
        $this->assertPdfReportHeadersInOrder('pdf.anggota_tim_penggerak_report', [
            'NO',
            'NAMA',
            'JABATAN',
            'JENIS KELAMIN (L/P)',
            'TEMPAT LAHIR',
            'TG/BL/TH.LAHIR/UMUR',
            'STATUS',
            'ALAMAT',
            'PENDIDIKAN',
            'PEKERJAAN',
            'KET',
        ]);
    }

    public function test_admin_desa_dapat_mencetak_laporan_pdf_anggota_tim_penggerak_desanya_sendiri(): void
    {
        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $this->desaA->id,
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('desa-sekretaris');

        AnggotaTimPenggerak::create([
            'nama' => 'Anggota Desa',
            'jabatan' => 'Ketua',
            'jenis_kelamin' => 'P',
            'tempat_lahir' => 'Batang',
            'tanggal_lahir' => '1995-01-01',
            'status_perkawinan' => 'kawin',
            'alamat' => 'Jl. Desa',
            'pendidikan' => 'SMA',
            'pekerjaan' => 'Ibu Rumah Tangga',
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $response = $this->actingAs($user)->get(route('desa.anggota-tim-penggerak.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_admin_kecamatan_dapat_mencetak_laporan_pdf_anggota_tim_penggerak_kecamatannya_sendiri(): void
    {
        $user = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('kecamatan-sekretaris');

        AnggotaTimPenggerak::create([
            'nama' => 'Anggota Kecamatan',
            'jabatan' => 'Bendahara',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Batang',
            'tanggal_lahir' => '1990-01-01',
            'status_perkawinan' => 'kawin',
            'alamat' => 'Jl. Kecamatan',
            'pendidikan' => 'S1',
            'pekerjaan' => 'PNS',
            'keterangan' => null,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $response = $this->actingAs($user)->get(route('kecamatan.anggota-tim-penggerak.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_laporan_pdf_anggota_tim_penggerak_tetap_aman_saat_scope_metadata_tidak_sinkron(): void
    {
        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $this->kecamatanB->id,
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('desa-sekretaris');

        $response = $this->actingAs($user)->get(route('desa.anggota-tim-penggerak.report'));

        $response->assertStatus(403);
    }
}
