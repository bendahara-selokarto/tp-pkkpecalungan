<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Models\Area;
use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Models\PilotProjectNaskahPelaporanReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PilotProjectNaskahPelaporanReportPrintTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_admin_desa_dapat_mencetak_pdf_naskah_desanya_sendiri(): void
    {
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $this->desaA->id]);
        $user->assignRole('admin-desa');

        PilotProjectNaskahPelaporanReport::create([
            'judul_laporan' => 'Naskah Desa',
            'dasar_pelaksanaan' => 'A',
            'pendahuluan' => 'B',
            'pelaksanaan_1' => '1',
            'pelaksanaan_2' => '2',
            'pelaksanaan_3' => '3',
            'pelaksanaan_4' => '4',
            'pelaksanaan_5' => '5',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('desa.pilot-project-naskah-pelaporan.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_admin_kecamatan_dapat_mencetak_pdf_naskah_kecamatannya_sendiri(): void
    {
        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $this->kecamatanA->id]);
        $user->assignRole('admin-kecamatan');

        PilotProjectNaskahPelaporanReport::create([
            'judul_laporan' => 'Naskah Kecamatan',
            'dasar_pelaksanaan' => 'A',
            'pendahuluan' => 'B',
            'pelaksanaan_1' => '1',
            'pelaksanaan_2' => '2',
            'pelaksanaan_3' => '3',
            'pelaksanaan_4' => '4',
            'pelaksanaan_5' => '5',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('kecamatan.pilot-project-naskah-pelaporan.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_pdf_naskah_tetap_aman_saat_scope_metadata_tidak_sinkron(): void
    {
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $this->kecamatanB->id]);
        $user->assignRole('admin-desa');

        $response = $this->actingAs($user)->get(route('desa.pilot-project-naskah-pelaporan.report'));

        $response->assertStatus(403);
    }
}
