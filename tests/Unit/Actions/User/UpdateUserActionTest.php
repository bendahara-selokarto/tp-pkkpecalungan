<?php

namespace Tests\Unit\Actions\User;

use App\Actions\User\UpdateUserAction;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UpdateUserActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_memperbarui_pengguna_dan_peran(): void
    {
        Role::create(['name' => 'admin-desa']);
        Role::create(['name' => 'admin-kecamatan']);
        $oldArea = Area::create(['name' => 'Gombong', 'level' => 'desa']);
        $newArea = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);

        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $oldArea->id,
        ]);
        $user->assignRole('admin-desa');

        $action = app(UpdateUserAction::class);

        $action->execute($user, [
            'name' => 'Updated Name',
            'email' => 'updated@email.com',
            'scope' => 'kecamatan',
            'area_id' => $newArea->id,
            'role' => 'admin-kecamatan',
        ]);

        $this->assertEquals('Updated Name', $user->fresh()->name);
        $this->assertEquals('kecamatan', $user->fresh()->scope);
        $this->assertEquals($newArea->id, $user->fresh()->area_id);
        $this->assertTrue($user->fresh()->hasRole('admin-kecamatan'));
    }

    public function test_gagal_memperbarui_pengguna_jika_area_tidak_sesuai_scope(): void
    {
        $this->expectException(ValidationException::class);

        Role::create(['name' => 'admin-desa']);
        $desaArea = Area::create(['name' => 'Gombong', 'level' => 'desa']);
        $kecamatanArea = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);

        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $desaArea->id,
        ]);
        $user->assignRole('admin-desa');

        $action = app(UpdateUserAction::class);
        $action->execute($user, [
            'name' => 'Updated Name',
            'email' => 'updated@email.com',
            'scope' => 'desa',
            'area_id' => $kecamatanArea->id,
            'role' => 'admin-desa',
        ]);
    }

    public function test_memperbarui_pengguna_memulihkan_scope_stale_berdasarkan_area(): void
    {
        Role::create(['name' => 'admin-desa']);
        $desaArea = Area::create(['name' => 'Gombong', 'level' => 'desa']);

        $user = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $desaArea->id,
        ]);
        $user->assignRole('admin-desa');

        $action = app(UpdateUserAction::class);

        $action->execute($user, [
            'name' => 'Normalized Scope',
            'email' => 'normalized-scope@email.com',
            'area_id' => $desaArea->id,
            'role' => 'admin-desa',
        ]);

        $this->assertSame('desa', $user->fresh()->scope);
    }

    public function test_gagal_memperbarui_super_admin(): void
    {
        $this->expectException(DomainException::class);

        Role::create(['name' => 'super-admin']);
        Role::create(['name' => 'admin-desa']);
        $desaArea = Area::create(['name' => 'Gombong', 'level' => 'desa']);

        $user = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $desaArea->id,
        ]);
        $user->assignRole('super-admin');

        $action = app(UpdateUserAction::class);
        $action->execute($user, [
            'name' => 'Should Not Update',
            'email' => 'should-not-update@email.com',
            'scope' => 'desa',
            'area_id' => $desaArea->id,
            'role' => 'admin-desa',
        ]);
    }
}
