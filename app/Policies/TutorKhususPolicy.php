<?php

namespace App\Policies;

use App\Domains\Wilayah\TutorKhusus\Models\TutorKhusus;
use App\Domains\Wilayah\TutorKhusus\Services\TutorKhususScopeService;
use App\Models\User;

class TutorKhususPolicy
{
    public function __construct(
        private readonly TutorKhususScopeService $tutorKhususScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return $this->tutorKhususScopeService->canEnterModule($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function view(User $user, TutorKhusus $tutorKhusus): bool
    {
        return $this->tutorKhususScopeService->canView($user, $tutorKhusus);
    }

    public function update(User $user, TutorKhusus $tutorKhusus): bool
    {
        return $this->tutorKhususScopeService->canUpdate($user, $tutorKhusus);
    }

    public function delete(User $user, TutorKhusus $tutorKhusus): bool
    {
        return $this->view($user, $tutorKhusus);
    }
}
