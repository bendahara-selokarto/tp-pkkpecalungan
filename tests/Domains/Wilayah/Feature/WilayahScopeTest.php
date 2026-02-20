<?php

namespace Tests\Domains\Wilayah\Feature;
use PHPUnit\Framework\Attributes\Test;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Models\User;
use App\Domains\Wilayah\Models\Area;
use Spatie\Permission\Models\Role;

class WilayahScopeTest extends TestCase
{
    use RefreshDatabase;

    protected Area $kecamatan;
    protected Area $desa1;
    protected Area $desa2;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin-kecamatan']);
        Role::create(['name' => 'admin-desa']);

        // Kecamatan
        $this->kecamatan = Area::create([
            'name'  => 'Pecalungan',
            'level' => ScopeLevel::KECAMATAN->value
        ]);

        // Desa
        $this->desa1 = Area::create([
            'name'      => 'Bandung',
            'level'     => ScopeLevel::DESA->value,
            'parent_id' => $this->kecamatan->id
        ]);

        $this->desa2 = Area::create([
            'name'      => 'Gombong',
            'level'     => ScopeLevel::DESA->value,
            'parent_id' => $this->kecamatan->id
        ]);
    }

    #[Test]
    public function pengguna_kecamatan_dapat_mengakses_semua_desa()
    {
        $user = User::factory()->create([
            'scope'   => ScopeLevel::KECAMATAN->value,
            'area_id' => $this->kecamatan->id,
        ]);
        $user->assignRole('admin-kecamatan');

        $this->actingAs($user);

        $areas = app('App\Domains\Wilayah\Repositories\AreaRepositoryInterface')
                    ->getByUser($user);

        $this->assertCount(2, $areas);
    }

    #[Test]
    public function pengguna_desa_hanya_dapat_mengakses_desanya_sendiri()
    {
        $user = User::factory()->create([
            'scope'   => ScopeLevel::DESA->value,
            'area_id' => $this->desa1->id,
        ]);
        $user->assignRole('admin-desa');

        $this->actingAs($user);

        $areas = app('App\Domains\Wilayah\Repositories\AreaRepositoryInterface')
                    ->getByUser($user);

        $this->assertCount(1, $areas);

        $this->assertEquals(
            $this->desa1->id,
            $areas->first()->id
        );
    }
}


