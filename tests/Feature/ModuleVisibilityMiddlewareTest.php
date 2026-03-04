<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ModuleVisibilityMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    private Area $kecamatan;

    private Area $desa;

    protected function setUp(): void
    {
        parent::setUp();

        foreach ([
            'desa-sekretaris',
            'kecamatan-sekretaris',
            'desa-pokja-i',
            'desa-pokja-ii',
            'desa-pokja-iii',
            'desa-pokja-iv',
            'kecamatan-pokja-i',
            'kecamatan-pokja-ii',
            'kecamatan-pokja-iii',
            'kecamatan-pokja-iv',
        ] as $roleName) {
            Role::create(['name' => $roleName]);
        }

        $this->kecamatan = Area::create([
            'name' => 'Pecalungan',
            'level' => 'kecamatan',
        ]);

        $this->desa = Area::create([
            'name' => 'Gombong',
            'level' => 'desa',
            'parent_id' => $this->kecamatan->id,
        ]);
    }

    public function test_desa_sekretaris_read_only_pada_modul_pokja(): void
    {
        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $this->desa->id,
        ]);
        $user->assignRole('desa-sekretaris');

        $this->actingAs($user);

        $this->get('/desa/data-warga')->assertOk();
        $this->get('/desa/data-warga/create')->assertForbidden();
        $this->post('/desa/data-warga', [])->assertForbidden();
    }

    public function test_desa_pokja_i_tidak_bisa_akses_modul_pokja_iii(): void
    {
        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $this->desa->id,
        ]);
        $user->assignRole('desa-pokja-i');

        $this->actingAs($user);

        $this->get('/desa/data-keluarga')->assertForbidden();
    }

    public function test_desa_pokja_i_dapat_akses_dan_menulis_buku_kegiatan_scope_sendiri(): void
    {
        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $this->desa->id,
        ]);
        $user->assignRole('desa-pokja-i');

        $this->actingAs($user);

        $this->get('/desa/activities')->assertOk();
        $this->get('/desa/activities/create')->assertOk();
        $this->post('/desa/activities', [
            'title' => 'Kegiatan Pokja I Desa',
            'activity_date' => '2026-02-24',
        ])->assertRedirect('/desa/activities');

        $this->assertDatabaseHas('activities', [
            'title' => 'Kegiatan Pokja I Desa',
            'level' => 'desa',
            'area_id' => $this->desa->id,
            'created_by' => $user->id,
        ]);
    }

    public function test_kecamatan_sekretaris_dapat_monitoring_dan_pokja_read_only(): void
    {
        $user = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $this->kecamatan->id,
        ]);
        $user->assignRole('kecamatan-sekretaris');

        $this->actingAs($user);

        $this->get('/kecamatan/desa-activities')->assertOk();
        $this->get('/kecamatan/data-warga')->assertOk();
        $this->get('/kecamatan/data-warga/create')->assertForbidden();
    }

    public function test_kecamatan_pokja_i_tidak_memiliki_menu_monitoring(): void
    {
        $user = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $this->kecamatan->id,
        ]);
        $user->assignRole('kecamatan-pokja-i');

        $this->actingAs($user);

        $this->get('/kecamatan/desa-activities')->assertForbidden();
    }

    public function test_semua_pokja_desa_memiliki_akses_rw_modul_inventaris(): void
    {
        foreach (['desa-pokja-i', 'desa-pokja-ii', 'desa-pokja-iii', 'desa-pokja-iv'] as $role) {
            $user = User::factory()->create([
                'scope' => 'desa',
                'area_id' => $this->desa->id,
            ]);
            $user->assignRole($role);

            $response = $this->actingAs($user)->post('/desa/inventaris', [
                'name' => 'Inventaris '.$role,
                'asal_barang' => 'Bantuan Desa',
                'tanggal_penerimaan' => '2026-03-04',
                'tempat_penyimpanan' => 'Gudang',
                'keterangan' => 'Uji akses RW inventaris',
                'quantity' => 1,
                'unit' => 'unit',
                'condition' => 'baik',
            ]);

            $response->assertStatus(302);
            $this->assertDatabaseHas('inventaris', [
                'name' => 'Inventaris '.$role,
                'level' => 'desa',
                'area_id' => $this->desa->id,
                'created_by' => $user->id,
            ]);
        }
    }

    public function test_semua_pokja_kecamatan_tetap_tidak_memiliki_akses_inventaris(): void
    {
        foreach (['kecamatan-pokja-i', 'kecamatan-pokja-ii', 'kecamatan-pokja-iii', 'kecamatan-pokja-iv'] as $role) {
            $user = User::factory()->create([
                'scope' => 'kecamatan',
                'area_id' => $this->kecamatan->id,
            ]);
            $user->assignRole($role);

            $this->actingAs($user)->get('/kecamatan/inventaris')->assertForbidden();
            $this->actingAs($user)->get('/kecamatan/inventaris/create')->assertForbidden();
            $this->actingAs($user)->post('/kecamatan/inventaris', [])->assertForbidden();
        }
    }

    public function test_kecamatan_pokja_i_dapat_akses_dan_menulis_buku_kegiatan_scope_kecamatan(): void
    {
        $user = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $this->kecamatan->id,
        ]);
        $user->assignRole('kecamatan-pokja-i');

        $this->actingAs($user);

        $this->get('/kecamatan/activities')->assertOk();
        $this->get('/kecamatan/activities/create')->assertOk();
        $this->post('/kecamatan/activities', [
            'title' => 'Kegiatan Pokja I Kecamatan',
            'activity_date' => '2026-02-24',
        ])->assertRedirect('/kecamatan/activities');

        $this->assertDatabaseHas('activities', [
            'title' => 'Kegiatan Pokja I Kecamatan',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatan->id,
            'created_by' => $user->id,
        ]);
    }

    public function test_kecamatan_pokja_i_tidak_memiliki_akses_modul_desa_only(): void
    {
        $user = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $this->kecamatan->id,
        ]);
        $user->assignRole('kecamatan-pokja-i');

        $this->actingAs($user);

        $this->get('/kecamatan/data-warga')->assertForbidden();
        $this->get('/kecamatan/data-warga/create')->assertForbidden();
        $this->post('/kecamatan/data-warga', [])->assertForbidden();
    }

    public function test_kecamatan_pokja_i_tetap_memiliki_modul_anggota_dan_prestasi_rw(): void
    {
        $user = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $this->kecamatan->id,
        ]);
        $user->assignRole('kecamatan-pokja-i');

        $this->actingAs($user);

        $this->get('/kecamatan/anggota-pokja')->assertOk();
        $this->get('/kecamatan/prestasi-lomba')->assertOk();
    }

    public function test_desa_sekretaris_memiliki_akses_rw_ke_program_prioritas(): void
    {
        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $this->desa->id,
        ]);
        $user->assignRole('desa-sekretaris');

        $this->actingAs($user);

        $this->get('/desa/program-prioritas')->assertOk();
        $this->get('/desa/program-prioritas/create')->assertOk();
    }

    public function test_desa_pokja_iv_tidak_memiliki_akses_program_prioritas(): void
    {
        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $this->desa->id,
        ]);
        $user->assignRole('desa-pokja-iv');

        $this->actingAs($user);

        $this->get('/desa/program-prioritas')->assertForbidden();
        $this->get('/desa/program-prioritas/create')->assertForbidden();
    }

    public function test_role_pokja_tidak_bisa_akses_modul_buku_sekretaris(): void
    {
        $desaPokja = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $this->desa->id,
        ]);
        $desaPokja->assignRole('desa-pokja-i');

        $this->actingAs($desaPokja);
        $this->get('/desa/buku-notulen-rapat')->assertForbidden();
        $this->get('/desa/buku-daftar-hadir')->assertForbidden();
        $this->get('/desa/buku-tamu')->assertForbidden();

        $kecamatanPokja = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $this->kecamatan->id,
        ]);
        $kecamatanPokja->assignRole('kecamatan-pokja-i');

        $this->actingAs($kecamatanPokja);
        $this->get('/kecamatan/buku-notulen-rapat')->assertForbidden();
        $this->get('/kecamatan/buku-daftar-hadir')->assertForbidden();
        $this->get('/kecamatan/buku-tamu')->assertForbidden();
    }
}
