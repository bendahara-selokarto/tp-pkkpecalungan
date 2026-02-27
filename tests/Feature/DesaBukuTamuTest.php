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
    public function admin_desa_dapat_crud_dan_list_buku_tamu_terbatas_pada_desanya_sendiri(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        BukuTamu::create([
            'visit_date' => '2026-02-26',
            'guest_name' => 'Siti Aminah',
            'purpose' => 'Konsultasi program',
            'institution' => 'TP PKK Desa A',
            'description' => 'Tamu desa A',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
        ]);

        BukuTamu::create([
            'visit_date' => '2026-02-26',
            'guest_name' => 'Budi Santoso',
            'purpose' => 'Koordinasi lintas desa',
            'institution' => 'TP PKK Desa B',
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
                    ->where('items.data.0.guest_name', 'Siti Aminah')
                    ->where('items.total', 1)
                    ->where('filters.per_page', 10);
            });

        $this->actingAs($adminDesa)->post('/desa/buku-tamu', [
            'visit_date' => '2026-02-27',
            'guest_name' => 'Nur Kholis',
            'purpose' => 'Audiensi kegiatan',
            'institution' => 'TP PKK Desa Gombong',
            'description' => 'Audiensi kegiatan bulanan.',
        ])->assertStatus(302);

        $created = BukuTamu::query()
            ->where('guest_name', 'Nur Kholis')
            ->firstOrFail();

        $this->assertDatabaseHas('buku_tamus', [
            'id' => $created->id,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
        ]);

        $this->actingAs($adminDesa)->put(route('desa.buku-tamu.update', $created->id), [
            'visit_date' => '2026-02-27',
            'guest_name' => 'Nur Kholis Final',
            'purpose' => 'Audiensi kegiatan lanjutan',
            'institution' => 'TP PKK Desa Gombong',
            'description' => 'Audiensi kegiatan lanjutan.',
        ])->assertStatus(302);

        $this->assertDatabaseHas('buku_tamus', [
            'id' => $created->id,
            'guest_name' => 'Nur Kholis Final',
            'description' => 'Audiensi kegiatan lanjutan.',
        ]);

        $this->actingAs($adminDesa)->delete(route('desa.buku-tamu.destroy', $created->id))
            ->assertStatus(302);

        $this->assertDatabaseMissing('buku_tamus', ['id' => $created->id]);
    }

    #[Test]
    public function pengguna_non_admin_desa_tidak_bisa_mengakses_modul_buku_tamu_desa(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $this->actingAs($adminKecamatan)->get('/desa/buku-tamu')
            ->assertStatus(403);
    }

    #[Test]
    public function metadata_scope_stale_role_desa_dengan_area_kecamatan_ditolak(): void
    {
        $staleUser = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'desa',
        ]);
        $staleUser->assignRole('admin-desa');

        $this->actingAs($staleUser)->get('/desa/buku-tamu')
            ->assertStatus(403);
    }
}
