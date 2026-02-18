<?php

namespace Tests\Unit\Actions\User;

use App\Actions\User\UpdateUserAction;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}

