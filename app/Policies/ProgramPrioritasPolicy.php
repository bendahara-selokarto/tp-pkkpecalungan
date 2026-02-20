<?php

namespace App\Policies;

use App\Domains\Wilayah\ProgramPrioritas\Models\ProgramPrioritas;
use App\Domains\Wilayah\ProgramPrioritas\Services\ProgramPrioritasScopeService;
use App\Models\User;

class ProgramPrioritasPolicy
{
    public function __construct(
        private readonly ProgramPrioritasScopeService $programPrioritasScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return $this->programPrioritasScopeService->canEnterModule($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function view(User $user, ProgramPrioritas $programPrioritas): bool
    {
        return $this->programPrioritasScopeService->canView($user, $programPrioritas);
    }

    public function update(User $user, ProgramPrioritas $programPrioritas): bool
    {
        return $this->programPrioritasScopeService->canUpdate($user, $programPrioritas);
    }

    public function delete(User $user, ProgramPrioritas $programPrioritas): bool
    {
        return $this->view($user, $programPrioritas);
    }
}
