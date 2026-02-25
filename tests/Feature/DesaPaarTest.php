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

class DesaPaarTest extends TestCase
{
    use RefreshDatabase;

    protected Area $kecamatan;
    protected Area $desaA;
    protected Area $desaB;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin-desa']);
        Role::create(['name' => 'admin-kecamatan']);

        $this->kecamatan = Area::create([
            'name' => 'Pecalungan',
            'level' => 'kecamatan',
        ]);

        $this->desaA = Area::create([
            'name' => 'Gombong',
            'level' => 'desa',
            'parent_id' => $this->kecamatan->id,
        ]);

        $this->desaB = Area::create([
            'name' => 'Bandung',
            'level' => 'desa',
            'parent_id' => $this->kecamatan->id,
        ]);
    }

    #[Test]
    public function admin_desa_dapat_melihat_data_paar_di_desanya_sendiri(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        Paar::create([
            'indikator' => 'akte_kelahiran',
            'jumlah' => 12,
            'keterangan' => 'Data desa A',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
        ]);

        Paar::create([
            'indikator' => 'kia',
            'jumlah' => 8,
            'keterangan' => 'Data desa B',
            'level' => 'desa',
            'area_id' => $this->desaB->id,
            'created_by' => $adminDesa->id,
        ]);

        $response = $this->actingAs($adminDesa)->get('/desa/paar');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Desa/Paar/Index')
                ->has('paarItems.data', 1)
                ->where('paarItems.data.0.indikator', 'akte_kelahiran')
                ->where('paarItems.total', 1)
                ->where('filters.per_page', 10);
        });
    }

    #[Test]
    public function daftar_paar_desa_mendukung_pagination_dan_tetap_scoped(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        foreach (Paar::indicatorKeys() as $index => $indikator) {
            Paar::create([
                'indikator' => $indikator,
                'jumlah' => $index + 1,
                'keterangan' => 'Data ' . $index,
                'level' => 'desa',
                'area_id' => $this->desaA->id,
                'created_by' => $adminDesa->id,
            ]);
        }

        Paar::create([
            'indikator' => 'kia',
            'jumlah' => 99,
            'keterangan' => 'Data bocor',
            'level' => 'desa',
            'area_id' => $this->desaB->id,
            'created_by' => $adminDesa->id,
        ]);

        $response = $this->actingAs($adminDesa)->get('/desa/paar?page=2&per_page=10');

        $response->assertOk();
        $response->assertDontSee('Data bocor');
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Desa/Paar/Index')
                ->has('paarItems.data', 0)
                ->where('paarItems.current_page', 2)
                ->where('paarItems.per_page', 10)
                ->where('paarItems.total', 6)
                ->where('filters.per_page', 10);
        });
    }

    #[Test]
    public function per_page_tidak_valid_di_paar_desa_kembali_ke_default(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        Paar::create([
            'indikator' => 'akte_kelahiran',
            'jumlah' => 10,
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
        ]);

        $response = $this->actingAs($adminDesa)->get('/desa/paar?per_page=999');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Desa/Paar/Index')
                ->where('filters.per_page', 10)
                ->where('paarItems.per_page', 10);
        });
    }

    #[Test]
    public function admin_desa_dapat_menambah_memperbarui_dan_menghapus_data_paar(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        $this->actingAs($adminDesa)->post('/desa/paar', [
            'indikator' => 'akte_kelahiran',
            'jumlah' => 10,
            'keterangan' => 'Awal',
        ])->assertStatus(302);

        $paar = Paar::where('indikator', 'akte_kelahiran')->firstOrFail();

        $this->actingAs($adminDesa)->put(route('desa.paar.update', $paar->id), [
            'indikator' => 'akte_kelahiran',
            'jumlah' => 14,
            'keterangan' => 'Perbaruan',
        ])->assertStatus(302);

        $this->assertDatabaseHas('paars', [
            'id' => $paar->id,
            'jumlah' => 14,
            'keterangan' => 'Perbaruan',
        ]);

        $this->actingAs($adminDesa)->delete(route('desa.paar.destroy', $paar->id))
            ->assertStatus(302);

        $this->assertDatabaseMissing('paars', ['id' => $paar->id]);
    }

    #[Test]
    public function pengguna_non_admin_desa_tidak_bisa_mengakses_modul_paar_desa(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $response = $this->actingAs($adminKecamatan)->get('/desa/paar');

        $response->assertStatus(403);
    }
}
