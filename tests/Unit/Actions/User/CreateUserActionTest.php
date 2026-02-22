<?php

namespace Tests\Unit\Actions\User;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use App\Actions\User\CreateUserAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CreateUserActionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'admin-desa']);
        Role::create(['name' => 'admin-kecamatan']);
        Role::create(['name' => 'super-admin']);
    }

    public function test_membuat_pengguna_dengan_peran(): void
    {
        $action = app(CreateUserAction::class);
        $area = Area::create([
            'name' => 'Gombong',
            'level' => 'desa',
            'parent_id' => null,
        ]);

        $user = $action->execute([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'scope' => 'desa',
            'area_id' => $area->id,
            'role' => 'admin-desa',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'scope' => 'desa',
            'area_id' => $area->id,
        ]);

        $this->assertTrue($user->hasRole('admin-desa'));
    }

    public function test_gagal_membuat_pengguna_jika_role_tidak_sesuai_scope(): void
    {
        $this->expectException(ValidationException::class);

        $action = app(CreateUserAction::class);
        $area = Area::create([
            'name' => 'Gombong',
            'level' => 'desa',
            'parent_id' => null,
        ]);

        $action->execute([
            'name' => 'Test User',
            'email' => 'invalid-role@example.com',
            'password' => 'password123',
            'scope' => 'desa',
            'area_id' => $area->id,
            'role' => 'admin-kecamatan',
        ]);
    }

    public function test_membuat_pengguna_tetap_mengikuti_scope_canonical_dari_area(): void
    {
        $action = app(CreateUserAction::class);
        $area = Area::create([
            'name' => 'Gombong',
            'level' => 'desa',
            'parent_id' => null,
        ]);

        $user = $action->execute([
            'name' => 'Canonical Scope User',
            'email' => 'canonical-scope@example.com',
            'password' => 'password123',
            'area_id' => $area->id,
            'role' => 'admin-desa',
        ]);

        $this->assertSame('desa', $user->fresh()->scope);
    }

    public function test_gagal_membuat_pengguna_dengan_role_super_admin_pada_jalur_manajemen_user(): void
    {
        $this->expectException(ValidationException::class);

        $action = app(CreateUserAction::class);
        $area = Area::create([
            'name' => 'Pecalungan',
            'level' => 'kecamatan',
            'parent_id' => null,
        ]);

        $action->execute([
            'name' => 'Forbidden Role User',
            'email' => 'forbidden-role@example.com',
            'password' => 'password123',
            'scope' => 'kecamatan',
            'area_id' => $area->id,
            'role' => 'super-admin',
        ]);
    }
}
