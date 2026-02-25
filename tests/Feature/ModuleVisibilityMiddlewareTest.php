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
            'kecamatan-pokja-i',
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

    public function test_kecamatan_pokja_i_dapat_akses_dan_menulis_buku_kegiatan_scope_sendiri(): void
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

    public function test_kecamatan_pokja_i_hanya_baca_pada_modul_desa_only(): void
    {
        $user = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $this->kecamatan->id,
        ]);
        $user->assignRole('kecamatan-pokja-i');

        $this->actingAs($user);

        $this->get('/kecamatan/data-warga')->assertOk();
        $this->get('/kecamatan/data-warga/create')->assertForbidden();
        $this->post('/kecamatan/data-warga', [])->assertForbidden();
    }
}
