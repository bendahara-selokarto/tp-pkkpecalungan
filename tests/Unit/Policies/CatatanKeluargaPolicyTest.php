<?php

namespace Tests\Unit\Policies;

use App\Domains\Wilayah\CatatanKeluarga\Models\CatatanKeluarga;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use App\Policies\CatatanKeluargaPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CatatanKeluargaPolicyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_desa_dengan_area_desa_valid_boleh_melihat_catatan_keluarga(): void
    {
        Role::create(['name' => 'admin-desa']);

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desa = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $desa->id]);
        $user->assignRole('admin-desa');

        $policy = app(CatatanKeluargaPolicy::class);

        $this->assertTrue($policy->viewAny($user));
        $this->assertTrue($policy->view($user, new CatatanKeluarga()));
    }

    #[Test]
    public function admin_desa_dengan_area_bukan_desa_tidak_boleh_melihat_catatan_keluarga(): void
    {
        Role::create(['name' => 'admin-desa']);

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);

        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $kecamatan->id]);
        $user->assignRole('admin-desa');

        $policy = app(CatatanKeluargaPolicy::class);

        $this->assertFalse($policy->viewAny($user));
    }
}

