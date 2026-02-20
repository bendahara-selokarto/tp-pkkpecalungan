<?php

namespace App\Policies;

use App\Domains\Wilayah\KaderKhusus\Models\KaderKhusus;
use App\Domains\Wilayah\KaderKhusus\Services\KaderKhususScopeService;
use App\Models\User;

class KaderKhususPolicy
{
    public function __construct(
        private readonly KaderKhususScopeService $kaderKhususScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return $this->kaderKhususScopeService->canEnterModule($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function view(User $user, KaderKhusus $kaderKhusus): bool
    {
        return $this->kaderKhususScopeService->canView($user, $kaderKhusus);
    }

    public function update(User $user, KaderKhusus $kaderKhusus): bool
    {
        return $this->kaderKhususScopeService->canUpdate($user, $kaderKhusus);
    }

    public function delete(User $user, KaderKhusus $kaderKhusus): bool
    {
        return $this->view($user, $kaderKhusus);
    }
}
