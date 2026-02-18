<?php

namespace Tests\Unit\Actions\User;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use App\Actions\User\CreateUserAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CreateUserActionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'admin-desa']);
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
}

