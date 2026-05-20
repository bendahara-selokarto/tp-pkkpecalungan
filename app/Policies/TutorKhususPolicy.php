<?php

namespace App\Policies;

use App\Support\RoleScopeMatrix;

use App\Domains\Wilayah\TutorKhusus\Models\TutorKhusus;
use App\Domains\Wilayah\TutorKhusus\Services\TutorKhususScopeService;
use App\Models\User;

class TutorKhususPolicy
{
    use \Illuminate\Auth\Access\HandlesAuthorization;

    public function __construct(
        private readonly TutorKhususScopeService $tutorKhususScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'tutor_khusus.view');
    }

    public function create(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'tutor_khusus.create');
    }

    public function view(User $user, TutorKhusus $tutorKhusus): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'tutor_khusus.view')) {
            return false;
        }

        return $this->tutorKhususScopeService->canView($user, $tutorKhusus);
    }

    public function update(User $user, TutorKhusus $tutorKhusus): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'tutor_khusus.update')) {
            return false;
        }

        return $this->tutorKhususScopeService->canUpdate($user, $tutorKhusus);
    }

    public function delete(User $user, TutorKhusus $tutorKhusus): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'tutor_khusus.delete')) {
            return false;
        }

        return $this->view($user, $tutorKhusus);
    }
}
