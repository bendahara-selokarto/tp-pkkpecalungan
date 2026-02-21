<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Models\Area;
use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Models\PilotProjectNaskahPelaporanReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KecamatanPilotProjectNaskahPelaporanTest extends TestCase
{
    use RefreshDatabase;

    protected Area $kecamatanA;
    protected Area $kecamatanB;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin-kecamatan']);
        Role::create(['name' => 'admin-desa']);

        $this->kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $this->kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);
    }

    #[Test]
    public function admin_kecamatan_hanya_melihat_naskah_di_kecamatannya_sendiri(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        PilotProjectNaskahPelaporanReport::create([
            'judul_laporan' => 'Naskah Kecamatan A',
            'dasar_pelaksanaan' => 'A',
            'pendahuluan' => 'A',
            'pelaksanaan_1' => '1',
            'pelaksanaan_2' => '2',
            'pelaksanaan_3' => '3',
            'pelaksanaan_4' => '4',
            'pelaksanaan_5' => '5',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $adminKecamatan->id,
        ]);

        PilotProjectNaskahPelaporanReport::create([
            'judul_laporan' => 'Naskah Kecamatan B',
            'dasar_pelaksanaan' => 'B',
            'pendahuluan' => 'B',
            'pelaksanaan_1' => '1',
            'pelaksanaan_2' => '2',
            'pelaksanaan_3' => '3',
            'pelaksanaan_4' => '4',
            'pelaksanaan_5' => '5',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/pilot-project-naskah-pelaporan');

        $response->assertOk();
        $response->assertSee('Naskah Kecamatan A');
        $response->assertDontSee('Naskah Kecamatan B');
    }

    #[Test]
    public function pengguna_non_admin_kecamatan_tidak_bisa_mengakses_modul_naskah_kecamatan(): void
    {
        $desa = Area::create([
            'name' => 'Gombong',
            'level' => 'desa',
            'parent_id' => $this->kecamatanA->id,
        ]);

        $adminDesa = User::factory()->create([
            'area_id' => $desa->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        $response = $this->actingAs($adminDesa)->get('/kecamatan/pilot-project-naskah-pelaporan');

        $response->assertStatus(403);
    }
}
