<?php

namespace App\Policies;

use App\Domains\Wilayah\ProgramPrioritas\Models\ProgramPrioritas;
use App\Domains\Wilayah\ProgramPrioritas\Services\ProgramPrioritasScopeService;
use App\Models\User;
use App\Support\RoleScopeMatrix;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProgramPrioritasPolicy
{
    use HandlesAuthorization;

    public function __construct(
        private readonly ProgramPrioritasScopeService $programPrioritasScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'program_prioritas.view');
    }

    public function create(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'program_prioritas.create');
    }

    public function view(User $user, ProgramPrioritas $programPrioritas): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'program_prioritas.view')) {
            return false;
        }

        return $this->programPrioritasScopeService->canView($user, $programPrioritas);
    }

    public function update(User $user, ProgramPrioritas $programPrioritas): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'program_prioritas.update')) {
            return false;
        }

        return $this->programPrioritasScopeService->canUpdate($user, $programPrioritas);
    }

    public function delete(User $user, ProgramPrioritas $programPrioritas): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'program_prioritas.delete')) {
            return false;
        }

        return $this->view($user, $programPrioritas);
    }

    public function print(User $user, ProgramPrioritas $programPrioritas): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'program_prioritas.print')) {
            return false;
        }

        return $this->view($user, $programPrioritas);
    }
}
