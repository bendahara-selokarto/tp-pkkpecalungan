<?php

namespace Tests\Unit\UseCases;

use App\Domains\Wilayah\Dashboard\Repositories\DashboardGroupCoverageRepositoryInterface;
use App\Domains\Wilayah\Dashboard\UseCases\BuildDashboardBlockDetailWidgetUseCase;
use App\Domains\Wilayah\Services\RoleMenuVisibilityService;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use Mockery;
use Tests\TestCase;

class BuildDashboardBlockDetailWidgetUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_execute_menolak_scope_non_kecamatan(): void
    {
        $repository = Mockery::mock(DashboardGroupCoverageRepositoryInterface::class);
        $repository->shouldNotReceive('buildBreakdownByDesaForModules');
        $repository->shouldNotReceive('buildBreakdownByDesaForGroup');

        $roleMenuVisibilityService = Mockery::mock(RoleMenuVisibilityService::class);
        $roleMenuVisibilityService->shouldNotReceive('resolveForScope');
        $roleMenuVisibilityService->shouldNotReceive('modulesForGroup');

        $userAreaContextService = Mockery::mock(UserAreaContextService::class);
        $userAreaContextService
            ->shouldReceive('resolveEffectiveScope')
            ->once()
            ->andReturn('desa');

        $useCase = new BuildDashboardBlockDetailWidgetUseCase(
            $repository,
            $roleMenuVisibilityService,
            $userAreaContextService
        );

        $payload = $useCase->execute(User::factory()->make(), 'documents-pokja-i-kecamatan-desa-breakdown');

        $this->assertNull($payload);
    }

    public function test_execute_mengembalikan_rincian_group_dashboard_yang_didukung(): void
    {
        $repository = Mockery::mock(DashboardGroupCoverageRepositoryInterface::class);
        $repository
            ->shouldReceive('buildBreakdownByDesaForGroup')
            ->once()
            ->with(Mockery::type(User::class), 'pokja-i')
            ->andReturn([
                [
                    'desa_id' => 11,
                    'desa_name' => 'Gombong',
                    'total' => 3,
                    'per_module' => [
                        'data-warga' => 2,
                        'catatan-keluarga' => 1,
                    ],
                ],
            ]);
        $repository->shouldNotReceive('buildBreakdownByDesaForModules');

        $roleMenuVisibilityService = Mockery::mock(RoleMenuVisibilityService::class);
        $roleMenuVisibilityService
            ->shouldReceive('resolveForScope')
            ->once()
            ->with(Mockery::type(User::class), 'kecamatan')
            ->andReturn([
                'groups' => [
                    'pokja-i' => RoleMenuVisibilityService::MODE_READ_WRITE,
                ],
            ]);
        $roleMenuVisibilityService
            ->shouldReceive('modulesForGroup')
            ->once()
            ->with('pokja-i')
            ->andReturn(['data-warga', 'catatan-keluarga']);

        $userAreaContextService = Mockery::mock(UserAreaContextService::class);
        $userAreaContextService
            ->shouldReceive('resolveEffectiveScope')
            ->once()
            ->andReturn('kecamatan');

        $useCase = new BuildDashboardBlockDetailWidgetUseCase(
            $repository,
            $roleMenuVisibilityService,
            $userAreaContextService
        );

        $payload = $useCase->execute(User::factory()->make(), 'documents-pokja-i-kecamatan-desa-breakdown');

        $this->assertSame('documents-pokja-i-kecamatan-desa-breakdown', $payload['key']);
        $this->assertSame(['data-warga', 'catatan-keluarga'], $payload['tracked_modules']);
        $this->assertSame([
            [
                'slug' => 'desa-11',
                'label' => 'Gombong',
                'total' => 3,
                'per_module' => [
                    'data-warga' => 2,
                    'catatan-keluarga' => 1,
                ],
            ],
        ], $payload['items']);
    }

    public function test_execute_menggunakan_kontrak_khusus_section4_saat_block_pokja_i_desa_breakdown_diminta(): void
    {
        $repository = Mockery::mock(DashboardGroupCoverageRepositoryInterface::class);
        $repository
            ->shouldReceive('buildBreakdownByDesaForModules')
            ->once()
            ->with(
                Mockery::type(User::class),
                ['data-warga', 'data-kegiatan-warga', 'bkl', 'bkr', 'paar']
            )
            ->andReturn([
                [
                    'desa_id' => 12,
                    'desa_name' => 'Karangasem',
                    'total' => 5,
                    'per_module' => [
                        'data-warga' => 2,
                        'paar' => 3,
                    ],
                ],
            ]);
        $repository->shouldNotReceive('buildBreakdownByDesaForGroup');

        $roleMenuVisibilityService = Mockery::mock(RoleMenuVisibilityService::class);
        $roleMenuVisibilityService
            ->shouldReceive('resolveForScope')
            ->once()
            ->with(Mockery::type(User::class), 'kecamatan')
            ->andReturn([
                'groups' => [
                    'sekretaris-tpk' => RoleMenuVisibilityService::MODE_READ_WRITE,
                    'pokja-i' => RoleMenuVisibilityService::MODE_READ_ONLY,
                ],
            ]);
        $roleMenuVisibilityService->shouldNotReceive('modulesForGroup');

        $userAreaContextService = Mockery::mock(UserAreaContextService::class);
        $userAreaContextService
            ->shouldReceive('resolveEffectiveScope')
            ->once()
            ->andReturn('kecamatan');

        $useCase = new BuildDashboardBlockDetailWidgetUseCase(
            $repository,
            $roleMenuVisibilityService,
            $userAreaContextService
        );

        $payload = $useCase->execute(User::factory()->make(), 'documents-pokja-i-desa-breakdown');

        $this->assertSame('documents-pokja-i-desa-breakdown', $payload['key']);
        $this->assertSame(
            ['data-warga', 'data-kegiatan-warga', 'bkl', 'bkr', 'paar'],
            $payload['tracked_modules']
        );
        $this->assertSame('desa-12', $payload['items'][0]['slug']);
        $this->assertSame('Karangasem', $payload['items'][0]['label']);
        $this->assertSame(5, $payload['items'][0]['total']);
        $this->assertSame(['data-warga' => 2, 'paar' => 3], $payload['items'][0]['per_module']);
    }
}
