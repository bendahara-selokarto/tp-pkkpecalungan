<?php

namespace App\Policies;

use App\Domains\Wilayah\Bantuan\Models\Bantuan;
use App\Domains\Wilayah\Bantuan\Services\BantuanScopeService;
use App\Models\User;

class BantuanPolicy
{
    public function __construct(
        private readonly BantuanScopeService $bantuanScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return $this->bantuanScopeService->canEnterModule($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function view(User $user, Bantuan $bantuan): bool
    {
        return $this->bantuanScopeService->canView($user, $bantuan);
    }

    public function update(User $user, Bantuan $bantuan): bool
    {
        return $this->bantuanScopeService->canUpdate($user, $bantuan);
    }

    public function delete(User $user, Bantuan $bantuan): bool
    {
        return $this->view($user, $bantuan);
    }
}
