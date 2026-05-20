<?php

namespace App\Policies;

use App\Support\RoleScopeMatrix;

use App\Domains\Wilayah\PilotProjectKeluargaSehat\Models\PilotProjectKeluargaSehatReport;
use App\Domains\Wilayah\PilotProjectKeluargaSehat\Services\PilotProjectKeluargaSehatScopeService;
use App\Models\User;

class PilotProjectKeluargaSehatPolicy
{
    use \Illuminate\Auth\Access\HandlesAuthorization;

    public function __construct(
        private readonly PilotProjectKeluargaSehatScopeService $pilotProjectKeluargaSehatScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'pilot_project_keluarga_sehat.view');
    }

    public function create(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'pilot_project_keluarga_sehat.create');
    }

    public function view(User $user, PilotProjectKeluargaSehatReport $report): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'pilot_project_keluarga_sehat.view')) {
            return false;
        }

        return $this->pilotProjectKeluargaSehatScopeService->canView($user, $report);
    }

    public function update(User $user, PilotProjectKeluargaSehatReport $report): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'pilot_project_keluarga_sehat.update')) {
            return false;
        }

        return $this->pilotProjectKeluargaSehatScopeService->canUpdate($user, $report);
    }

    public function delete(User $user, PilotProjectKeluargaSehatReport $report): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'pilot_project_keluarga_sehat.delete')) {
            return false;
        }

        return $this->view($user, $report);
    }
}

