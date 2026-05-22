<?php

namespace Tests\Feature;

use App\Domains\Wilayah\BukuTamu\Models\BukuTamu;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DesaBukuTamuTest extends TestCase
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
    public function admin_desa_dapat_crud_dan_list_buku_tamu_terbatas_pada_desanya_sendiri(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $adminDesa->assignRole('desa-sekretaris');

        BukuTamu::create([
            'visit_date' => '2026-02-26',
            'description' => 'Tamu desa A',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
        ]);

        BukuTamu::create([
            'visit_date' => '2026-02-26',
            'description' => 'Tamu desa B',
            'level' => 'desa',
            'area_id' => $this->desaB->id,
            'created_by' => $adminDesa->id,
        ]);

        $this->actingAs($adminDesa)->get('/desa/buku-tamu')
            ->assertOk()
            ->assertInertia(function (AssertableInertia $page): void {
                $page
                    ->component('Desa/BukuTamu/Index')
                    ->has('items.data', 1)
                    ->where('items.data.0.description', 'Tamu desa A')
                    ->where('items.total', 1)
                    ->where('filters.per_page', 10);
            });

        $file = \Illuminate\Http\UploadedFile::fake()->image('visit.jpg');

        $this->actingAs($adminDesa)->post('/desa/buku-tamu', [
            'visit_date' => '2026-02-27',
            'description' => 'Audiensi kegiatan bulanan.',
            'file' => $file,
        ])->assertStatus(302);

        $created = BukuTamu::query()
            ->where('description', 'Audiensi kegiatan bulanan.')
            ->firstOrFail();

        $this->assertDatabaseHas('buku_tamus', [
            'id' => $created->id,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $this->actingAs($adminDesa)->put(route('desa.buku-tamu.update', $created->id), [
            'visit_date' => '2026-02-27',
            'description' => 'Audiensi kegiatan lanjutan.',
        ])->assertStatus(302);

        $this->assertDatabaseHas('buku_tamus', [
            'id' => $created->id,
            'description' => 'Audiensi kegiatan lanjutan.',
        ]);

        $this->actingAs($adminDesa)->delete(route('desa.buku-tamu.destroy', $created->id))
            ->assertStatus(302);

        $this->assertDatabaseMissing('buku_tamus', ['id' => $created->id]);
    }

    #[Test]
    public function admin_desa_hanya_melihat_buku_tamu_pada_tahun_anggaran_aktif(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $adminDesa->assignRole('desa-sekretaris');

        BukuTamu::create([
            'visit_date' => '2026-02-26',
            'description' => 'Masuk list 2026',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
            'tahun_anggaran' => 2026,
        ]);

        BukuTamu::create([
            'visit_date' => '2025-02-26',
            'description' => 'Tidak boleh muncul',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
            'tahun_anggaran' => 2025,
        ]);

        $this->actingAs($adminDesa)->get('/desa/buku-tamu')
            ->assertOk()
            ->assertInertia(function (AssertableInertia $page): void {
                $page
                    ->where('items.total', 1)
                    ->where('items.data.0.description', 'Masuk list 2026')
                    ->where('filters.tahun_anggaran', 2026);
            });
    }

    #[Test]
    public function pengguna_non_admin_desa_tidak_bisa_mengakses_modul_buku_tamu_desa(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'kecamatan',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $adminKecamatan->assignRole('kecamatan-sekretaris');

        $this->actingAs($adminKecamatan)->get('/desa/buku-tamu')
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

        $this->actingAs($staleUser)->get('/desa/buku-tamu')
            ->assertStatus(403);
    }
}
