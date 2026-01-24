<?php

namespace App\Services\User;

use App\Actions\User\CreateUserAction;
use App\Actions\User\UpdateUserAction;
use App\Actions\User\DeleteUserAction;
use App\Models\User;

class UserService
{
    public function create(array $data): User
    {
        return app(CreateUserAction::class)->execute($data);
    }

    public function update(User $user, array $data): User
    {
        return app(UpdateUserAction::class)->execute($user, $data);
    }

    public function delete(User $user): void
    {
        app(DeleteUserAction::class)->execute($user);
    }
}
