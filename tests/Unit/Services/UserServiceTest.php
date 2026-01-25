<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;


class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_creates_user(): void
    {
        Role::create(['name' => 'admin']);

        $service = app(\App\Services\User\UserService::class);

        $user = $service->create([
            'name' => 'Service User',
            'email' => 'service@test.com',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $this->assertInstanceOf(User::class, $user);
    }
}
