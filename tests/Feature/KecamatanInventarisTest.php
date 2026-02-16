<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Inventaris\Models\Inventaris;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    /** @test */
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
        $response->assertSee('Kursi Aula Kecamatan');
        $response->assertDontSee('Printer Kecamatan B');
    }

    /** @test */
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

    /** @test */
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

    /** @test */
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
