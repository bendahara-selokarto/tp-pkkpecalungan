<?php

namespace Tests\Feature;

use App\Domains\Wilayah\WarungPkk\Models\WarungPkk;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WarungPkkReportPrintTest extends TestCase
{
    use RefreshDatabase;

    private const ACTIVE_BUDGET_YEAR = 2026;

    protected Area $kecamatanA;
    protected Area $kecamatanB;
    protected Area $desaA;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'desa-pokja-iii']);
        Role::firstOrCreate(['name' => 'kecamatan-sekretaris']);

        $this->kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $this->kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);
        $this->desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $this->kecamatanA->id]);
    }

    public function test_admin_desa_dapat_mencetak_laporan_pdf_warung_pkk_desanya_sendiri(): void
    {
        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $this->desaA->id,
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('desa-pokja-iii');

        WarungPkk::create([
            'nama_warung_pkk' => 'Warung PKK Mawar',
            'nama_pengelola' => 'Siti Aminah',
            'komoditi' => 'Beras',
            'kategori' => 'Pangan',
            'volume' => '100 kg',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $response = $this->actingAs($user)->get(route('desa.warung-pkk.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_admin_kecamatan_dapat_mencetak_laporan_pdf_warung_pkk_kecamatannya_sendiri(): void
    {
        $user = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('kecamatan-sekretaris');

        WarungPkk::create([
            'nama_warung_pkk' => 'Warung PKK Melati',
            'nama_pengelola' => 'Rina Wati',
            'komoditi' => 'Sabun',
            'kategori' => 'Kebutuhan rumah tangga',
            'volume' => '120 pcs',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $response = $this->actingAs($user)->get(route('kecamatan.warung-pkk.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_laporan_pdf_warung_pkk_tetap_aman_saat_scope_metadata_tidak_sinkron(): void
    {
        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $this->kecamatanB->id,
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('desa-pokja-iii');

        $response = $this->actingAs($user)->get(route('desa.warung-pkk.report'));

        $response->assertStatus(403);
    }
}
