<?php

namespace Tests\Unit\UseCases\User;

use App\Support\RoleScopeMatrix;
use App\UseCases\User\GetUserManagementFormOptionsUseCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class GetUserManagementFormOptionsUseCaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_opsi_role_per_scope_hanya_memuat_role_assignable_yang_tersedia(): void
    {
        Role::firstOrCreate(['name' => 'desa-sekretaris']);
        Role::firstOrCreate(['name' => 'kecamatan-sekretaris']);
        Role::firstOrCreate(['name' => 'super-admin']);

        $useCase = app(GetUserManagementFormOptionsUseCase::class);
        $options = $useCase->roleOptionsByScope();

        $this->assertSame(['desa-sekretaris'], $options['desa']);
        $this->assertSame(['kecamatan-sekretaris'], $options['kecamatan']);
        $this->assertNotContains('super-admin', $options['kecamatan']);
    }

    public function test_label_role_dibentuk_dari_opsi_scope(): void
    {
        foreach (RoleScopeMatrix::assignableRolesForScope('desa') as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }
        foreach (RoleScopeMatrix::assignableRolesForScope('kecamatan') as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        $useCase = app(GetUserManagementFormOptionsUseCase::class);
        $options = $useCase->roleOptionsByScope();
        $labels = $useCase->roleLabels($options);

        $this->assertSame('Sekretaris (Desa)', $labels['desa-sekretaris']);
        $this->assertSame('Sekretaris (Kecamatan)', $labels['kecamatan-sekretaris']);
        $this->assertArrayNotHasKey('super-admin', $labels);
    }
}
