<?php

namespace Tests\Unit\Actions\User;

use App\Actions\User\DeleteUserAction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteUserActionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_deletes_normal_user()
    {
        $user = User::factory()->create();

        (new DeleteUserAction())->execute($user);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }
}
