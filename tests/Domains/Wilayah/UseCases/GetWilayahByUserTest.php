<?php

namespace Tests\Domains\Wilayah\UseCases;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\User;
use App\Domains\Wilayah\Models\Area;
use App\Domains\Wilayah\UseCases\GetWilayahByUser;

class GetWilayahByUserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function usecase_mengembalikan_data_yang_benar_untuk_pengguna_kecamatan()
    {
        $kecamatan = Area::create([
            'name'  => 'Pecalungan',
            'level' => 'kecamatan'
        ]);

        Area::create([
            'name'      => 'Bandung',
            'level'     => 'desa',
            'parent_id' => $kecamatan->id
        ]);

        Area::create([
            'name'      => 'Gombong',
            'level'     => 'desa',
            'parent_id' => $kecamatan->id
        ]);

        $user = User::factory()->create([
            'scope'   => 'kecamatan',
            'area_id' => $kecamatan->id,
        ]);

        $usecase = app(GetWilayahByUser::class);

        $result = $usecase->handle($user);

        $this->assertCount(2, $result);
    }

    /** @test */
    public function usecase_mengembalikan_data_yang_benar_untuk_pengguna_desa()
    {
        $kecamatan = Area::create([
            'name'  => 'Pecalungan',
            'level' => 'kecamatan'
        ]);

        $desa = Area::create([
            'name'      => 'Bandung',
            'level'     => 'desa',
            'parent_id' => $kecamatan->id
        ]);

        Area::create([
            'name'      => 'Gombong',
            'level'     => 'desa',
            'parent_id' => $kecamatan->id
        ]);

        $user = User::factory()->create([
            'scope'   => 'desa',
            'area_id' => $desa->id,
        ]);

        $usecase = app(GetWilayahByUser::class);

        $result = $usecase->handle($user);

        $this->assertCount(1, $result);
        $this->assertEquals($desa->id, $result->first()->id);
    }
}

