<?php

namespace Tests\Unit\Policies;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'super-admin']);
        Role::create(['name' => 'admin-desa']);
    }

    public function test_hanya_super_admin_dapat_membuat_pengguna_melalui_kebijakan(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super-admin');

        $adminDesa = User::factory()->create();
        $adminDesa->assignRole('admin-desa');

        $this->assertTrue($superAdmin->can('create', User::class));
        $this->assertFalse($adminDesa->can('create', User::class));
    }
}

