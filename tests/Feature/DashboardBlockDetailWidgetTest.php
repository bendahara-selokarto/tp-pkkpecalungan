<?php

namespace Tests\Feature;

use App\Domains\Wilayah\DataWarga\Models\DataWarga;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardBlockDetailWidgetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'kecamatan-sekretaris']);
        Role::create(['name' => 'kecamatan-pokja-i']);
        Role::create(['name' => 'kecamatan-pokja-ii']);
        Role::create(['name' => 'kecamatan-pokja-iii']);
        Role::create(['name' => 'kecamatan-pokja-iv']);
    }

    public function test_dashboard_block_per_desa_memuat_metadata_detail_tanpa_payload_per_module_pada_first_load(): void
    {
        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desa = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $kecamatan->id,
        ]);
        $user->assignRole('kecamatan-pokja-i');

        $this->createDataWarga($user, 'desa', $desa->id, 'Kepala A');

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Dashboard')
                ->missing('dashboardBlocks');

            $page->loadDeferredProps('dashboard-blocks', function (AssertableInertia $reload): void {
                $reload->where('dashboardBlocks', function ($blocks): bool {
                    $block = collect($blocks)->firstWhere('key', 'documents-pokja-i-kecamatan-desa-breakdown');
                    if (! is_array($block)) {
                        return false;
                    }

                    $items = $block['charts']['coverage_per_module']['items'] ?? [];
                    $firstItem = is_array($items) ? ($items[0] ?? null) : null;

                    return ($block['detail']['strategy'] ?? null) === 'json'
                        && is_string($block['detail']['endpoint'] ?? null)
                        && is_array($firstItem)
                        && array_key_exists('label', $firstItem)
                        && ! array_key_exists('per_module', $firstItem);
                });
            });
        });
    }

    public function test_dashboard_widget_detail_json_mengembalikan_rincian_per_module_per_desa(): void
    {
        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desa = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $kecamatan->id,
        ]);
        $user->assignRole('kecamatan-pokja-i');

        $this->createDataWarga($user, 'desa', $desa->id, 'Kepala A');

        $response = $this->actingAs($user)
            ->getJson(route('dashboard.blocks.show', ['blockKey' => 'documents-pokja-i-kecamatan-desa-breakdown']));

        $response->assertOk()->assertJson(function ($json): void {
            $json
                ->where('key', 'documents-pokja-i-kecamatan-desa-breakdown')
                ->where('items.0.label', 'Gombong')
                ->where('items.0.total', 1)
                ->where('items.0.per_module.data-warga', 1)
                ->has('tracked_modules');
        });
    }

    public function test_dashboard_widget_detail_json_menolak_block_yang_tidak_didukung(): void
    {
        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);

        $user = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $kecamatan->id,
        ]);
        $user->assignRole('kecamatan-pokja-i');

        $response = $this->actingAs($user)
            ->getJson(route('dashboard.blocks.show', ['blockKey' => 'documents-pokja-i']));

        $response->assertNotFound();
    }

    private function createDataWarga(User $user, string $level, int $areaId, string $kepalaKeluarga): void
    {
        DataWarga::create([
            'dasawisma' => 'Melati',
            'nama_kepala_keluarga' => $kepalaKeluarga,
            'alamat' => 'Alamat',
            'jumlah_warga_laki_laki' => 2,
            'jumlah_warga_perempuan' => 1,
            'keterangan' => null,
            'level' => $level,
            'area_id' => $areaId,
            'created_by' => $user->id,
            'tahun_anggaran' => (int) ($user->active_budget_year ?? now()->format('Y')),
        ]);
    }
}
