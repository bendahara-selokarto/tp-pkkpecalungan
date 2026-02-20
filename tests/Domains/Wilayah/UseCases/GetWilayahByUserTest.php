<?php

namespace Tests\Domains\Wilayah\UseCases;
use PHPUnit\Framework\Attributes\Test;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Models\User;
use App\Domains\Wilayah\Models\Area;
use App\Domains\Wilayah\UseCases\GetWilayahByUser;
use Spatie\Permission\Models\Role;

class GetWilayahByUserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin-kecamatan']);
        Role::create(['name' => 'admin-desa']);
    }

    #[Test]
    public function usecase_mengembalikan_data_yang_benar_untuk_pengguna_kecamatan()
    {
        $kecamatan = Area::create([
            'name'  => 'Pecalungan',
            'level' => ScopeLevel::KECAMATAN->value
        ]);

        Area::create([
            'name'      => 'Bandung',
            'level'     => ScopeLevel::DESA->value,
            'parent_id' => $kecamatan->id
        ]);

        Area::create([
            'name'      => 'Gombong',
            'level'     => ScopeLevel::DESA->value,
            'parent_id' => $kecamatan->id
        ]);

        $user = User::factory()->create([
            'scope'   => ScopeLevel::KECAMATAN->value,
            'area_id' => $kecamatan->id,
        ]);
        $user->assignRole('admin-kecamatan');

        $usecase = app(GetWilayahByUser::class);

        $result = $usecase->handle($user);

        $this->assertCount(2, $result);
    }

    #[Test]
    public function usecase_mengembalikan_data_yang_benar_untuk_pengguna_desa()
    {
        $kecamatan = Area::create([
            'name'  => 'Pecalungan',
            'level' => ScopeLevel::KECAMATAN->value
        ]);

        $desa = Area::create([
            'name'      => 'Bandung',
            'level'     => ScopeLevel::DESA->value,
            'parent_id' => $kecamatan->id
        ]);

        Area::create([
            'name'      => 'Gombong',
            'level'     => ScopeLevel::DESA->value,
            'parent_id' => $kecamatan->id
        ]);

        $user = User::factory()->create([
            'scope'   => ScopeLevel::DESA->value,
            'area_id' => $desa->id,
        ]);
        $user->assignRole('admin-desa');

        $usecase = app(GetWilayahByUser::class);

        $result = $usecase->handle($user);

        $this->assertCount(1, $result);
        $this->assertEquals($desa->id, $result->first()->id);
    }

    #[Test]
    public function usecase_mengembalikan_kosong_jika_scope_metadata_tidak_sinkron_dengan_role_dan_level_area()
    {
        $kecamatan = Area::create([
            'name'  => 'Pecalungan',
            'level' => ScopeLevel::KECAMATAN->value,
        ]);

        Area::create([
            'name'      => 'Bandung',
            'level'     => ScopeLevel::DESA->value,
            'parent_id' => $kecamatan->id,
        ]);

        $user = User::factory()->create([
            // Metadata scope sengaja stale: kecamatan, tapi role hanya desa.
            'scope'   => ScopeLevel::KECAMATAN->value,
            'area_id' => $kecamatan->id,
        ]);
        $user->assignRole('admin-desa');

        $usecase = app(GetWilayahByUser::class);

        $result = $usecase->handle($user);

        $this->assertCount(0, $result);
    }
}


