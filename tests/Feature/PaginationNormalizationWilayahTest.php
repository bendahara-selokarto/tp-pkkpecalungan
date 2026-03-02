<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PaginationNormalizationWilayahTest extends TestCase
{
    use RefreshDatabase;

    private User $adminDesa;
    private User $adminKecamatan;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin-desa']);
        Role::create(['name' => 'admin-kecamatan']);

        $kecamatan = Area::create([
            'name' => 'Pecalungan',
            'level' => ScopeLevel::KECAMATAN->value,
        ]);

        $desa = Area::create([
            'name' => 'Gombong',
            'level' => ScopeLevel::DESA->value,
            'parent_id' => $kecamatan->id,
        ]);

        $this->adminDesa = User::factory()->create([
            'scope' => ScopeLevel::DESA->value,
            'area_id' => $desa->id,
        ]);
        $this->adminDesa->assignRole('admin-desa');

        $this->adminKecamatan = User::factory()->create([
            'scope' => ScopeLevel::KECAMATAN->value,
            'area_id' => $kecamatan->id,
        ]);
        $this->adminKecamatan->assignRole('admin-kecamatan');
    }

    #[Test]
    #[DataProvider('indexEndpointProvider')]
    public function index_menerima_per_page_valid_dan_mempertahankan_kontrak_paginator(
        string $scope,
        string $url,
        string $component,
        string $listProp
    ): void {
        $user = $scope === ScopeLevel::DESA->value
            ? $this->adminDesa
            : $this->adminKecamatan;

        $response = $this->actingAs($user)->get($url . '?per_page=25');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page) use ($component, $listProp): void {
            $page
                ->component($component)
                ->where('filters.per_page', 25)
                ->where('pagination.perPageOptions', [10, 25, 50])
                ->where($listProp . '.per_page', 25)
                ->has($listProp . '.data')
                ->has($listProp . '.links');
        });
    }

    #[Test]
    #[DataProvider('indexEndpointProvider')]
    public function index_fallback_ke_default_saat_per_page_invalid(
        string $scope,
        string $url,
        string $component,
        string $listProp
    ): void {
        $user = $scope === ScopeLevel::DESA->value
            ? $this->adminDesa
            : $this->adminKecamatan;

        $response = $this->actingAs($user)->get($url . '?per_page=999');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page) use ($component, $listProp): void {
            $page
                ->component($component)
                ->where('filters.per_page', 10)
                ->where('pagination.perPageOptions', [10, 25, 50])
                ->where($listProp . '.per_page', 10)
                ->has($listProp . '.data')
                ->has($listProp . '.links');
        });
    }

    /**
     * @return array<string, array{string, string, string, string}>
     */
    public static function indexEndpointProvider(): array
    {
        return [
            'desa-koperasi' => [ScopeLevel::DESA->value, '/desa/koperasi', 'Desa/Koperasi/Index', 'koperasiItems'],
            'kecamatan-koperasi' => [ScopeLevel::KECAMATAN->value, '/kecamatan/koperasi', 'Kecamatan/Koperasi/Index', 'koperasiItems'],
            'desa-kejar-paket' => [ScopeLevel::DESA->value, '/desa/kejar-paket', 'Desa/KejarPaket/Index', 'kejarPaketItems'],
            'kecamatan-kejar-paket' => [ScopeLevel::KECAMATAN->value, '/kecamatan/kejar-paket', 'Kecamatan/KejarPaket/Index', 'kejarPaketItems'],
            'desa-posyandu' => [ScopeLevel::DESA->value, '/desa/posyandu', 'Desa/Posyandu/Index', 'posyanduItems'],
            'kecamatan-posyandu' => [ScopeLevel::KECAMATAN->value, '/kecamatan/posyandu', 'Kecamatan/Posyandu/Index', 'posyanduItems'],
            'desa-program-prioritas' => [ScopeLevel::DESA->value, '/desa/program-prioritas', 'Desa/ProgramPrioritas/Index', 'programPrioritas'],
            'kecamatan-program-prioritas' => [ScopeLevel::KECAMATAN->value, '/kecamatan/program-prioritas', 'Kecamatan/ProgramPrioritas/Index', 'programPrioritas'],
            'desa-simulasi-penyuluhan' => [ScopeLevel::DESA->value, '/desa/simulasi-penyuluhan', 'Desa/SimulasiPenyuluhan/Index', 'simulasiPenyuluhanItems'],
            'kecamatan-simulasi-penyuluhan' => [ScopeLevel::KECAMATAN->value, '/kecamatan/simulasi-penyuluhan', 'Kecamatan/SimulasiPenyuluhan/Index', 'simulasiPenyuluhanItems'],
            'desa-warung-pkk' => [ScopeLevel::DESA->value, '/desa/warung-pkk', 'Desa/WarungPkk/Index', 'warungPkkItems'],
            'kecamatan-warung-pkk' => [ScopeLevel::KECAMATAN->value, '/kecamatan/warung-pkk', 'Kecamatan/WarungPkk/Index', 'warungPkkItems'],
            'desa-pilot-keluarga-sehat' => [ScopeLevel::DESA->value, '/desa/pilot-project-keluarga-sehat', 'PilotProjectKeluargaSehat/Index', 'reports'],
            'kecamatan-pilot-keluarga-sehat' => [ScopeLevel::KECAMATAN->value, '/kecamatan/pilot-project-keluarga-sehat', 'PilotProjectKeluargaSehat/Index', 'reports'],
            'desa-pilot-naskah-pelaporan' => [ScopeLevel::DESA->value, '/desa/pilot-project-naskah-pelaporan', 'PilotProjectNaskahPelaporan/Index', 'reports'],
            'kecamatan-pilot-naskah-pelaporan' => [ScopeLevel::KECAMATAN->value, '/kecamatan/pilot-project-naskah-pelaporan', 'PilotProjectNaskahPelaporan/Index', 'reports'],
        ];
    }
}
