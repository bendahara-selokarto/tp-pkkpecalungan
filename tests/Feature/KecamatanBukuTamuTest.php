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

class KecamatanBukuTamuTest extends TestCase
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
    public function admin_kecamatan_dapat_list_dan_crud_buku_tamu_di_kecamatannya_sendiri(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        BukuTamu::create([
            'visit_date' => '2026-02-26',
            'guest_name' => 'Dewi Lestari',
            'purpose' => 'Kunjungan monitoring',
            'institution' => 'TP PKK Kecamatan A',
            'description' => 'Tamu kecamatan A',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $adminKecamatan->id,
        ]);

        BukuTamu::create([
            'visit_date' => '2026-02-26',
            'guest_name' => 'Fajar Nugroho',
            'purpose' => 'Koordinasi kecamatan',
            'institution' => 'TP PKK Kecamatan B',
            'description' => 'Tamu kecamatan B',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $this->actingAs($adminKecamatan)->get('/kecamatan/buku-tamu')
            ->assertOk()
            ->assertInertia(function (AssertableInertia $page): void {
                $page
                    ->component('Kecamatan/BukuTamu/Index')
                    ->has('items.data', 1)
                    ->where('items.data.0.guest_name', 'Dewi Lestari')
                    ->where('items.total', 1)
                    ->where('filters.per_page', 10);
            });

        $this->actingAs($adminKecamatan)->post('/kecamatan/buku-tamu', [
            'visit_date' => '2026-02-27',
            'guest_name' => 'Slamet Riyadi',
            'purpose' => 'Verifikasi administrasi',
            'institution' => 'TP PKK Kecamatan Pecalungan',
            'description' => 'Verifikasi administrasi triwulan.',
        ])->assertStatus(302);

        $created = BukuTamu::query()
            ->where('guest_name', 'Slamet Riyadi')
            ->firstOrFail();

        $this->actingAs($adminKecamatan)->put(route('kecamatan.buku-tamu.update', $created->id), [
            'visit_date' => '2026-02-27',
            'guest_name' => 'Slamet Riyadi Final',
            'purpose' => 'Verifikasi administrasi lanjutan',
            'institution' => 'TP PKK Kecamatan Pecalungan',
            'description' => 'Verifikasi administrasi lanjutan.',
        ])->assertStatus(302);

        $this->assertDatabaseHas('buku_tamus', [
            'id' => $created->id,
            'guest_name' => 'Slamet Riyadi Final',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
        ]);

        $this->actingAs($adminKecamatan)->delete(route('kecamatan.buku-tamu.destroy', $created->id))
            ->assertStatus(302);

        $this->assertDatabaseMissing('buku_tamus', ['id' => $created->id]);
    }

    #[Test]
    public function pengguna_non_admin_kecamatan_tidak_bisa_mengakses_modul_buku_tamu_kecamatan(): void
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

        $this->actingAs($adminDesa)->get('/kecamatan/buku-tamu')
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

        $this->actingAs($staleUser)->get('/kecamatan/buku-tamu')
            ->assertStatus(403);
    }
}
