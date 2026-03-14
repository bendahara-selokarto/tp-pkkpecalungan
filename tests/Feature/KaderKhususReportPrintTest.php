<?php

namespace Tests\Feature;

use App\Domains\Wilayah\KaderKhusus\Models\KaderKhusus;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\Feature\Concerns\AssertsPdfReportHeaders;
use Tests\TestCase;

class KaderKhususReportPrintTest extends TestCase
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

        Role::firstOrCreate(['name' => 'desa-pokja-i']);
        Role::firstOrCreate(['name' => 'kecamatan-sekretaris']);

        $this->kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $this->kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);
        $this->desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $this->kecamatanA->id]);
    }

    public function test_header_kolom_pdf_kader_khusus_tetap_stabil(): void
    {
        $this->assertPdfReportHeadersInOrder('pdf.kader_khusus_report', [
            'NO',
            'NAMA',
            'JENIS KELAMIN',
            'TEMPAT TANGGAL LAHIR',
            'STATUS',
            'ALAMAT',
            'PENDIDIKAN',
            'JENIS KADER KHUSUS',
            'KETERANGAN',
            'L',
            'P',
            'NIKAH',
            'BLM NIKAH',
        ]);
    }

    public function test_admin_desa_dapat_mencetak_laporan_pdf_kader_khusus_desanya_sendiri(): void
    {
        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $this->desaA->id,
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('desa-pokja-i');

        KaderKhusus::create([
            'nama' => 'Kader Desa',
            'jenis_kelamin' => 'P',
            'tempat_lahir' => 'Batang',
            'tanggal_lahir' => '1995-01-01',
            'status_perkawinan' => 'kawin',
            'alamat' => 'Jl. Desa',
            'pendidikan' => 'SMA',
            'jenis_kader_khusus' => 'Kader Lansia',
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $response = $this->actingAs($user)->get(route('desa.kader-khusus.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_admin_kecamatan_dapat_mencetak_laporan_pdf_kader_khusus_kecamatannya_sendiri(): void
    {
        $user = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('kecamatan-sekretaris');

        KaderKhusus::create([
            'nama' => 'Kader Kecamatan',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Batang',
            'tanggal_lahir' => '1990-01-01',
            'status_perkawinan' => 'kawin',
            'alamat' => 'Jl. Kecamatan',
            'pendidikan' => 'S1',
            'jenis_kader_khusus' => 'Kader Disabilitas',
            'keterangan' => null,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $response = $this->actingAs($user)->get(route('kecamatan.kader-khusus.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_laporan_pdf_kader_khusus_tetap_aman_saat_scope_metadata_tidak_sinkron(): void
    {
        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $this->kecamatanB->id,
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('desa-pokja-i');

        $response = $this->actingAs($user)->get(route('desa.kader-khusus.report'));

        $response->assertStatus(403);
    }
}
