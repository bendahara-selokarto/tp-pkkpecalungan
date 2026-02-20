<?php

namespace Tests\Feature;

use App\Domains\Wilayah\WarungPkk\Models\WarungPkk;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KecamatanWarungPkkTest extends TestCase
{
    use RefreshDatabase;

    protected Area $kecamatanA;
    protected Area $kecamatanB;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin-kecamatan']);
        Role::create(['name' => 'admin-desa']);

        $this->kecamatanA = Area::create([
            'name' => 'Pecalungan',
            'level' => 'kecamatan',
        ]);

        $this->kecamatanB = Area::create([
            'name' => 'Limpung',
            'level' => 'kecamatan',
        ]);
    }

    #[Test]
    public function admin_kecamatan_dapat_melihat_warung_pkk_di_kecamatannya_sendiri(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        WarungPkk::create([
            'nama_warung_pkk' => 'Warung PKK Anyelir',
            'nama_pengelola' => 'Dewi Lestari',
            'komoditi' => 'Gula',
            'kategori' => 'Pangan',
            'volume' => '60 kg',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $adminKecamatan->id,
        ]);

        WarungPkk::create([
            'nama_warung_pkk' => 'Warung PKK Dahlia',
            'nama_pengelola' => 'Sri Handayani',
            'komoditi' => 'Sabun',
            'kategori' => 'Kebutuhan rumah tangga',
            'volume' => '90 pcs',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/warung-pkk');

        $response->assertOk();
        $response->assertSee('Warung PKK Anyelir');
        $response->assertDontSee('Warung PKK Dahlia');
    }

    #[Test]
    public function admin_kecamatan_tidak_bisa_melihat_detail_warung_pkk_kecamatan_lain(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $warungPkkLuar = WarungPkk::create([
            'nama_warung_pkk' => 'Warung PKK Luar Area',
            'nama_pengelola' => 'Santi',
            'komoditi' => 'Minyak goreng',
            'kategori' => 'Pangan',
            'volume' => '40 liter',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)
            ->get(route('kecamatan.warung-pkk.show', $warungPkkLuar->id));

        $response->assertStatus(403);
    }

    #[Test]
    public function pengguna_non_admin_kecamatan_tidak_bisa_mengakses_modul_warung_pkk_kecamatan(): void
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

        $response = $this->actingAs($adminDesa)->get('/kecamatan/warung-pkk');

        $response->assertStatus(403);
    }

    #[Test]
    public function pengguna_role_kecamatan_dengan_area_tidak_valid_tidak_bisa_mengakses_modul_warung_pkk_kecamatan(): void
    {
        $desa = Area::create([
            'name' => 'Bandung',
            'level' => 'desa',
            'parent_id' => $this->kecamatanA->id,
        ]);

        $userStale = User::factory()->create([
            'area_id' => $desa->id,
            'scope' => 'kecamatan',
        ]);
        $userStale->assignRole('admin-kecamatan');

        $response = $this->actingAs($userStale)->get('/kecamatan/warung-pkk');

        $response->assertStatus(403);
    }
}
