<?php

namespace Tests\Unit\Dashboard;

use App\Domains\Wilayah\DataWarga\Models\DataWarga;
use App\Domains\Wilayah\Dashboard\Repositories\DashboardGroupCoverageRepositoryInterface;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardGroupCoverageRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'kecamatan-pokja-i']);
        Role::create(['name' => 'desa-pokja-i']);
    }

    public function test_breakdown_per_desa_hanya_mengambil_desa_dalam_kecamatan_pengguna(): void
    {
        $kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);
        $desaA1 = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatanA->id]);
        $desaA2 = Area::create(['name' => 'Bandung', 'level' => 'desa', 'parent_id' => $kecamatanA->id]);
        $desaB1 = Area::create(['name' => 'Sidomukti', 'level' => 'desa', 'parent_id' => $kecamatanB->id]);

        $user = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $kecamatanA->id,
        ]);
        $user->assignRole('kecamatan-pokja-i');

        DataWarga::create([
            'dasawisma' => 'Melati',
            'nama_kepala_keluarga' => 'Kepala A1',
            'alamat' => 'Alamat A1',
            'jumlah_warga_laki_laki' => 2,
            'jumlah_warga_perempuan' => 1,
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $desaA1->id,
            'created_by' => $user->id,
        ]);
        DataWarga::create([
            'dasawisma' => 'Anggrek',
            'nama_kepala_keluarga' => 'Kepala B1',
            'alamat' => 'Alamat B1',
            'jumlah_warga_laki_laki' => 1,
            'jumlah_warga_perempuan' => 2,
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $desaB1->id,
            'created_by' => $user->id,
        ]);

        $repository = $this->app->make(DashboardGroupCoverageRepositoryInterface::class);
        $result = collect($repository->buildBreakdownByDesaForGroup($user, 'pokja-i'));

        $labels = $result->pluck('desa_name')->all();
        $totalsByDesa = $result->mapWithKeys(
            static fn (array $item): array => [(string) ($item['desa_name'] ?? '-') => (int) ($item['total'] ?? 0)]
        );

        $this->assertContains('Gombong', $labels);
        $this->assertContains('Bandung', $labels);
        $this->assertNotContains('Sidomukti', $labels);
        $this->assertSame(1, $totalsByDesa['Gombong'] ?? null);
        $this->assertSame(0, $totalsByDesa['Bandung'] ?? null);
    }

    public function test_breakdown_per_desa_menolak_user_non_kecamatan(): void
    {
        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desa = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $desa->id,
        ]);
        $user->assignRole('desa-pokja-i');

        $repository = $this->app->make(DashboardGroupCoverageRepositoryInterface::class);
        $result = $repository->buildBreakdownByDesaForGroup($user, 'pokja-i');

        $this->assertSame([], $result);
    }
}
