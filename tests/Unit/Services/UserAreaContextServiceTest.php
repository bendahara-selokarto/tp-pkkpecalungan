<?php

namespace Tests\Unit\Services;

use App\Domains\Wilayah\Models\Area;
use App\Domains\Wilayah\Repositories\AreaRepositoryInterface;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class UserAreaContextServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_resolve_user_area_level_memakai_relasi_area_yang_sudah_dimuat(): void
    {
        $areaRepository = Mockery::mock(AreaRepositoryInterface::class);
        $areaRepository->shouldNotReceive('getLevelById');

        $service = new UserAreaContextService($areaRepository);

        $user = User::factory()->make(['area_id' => 321]);
        $user->setRelation('area', new Area([
            'id' => 321,
            'name' => 'Area Uji',
            'level' => 'desa',
        ]));

        $this->assertSame('desa', $service->resolveUserAreaLevel($user));
    }

    public function test_resolve_user_area_level_fallback_ke_repository_jika_relasi_area_belum_dimuat(): void
    {
        $areaRepository = Mockery::mock(AreaRepositoryInterface::class);
        $areaRepository
            ->shouldReceive('getLevelById')
            ->once()
            ->with(123)
            ->andReturn('kecamatan');

        $service = new UserAreaContextService($areaRepository);

        $user = User::factory()->make(['area_id' => 123]);

        $this->assertSame('kecamatan', $service->resolveUserAreaLevel($user));
    }

    public function test_require_user_area_id_melempar_403_jika_user_tidak_memiliki_area_id(): void
    {
        $areaRepository = Mockery::mock(AreaRepositoryInterface::class);
        $service = new UserAreaContextService($areaRepository);

        $user = User::factory()->create(['area_id' => null]);
        $this->actingAs($user);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Area pengguna belum ditentukan.');

        $service->requireUserAreaId();
    }

    public function test_require_user_area_id_mengembalikan_integer_saat_tersedia(): void
    {
        $areaRepository = Mockery::mock(AreaRepositoryInterface::class);
        $service = new UserAreaContextService($areaRepository);

        $area = Area::create([
            'name' => 'Pecalungan',
            'level' => 'kecamatan',
        ]);
        $user = User::factory()->create(['area_id' => $area->id]);
        $this->actingAs($user);

        $this->assertSame($area->id, $service->requireUserAreaId());
    }
}
