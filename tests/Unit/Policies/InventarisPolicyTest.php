<?php

namespace Tests\Unit\Policies;
use PHPUnit\Framework\Attributes\Test;

use App\Domains\Wilayah\Inventaris\Models\Inventaris;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use App\Policies\InventarisPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class InventarisPolicyTest extends TestCase
{
    use RefreshDatabase;

    private const ACTIVE_BUDGET_YEAR = 2026;

    #[Test]
    public function admin_desa_hanya_boleh_melihat_inventaris_pada_desanya_sendiri()
    {
        Role::create(['name' => 'admin-desa']);

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);
        $desaB = Area::create(['name' => 'Bandung', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $desaA->id,
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('admin-desa');

        $milikSendiri = Inventaris::create([
            'name' => 'Kursi',
            'description' => null,
            'quantity' => 10,
            'unit' => 'buah',
            'condition' => 'baik',
            'level' => 'desa',
            'area_id' => $desaA->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $milikDesaLain = Inventaris::create([
            'name' => 'Meja',
            'description' => null,
            'quantity' => 5,
            'unit' => 'buah',
            'condition' => 'baik',
            'level' => 'desa',
            'area_id' => $desaB->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $policy = app(InventarisPolicy::class);

        $this->assertTrue($policy->view($user, $milikSendiri));
        $this->assertFalse($policy->view($user, $milikDesaLain));
    }

    #[Test]
    public function admin_desa_tidak_boleh_melihat_inventaris_tahun_anggaran_lain(): void
    {
        Role::create(['name' => 'admin-desa']);

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desa = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $desa->id,
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('admin-desa');

        $inventaris = Inventaris::create([
            'name' => 'Inventaris Lama',
            'description' => null,
            'quantity' => 1,
            'unit' => 'unit',
            'condition' => 'baik',
            'level' => 'desa',
            'area_id' => $desa->id,
            'created_by' => $user->id,
            'tahun_anggaran' => 2025,
        ]);

        $policy = app(InventarisPolicy::class);

        $this->assertFalse($policy->view($user, $inventaris));
    }

    #[Test]
    public function admin_kecamatan_tidak_boleh_memperbarui_inventaris_kecamatan_lain()
    {
        Role::create(['name' => 'admin-kecamatan']);

        $kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);

        $user = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $kecamatanA->id,
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('admin-kecamatan');

        $inventarisLuar = Inventaris::create([
            'name' => 'Laptop',
            'description' => null,
            'quantity' => 2,
            'unit' => 'unit',
            'condition' => 'baik',
            'level' => 'kecamatan',
            'area_id' => $kecamatanB->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $policy = app(InventarisPolicy::class);

        $this->assertFalse($policy->update($user, $inventarisLuar));
    }
}
