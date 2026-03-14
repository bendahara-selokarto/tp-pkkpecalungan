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

class DesaBukuNotulenRapatTest extends TestCase
{
    use RefreshDatabase;

    private const ACTIVE_BUDGET_YEAR = 2026;

    protected Area $kecamatan;

    protected Area $desaA;

    protected Area $desaB;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'desa-sekretaris']);
        Role::firstOrCreate(['name' => 'kecamatan-sekretaris']);

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
    public function admin_desa_dapat_crud_dan_list_notulen_terbatas_pada_desanya_sendiri(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $adminDesa->assignRole('desa-sekretaris');

        BukuNotulenRapat::create([
            'entry_date' => '2026-02-26',
            'title' => 'Rapat Desa A',
            'person_name' => 'Sekretaris A',
            'institution' => 'TP PKK Desa A',
            'description' => 'Notulen desa A',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
        ]);

        BukuNotulenRapat::create([
            'entry_date' => '2026-02-26',
            'title' => 'Rapat Desa B',
            'person_name' => 'Sekretaris B',
            'institution' => 'TP PKK Desa B',
            'description' => 'Notulen desa B',
            'level' => 'desa',
            'area_id' => $this->desaB->id,
            'created_by' => $adminDesa->id,
        ]);

        $this->actingAs($adminDesa)->get('/desa/buku-notulen-rapat')
            ->assertOk()
            ->assertInertia(function (AssertableInertia $page): void {
                $page
                    ->component('Desa/BukuNotulenRapat/Index')
                    ->has('items.data', 1)
                    ->where('items.data.0.title', 'Rapat Desa A')
                    ->where('items.total', 1)
                    ->where('filters.per_page', 10);
            });

        $this->actingAs($adminDesa)->post('/desa/buku-notulen-rapat', [
            'entry_date' => '2026-02-27',
            'title' => 'Rapat Koordinasi',
            'person_name' => 'Ketua TP PKK',
            'institution' => 'TP PKK Desa Gombong',
            'description' => 'Pembahasan agenda bulanan.',
        ])->assertStatus(302);

        $created = BukuNotulenRapat::query()
            ->where('title', 'Rapat Koordinasi')
            ->firstOrFail();

        $this->assertDatabaseHas('buku_notulen_rapats', [
            'id' => $created->id,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $this->actingAs($adminDesa)->put(route('desa.buku-notulen-rapat.update', $created->id), [
            'entry_date' => '2026-02-27',
            'title' => 'Rapat Koordinasi Final',
            'person_name' => 'Ketua TP PKK',
            'institution' => 'TP PKK Desa Gombong',
            'description' => 'Pembahasan final agenda bulanan.',
        ])->assertStatus(302);

        $this->assertDatabaseHas('buku_notulen_rapats', [
            'id' => $created->id,
            'title' => 'Rapat Koordinasi Final',
            'description' => 'Pembahasan final agenda bulanan.',
        ]);

        $this->actingAs($adminDesa)->delete(route('desa.buku-notulen-rapat.destroy', $created->id))
            ->assertStatus(302);

        $this->assertDatabaseMissing('buku_notulen_rapats', ['id' => $created->id]);
    }

    #[Test]
    public function admin_desa_hanya_melihat_notulen_pada_tahun_anggaran_aktif(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $adminDesa->assignRole('desa-sekretaris');

        BukuNotulenRapat::create([
            'entry_date' => '2026-02-26',
            'title' => 'Rapat Tahun Aktif',
            'person_name' => 'Sekretaris A',
            'institution' => 'TP PKK Desa A',
            'description' => 'Masuk list',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
            'tahun_anggaran' => 2026,
        ]);

        BukuNotulenRapat::create([
            'entry_date' => '2025-02-26',
            'title' => 'Rapat Tahun Lama',
            'person_name' => 'Sekretaris A',
            'institution' => 'TP PKK Desa A',
            'description' => 'Tidak boleh muncul',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
            'tahun_anggaran' => 2025,
        ]);

        $this->actingAs($adminDesa)->get('/desa/buku-notulen-rapat')
            ->assertOk()
            ->assertInertia(function (AssertableInertia $page): void {
                $page
                    ->where('items.total', 1)
                    ->where('items.data.0.title', 'Rapat Tahun Aktif')
                    ->where('filters.tahun_anggaran', 2026);
            });
    }

    #[Test]
    public function pengguna_non_admin_desa_tidak_bisa_mengakses_modul_notulen_desa(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'kecamatan',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $adminKecamatan->assignRole('kecamatan-sekretaris');

        $this->actingAs($adminKecamatan)->get('/desa/buku-notulen-rapat')
            ->assertStatus(403);
    }

    #[Test]
    public function metadata_scope_stale_role_desa_dengan_area_kecamatan_ditolak(): void
    {
        $staleUser = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'desa',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $staleUser->assignRole('desa-sekretaris');

        $this->actingAs($staleUser)->get('/desa/buku-notulen-rapat')
            ->assertStatus(403);
    }
}
