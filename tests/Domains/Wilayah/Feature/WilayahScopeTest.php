<?php

namespace Tests\Domains\Wilayah\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\User;
use App\Domains\Wilayah\Models\Area;

class WilayahScopeTest extends TestCase
{
    use RefreshDatabase;

    protected Area $kecamatan;
    protected Area $desa1;
    protected Area $desa2;

    protected function setUp(): void
    {
        parent::setUp();

        // Kecamatan
        $this->kecamatan = Area::create([
            'name'  => 'Pecalungan',
            'level' => 'kecamatan'
        ]);

        // Desa
        $this->desa1 = Area::create([
            'name'      => 'Bandung',
            'level'     => 'desa',
            'parent_id' => $this->kecamatan->id
        ]);

        $this->desa2 = Area::create([
            'name'      => 'Gombong',
            'level'     => 'desa',
            'parent_id' => $this->kecamatan->id
        ]);
    }

    /** @test */
    public function pengguna_kecamatan_dapat_mengakses_semua_desa()
    {
        $user = User::factory()->create([
            'scope'   => 'kecamatan',
            'area_id' => $this->kecamatan->id,
        ]);

        $this->actingAs($user);

        $areas = app('App\Domains\Wilayah\Repositories\AreaRepositoryInterface')
                    ->getByUser($user);

        $this->assertCount(2, $areas);
    }

    /** @test */
    public function pengguna_desa_hanya_dapat_mengakses_desanya_sendiri()
    {
        $user = User::factory()->create([
            'scope'   => 'desa',
            'area_id' => $this->desa1->id,
        ]);

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

