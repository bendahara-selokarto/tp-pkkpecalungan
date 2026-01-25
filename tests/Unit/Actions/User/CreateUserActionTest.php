<?php

namespace Tests\Unit\Actions\User;

use Tests\TestCase;
use App\Actions\User\CreateUserAction;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateUserActionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'admin']);
    }

    public function test_it_creates_user_with_role(): void
    {
        $action = app(CreateUserAction::class);

        $user = $action->execute([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'role' => 'admin',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);

        $this->assertTrue($user->hasRole('admin'));
    }
}
