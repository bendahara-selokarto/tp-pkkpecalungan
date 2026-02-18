<?php

namespace Tests\Unit\Actions\User;
use PHPUnit\Framework\Attributes\Test;

use App\Actions\User\DeleteUserAction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteUserActionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function menghapus_pengguna_biasa()
    {
        $user = User::factory()->create();

        (new DeleteUserAction())->execute($user);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }
}



