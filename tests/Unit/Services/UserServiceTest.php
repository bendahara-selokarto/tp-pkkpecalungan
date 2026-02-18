<?php

namespace Tests\Unit\Services;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_layanan_membuat_pengguna(): void
    {
        Role::create(['name' => 'admin-desa']);
        $area = Area::create([
            'name' => 'Gombong',
            'level' => 'desa',
        ]);

        $service = app(\App\Services\User\UserService::class);

        $user = $service->create([
            'name' => 'Service User',
            'email' => 'service@test.com',
            'password' => 'password',
            'scope' => 'desa',
            'area_id' => $area->id,
            'role' => 'admin-desa',
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame('desa', $user->scope);
        $this->assertSame($area->id, $user->area_id);
        $this->assertTrue($user->hasRole('admin-desa'));
    }
}

