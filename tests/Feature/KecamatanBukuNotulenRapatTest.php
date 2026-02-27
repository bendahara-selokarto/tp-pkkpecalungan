<?php

namespace Tests\Feature;

use App\Domains\Wilayah\BukuNotulenRapat\Models\BukuNotulenRapat;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KecamatanBukuNotulenRapatTest extends TestCase
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
    public function admin_kecamatan_dapat_list_dan_crud_notulen_di_kecamatannya_sendiri(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        BukuNotulenRapat::create([
            'entry_date' => '2026-02-26',
            'title' => 'Rapat Kecamatan A',
            'person_name' => 'Sekretaris Kecamatan A',
            'institution' => 'TP PKK Kecamatan A',
            'description' => 'Notulen kecamatan A',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $adminKecamatan->id,
        ]);

        BukuNotulenRapat::create([
            'entry_date' => '2026-02-26',
            'title' => 'Rapat Kecamatan B',
            'person_name' => 'Sekretaris Kecamatan B',
            'institution' => 'TP PKK Kecamatan B',
            'description' => 'Notulen kecamatan B',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $this->actingAs($adminKecamatan)->get('/kecamatan/buku-notulen-rapat')
            ->assertOk()
            ->assertInertia(function (AssertableInertia $page): void {
                $page
                    ->component('Kecamatan/BukuNotulenRapat/Index')
                    ->has('items.data', 1)
                    ->where('items.data.0.title', 'Rapat Kecamatan A')
                    ->where('items.total', 1)
                    ->where('filters.per_page', 10);
            });

        $this->actingAs($adminKecamatan)->post('/kecamatan/buku-notulen-rapat', [
            'entry_date' => '2026-02-27',
            'title' => 'Rapat Evaluasi',
            'person_name' => 'Sekretaris Kecamatan',
            'institution' => 'TP PKK Kecamatan Pecalungan',
            'description' => 'Evaluasi triwulan.',
        ])->assertStatus(302);

        $created = BukuNotulenRapat::query()
            ->where('title', 'Rapat Evaluasi')
            ->firstOrFail();

        $this->actingAs($adminKecamatan)->put(route('kecamatan.buku-notulen-rapat.update', $created->id), [
            'entry_date' => '2026-02-27',
            'title' => 'Rapat Evaluasi Final',
            'person_name' => 'Sekretaris Kecamatan',
            'institution' => 'TP PKK Kecamatan Pecalungan',
            'description' => 'Evaluasi triwulan final.',
        ])->assertStatus(302);

        $this->assertDatabaseHas('buku_notulen_rapats', [
            'id' => $created->id,
            'title' => 'Rapat Evaluasi Final',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
        ]);

        $this->actingAs($adminKecamatan)->delete(route('kecamatan.buku-notulen-rapat.destroy', $created->id))
            ->assertStatus(302);

        $this->assertDatabaseMissing('buku_notulen_rapats', ['id' => $created->id]);
    }

    #[Test]
    public function pengguna_non_admin_kecamatan_tidak_bisa_mengakses_modul_notulen_kecamatan(): void
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

        $this->actingAs($adminDesa)->get('/kecamatan/buku-notulen-rapat')
            ->assertStatus(403);
    }

    #[Test]
    public function metadata_scope_stale_role_kecamatan_dengan_area_desa_ditolak(): void
    {
        $desa = Area::create([
            'name' => 'Sidorejo',
            'level' => 'desa',
            'parent_id' => $this->kecamatanA->id,
        ]);

        $staleUser = User::factory()->create([
            'area_id' => $desa->id,
            'scope' => 'kecamatan',
        ]);
        $staleUser->assignRole('admin-kecamatan');

        $this->actingAs($staleUser)->get('/kecamatan/buku-notulen-rapat')
            ->assertStatus(403);
    }
}
