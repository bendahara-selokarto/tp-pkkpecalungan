<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Models\Area;
use App\Domains\Wilayah\Paar\Models\Paar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KecamatanPaarTest extends TestCase
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
    public function test_admin_kecamatan_dapat_melihat_paar_di_kecamatannya_sendiri(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        Paar::create([
            'indikator' => 'akte_kelahiran',
            'jumlah' => 20,
            'keterangan' => 'Data kecamatan A',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $adminKecamatan->id,
        ]);

        Paar::create([
            'indikator' => 'kia',
            'jumlah' => 30,
            'keterangan' => 'Data kecamatan B',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/paar');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Kecamatan/Paar/Index')
                ->has('paarItems.data', 1)
                ->where('paarItems.data.0.indikator', 'akte_kelahiran')
                ->where('paarItems.total', 1)
                ->where('filters.per_page', 10);
        });
    }

    #[Test]
    public function daftar_paar_kecamatan_mendukung_pagination_dan_tetap_scoped(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        foreach (Paar::indicatorKeys() as $index => $indikator) {
            Paar::create([
                'indikator' => $indikator,
                'jumlah' => $index + 1,
                'keterangan' => 'Data ' . $index,
                'level' => 'kecamatan',
                'area_id' => $this->kecamatanA->id,
                'created_by' => $adminKecamatan->id,
            ]);
        }

        Paar::create([
            'indikator' => 'kia',
            'jumlah' => 99,
            'keterangan' => 'Data bocor',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/paar?page=2&per_page=10');

        $response->assertOk();
        $response->assertDontSee('Data bocor');
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Kecamatan/Paar/Index')
                ->has('paarItems.data', 0)
                ->where('paarItems.current_page', 2)
                ->where('paarItems.per_page', 10)
                ->where('paarItems.total', 6)
                ->where('filters.per_page', 10);
        });
    }

    #[Test]
    public function per_page_tidak_valid_di_paar_kecamatan_kembali_ke_default(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        Paar::create([
            'indikator' => 'akte_kelahiran',
            'jumlah' => 10,
            'keterangan' => null,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/paar?per_page=999');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Kecamatan/Paar/Index')
                ->where('filters.per_page', 10)
                ->where('paarItems.per_page', 10);
        });
    }

    #[Test]
    public function test_admin_kecamatan_tidak_bisa_melihat_detail_paar_kecamatan_lain(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $paarKecamatanLain = Paar::create([
            'indikator' => 'narkoba',
            'jumlah' => 5,
            'keterangan' => 'Data luar area',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get(route('kecamatan.paar.show', $paarKecamatanLain->id));

        $response->assertStatus(403);
    }

    #[Test]
    public function test_pengguna_non_admin_kecamatan_tidak_bisa_mengakses_modul_paar_kecamatan(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminDesa->assignRole('admin-desa');

        $response = $this->actingAs($adminDesa)->get('/kecamatan/paar');

        $response->assertStatus(403);
    }
}
