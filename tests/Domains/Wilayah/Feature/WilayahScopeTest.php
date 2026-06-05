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

        Role::firstOrCreate(['name' => 'kecamatan-sekretaris']);
        Role::firstOrCreate(['name' => 'desa-sekretaris']);

        // Kecamatan
        $this->kecamatan = Area::create([
            'code'  => '1001',
            'name'  => 'Pecalungan',
            'level' => ScopeLevel::KECAMATAN->value
        ]);

        // Desa
        $this->desa1 = Area::create([
            'code'      => '2002',
            'name'      => 'Bandung',
            'level'     => ScopeLevel::DESA->value,
            'parent_id' => $this->kecamatan->id
        ]);

        $this->desa2 = Area::create([
            'code'      => '2003',
            'name'      => 'Gombong',
            'level'     => ScopeLevel::DESA->value,
            'parent_id' => $this->kecamatan->id
        ]);
    }

    #[Test]
    public function wilayah_canonical_pecalungan_menyediakan_10_desa()
    {
        $this->seed(\Database\Seeders\WilayahSeeder::class);

        $kecamatan = Area::query()
            ->where('code', '1001')
            ->where('level', ScopeLevel::KECAMATAN->value)
            ->first();

        $this->assertNotNull($kecamatan);
        $this->assertSame('Pecalungan', $kecamatan->name);
        $this->assertCount(10, $kecamatan->children);

        $codes = $kecamatan->children->pluck('code')->sort()->values()->all();

        $this->assertSame([
            '2001', '2002', '2003', '2004', '2005',
            '2006', '2007', '2008', '2009', '2010',
        ], $codes);
    }

    #[Test]
    public function pengguna_kecamatan_dapat_mengakses_semua_desa()
    {
        $user = User::factory()->create([
            'scope'   => ScopeLevel::KECAMATAN->value,
            'area_id' => $this->kecamatan->id,
        ]);
        $user->assignRole('kecamatan-sekretaris');

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
        $user->assignRole('desa-sekretaris');

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

