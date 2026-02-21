<?php

namespace App\Policies;

use App\Domains\Wilayah\PilotProjectKeluargaSehat\Models\PilotProjectKeluargaSehatReport;
use App\Domains\Wilayah\PilotProjectKeluargaSehat\Services\PilotProjectKeluargaSehatScopeService;
use App\Models\User;

class PilotProjectKeluargaSehatPolicy
{
    public function __construct(
        private readonly PilotProjectKeluargaSehatScopeService $pilotProjectKeluargaSehatScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return $this->pilotProjectKeluargaSehatScopeService->canEnterModule($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function view(User $user, PilotProjectKeluargaSehatReport $report): bool
    {
        return $this->pilotProjectKeluargaSehatScopeService->canView($user, $report);
    }

    public function update(User $user, PilotProjectKeluargaSehatReport $report): bool
    {
        return $this->pilotProjectKeluargaSehatScopeService->canUpdate($user, $report);
    }

    public function delete(User $user, PilotProjectKeluargaSehatReport $report): bool
    {
        return $this->view($user, $report);
    }
}

