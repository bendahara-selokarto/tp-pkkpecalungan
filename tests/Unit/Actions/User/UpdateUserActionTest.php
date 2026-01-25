<?php

namespace Tests\Unit\Actions\User;

use Tests\TestCase;
use App\Actions\User\UpdateUserAction;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateUserActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_updates_user_and_role(): void
    {
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'operator']);

        $user = User::factory()->create();
        $user->assignRole('admin');

        $action = app(UpdateUserAction::class);

        $action->execute($user, [
            'name' => 'Updated Name',
            'email' => 'updated@email.com',
            'role' => 'operator',
        ]);

        $this->assertEquals('Updated Name', $user->fresh()->name);
        $this->assertTrue($user->fresh()->hasRole('operator'));
    }
}
