<?php

namespace App\Services\User;

use App\Actions\User\CreateUserAction;
use App\Actions\User\UpdateUserAction;
use App\Actions\User\DeleteUserAction;
use App\Models\User;

class UserService
{
    public function __construct(
        private readonly CreateUserAction $createUserAction,
        private readonly UpdateUserAction $updateUserAction,
        private readonly DeleteUserAction $deleteUserAction
    ) {
    }

    public function create(array $data): User
    {
        return $this->createUserAction->execute($data);
    }

    public function update(User $user, array $data): User
    {
        return $this->updateUserAction->execute($user, $data);
    }

    public function delete(User $user): void
    {
        $this->deleteUserAction->execute($user);
    }
}
