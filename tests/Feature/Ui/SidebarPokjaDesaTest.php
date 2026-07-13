<?php

namespace Tests\Feature\Ui;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Support\RoleScopeMatrix;
use App\Domains\Wilayah\Services\RoleMenuVisibilityService;
use App\Models\User;
use Spatie\Permission\Models\Role;

class SidebarPokjaDesaTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function buku_bantu_modules_are_visible_for_pokja_i_desa()
    {
        // Create a user and assign the Pokja‑I Desa role
        $user = User::factory()->create();
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => RoleScopeMatrix::ROLE_POKJA_1_DESA]);
        $user->assignRole(RoleScopeMatrix::ROLE_POKJA_1_DESA);

        // Resolve menu modules for the user at desa scope
        $service = app(RoleMenuVisibilityService::class);
        $visibility = $service->resolveForScope($user, 'desa');
        $modules = $visibility['modules'];
        $this->assertArrayHasKey('buku-daftar-hadir', $modules);
        $this->assertArrayHasKey('buku-tamu', $modules);
        $this->assertArrayHasKey('buku-agenda-sk', $modules);
        $this->assertArrayHasKey('simulasi-penyuluhan', $modules);
        $this->assertArrayHasKey('buku-kliping', $modules);
        $this->assertArrayHasKey('literasi-warga', $modules);
    }
    #[\PHPUnit\Framework\Attributes\Test]
    public function buku_bantu_modules_are_visible_for_pokja_ii_desa()
    {
        // Create a user and assign the Pokja‑II Desa role
        $user = User::factory()->create();
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => RoleScopeMatrix::ROLE_POKJA_2_DESA]);
        $user->assignRole(RoleScopeMatrix::ROLE_POKJA_2_DESA);

        $service = app(RoleMenuVisibilityService::class);
        $visibility = $service->resolveForScope($user, 'desa');
        $modules = $visibility['modules'];

        $this->assertArrayHasKey('bkb-kegiatan', $modules);
        $this->assertArrayHasKey('literasi-warga', $modules);
        $this->assertArrayHasKey('tutor-khusus', $modules);
        $this->assertArrayHasKey('koperasi', $modules);
        $this->assertArrayHasKey('kejar-paket', $modules);
        $this->assertArrayHasKey('taman-bacaan', $modules);
        $this->assertArrayHasKey('kader-khusus', $modules);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function buku_bantu_modules_are_visible_for_pokja_iv_desa()
    {
        // Create a user and assign the Pokja‑IV Desa role
        $user = User::factory()->create();
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => RoleScopeMatrix::ROLE_POKJA_4_DESA]);
        $user->assignRole(RoleScopeMatrix::ROLE_POKJA_4_DESA);

        $service = app(RoleMenuVisibilityService::class);
        $visibility = $service->resolveForScope($user, 'desa');
        $modules = $visibility['modules'];

        $this->assertArrayHasKey('kader-khusus', $modules);
        $this->assertArrayHasKey('bkb-kegiatan', $modules);
        $this->assertArrayHasKey('bkl', $modules);
        $this->assertArrayHasKey('posyandu', $modules);
        $this->assertArrayHasKey('inventaris', $modules);
    }
}
