<?php

namespace Tests\Feature;
use PHPUnit\Framework\Attributes\Test;

use App\Domains\Wilayah\Inventaris\Models\Inventaris;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KecamatanInventarisTest extends TestCase
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
    public function admin_kecamatan_dapat_melihat_daftar_inventaris_di_kecamatannya_sendiri()
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        Inventaris::create([
            'name' => 'Kursi Aula Kecamatan',
            'description' => 'Aset kecamatan A',
            'quantity' => 25,
            'unit' => 'buah',
            'condition' => 'baik',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $adminKecamatan->id,
        ]);

        Inventaris::create([
            'name' => 'Printer Kecamatan B',
            'description' => 'Aset kecamatan B',
            'quantity' => 1,
            'unit' => 'unit',
            'condition' => 'baik',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/inventaris');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Kecamatan/Inventaris/Index')
                ->has('inventaris.data', 1)
                ->where('inventaris.data.0.name', 'Kursi Aula Kecamatan')
                ->where('inventaris.total', 1)
                ->where('filters.per_page', 10);
        });
    }

    #[Test]
    public function daftar_inventaris_kecamatan_mendukung_pagination_dan_tetap_scoped()
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        for ($index = 1; $index <= 11; $index++) {
            Inventaris::create([
                'name' => 'Inventaris Kec ' . $index,
                'description' => 'Aset ' . $index,
                'quantity' => $index,
                'unit' => 'buah',
                'condition' => 'baik',
                'level' => 'kecamatan',
                'area_id' => $this->kecamatanA->id,
                'created_by' => $adminKecamatan->id,
            ]);
        }

        Inventaris::create([
            'name' => 'Inventaris Bocor',
            'description' => 'Aset bocor',
            'quantity' => 1,
            'unit' => 'buah',
            'condition' => 'baik',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/inventaris?page=2&per_page=10');

        $response->assertOk();
        $response->assertDontSee('Inventaris Bocor');
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Kecamatan/Inventaris/Index')
                ->has('inventaris.data', 1)
                ->where('inventaris.current_page', 2)
                ->where('inventaris.per_page', 10)
                ->where('inventaris.total', 11)
                ->where('filters.per_page', 10);
        });
    }

    #[Test]
    public function per_page_tidak_valid_di_inventaris_kecamatan_kembali_ke_default()
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        Inventaris::create([
            'name' => 'Inventaris Default',
            'description' => 'Aset default',
            'quantity' => 1,
            'unit' => 'buah',
            'condition' => 'baik',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/inventaris?per_page=999');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Kecamatan/Inventaris/Index')
                ->where('filters.per_page', 10)
                ->where('inventaris.per_page', 10);
        });
    }

    #[Test]
    public function admin_kecamatan_dapat_menambah_memperbarui_dan_menghapus_inventarisnya()
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $this->actingAs($adminKecamatan)->post('/kecamatan/inventaris', [
            'name' => 'Laptop Operasional',
            'description' => 'Untuk pelayanan',
            'quantity' => 3,
            'unit' => 'unit',
            'condition' => 'baik',
        ])->assertStatus(302);

        $inventaris = Inventaris::where('name', 'Laptop Operasional')->firstOrFail();

        $this->actingAs($adminKecamatan)->put(route('kecamatan.inventaris.update', $inventaris->id), [
            'name' => 'Laptop Operasional',
            'description' => 'Untuk pelayanan publik',
            'quantity' => 4,
            'unit' => 'unit',
            'condition' => 'rusak_ringan',
        ])->assertStatus(302);

        $this->assertDatabaseHas('inventaris', [
            'id' => $inventaris->id,
            'description' => 'Untuk pelayanan publik',
            'quantity' => 4,
            'condition' => 'rusak_ringan',
        ]);

        $this->actingAs($adminKecamatan)->delete(route('kecamatan.inventaris.destroy', $inventaris->id))
            ->assertStatus(302);

        $this->assertDatabaseMissing('inventaris', ['id' => $inventaris->id]);
    }

    #[Test]
    public function admin_kecamatan_tidak_bisa_melihat_detail_inventaris_kecamatan_lain()
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $inventarisLuar = Inventaris::create([
            'name' => 'Generator Set',
            'description' => 'Aset kecamatan lain',
            'quantity' => 1,
            'unit' => 'unit',
            'condition' => 'baik',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get(route('kecamatan.inventaris.show', $inventarisLuar->id));

        $response->assertStatus(403);
    }

    #[Test]
    public function pengguna_non_admin_kecamatan_tidak_bisa_mengakses_modul_inventaris_kecamatan()
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

        $response = $this->actingAs($adminDesa)->get('/kecamatan/inventaris');

        $response->assertStatus(403);
    }
}

