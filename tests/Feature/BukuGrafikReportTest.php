<?php

namespace Tests\Feature;

use App\Models\User;
use App\Domains\Wilayah\Models\Area;
use App\Domains\Wilayah\Enums\ScopeLevel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BukuGrafikReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_kecamatan_user_can_access_buku_grafik_report()
    {
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $area = Area::create([
            'name' => 'Kecamatan Test',
            'level' => ScopeLevel::KECAMATAN->value,
        ]);
        $user = User::factory()->create([
            'scope' => ScopeLevel::KECAMATAN->value,
            'area_id' => $area->id,
        ]);
        $user->assignRole('kecamatan-sekretaris');

        $response = $this->actingAs($user)->get('/dashboard/charts/report/pdf');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_desa_user_can_access_buku_grafik_report()
    {
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $area = Area::create([
            'name' => 'Desa Test',
            'level' => ScopeLevel::DESA->value,
        ]);
        $user = User::factory()->create([
            'scope' => ScopeLevel::DESA->value,
            'area_id' => $area->id,
        ]);
        $user->assignRole('desa-sekretaris');

        $response = $this->actingAs($user)->get('/dashboard/charts/report/pdf');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}
