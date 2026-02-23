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
}

