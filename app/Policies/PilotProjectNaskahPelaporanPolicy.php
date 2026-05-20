<?php

namespace App\Policies;

use App\Support\RoleScopeMatrix;

use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Models\PilotProjectNaskahPelaporanReport;
use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Services\PilotProjectNaskahPelaporanScopeService;
use App\Models\User;

class PilotProjectNaskahPelaporanPolicy
{
    use \Illuminate\Auth\Access\HandlesAuthorization;

    public function __construct(
        private readonly PilotProjectNaskahPelaporanScopeService $scopeService
    ) {}

    public function viewAny(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'pilot_project_naskah_pelaporan.view');
    }

    public function create(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'pilot_project_naskah_pelaporan.create');
    }

    public function view(User $user, PilotProjectNaskahPelaporanReport $report): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'pilot_project_naskah_pelaporan.view')) {
            return false;
        }

        return $this->scopeService->canView($user, $report);
    }

    public function update(User $user, PilotProjectNaskahPelaporanReport $report): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'pilot_project_naskah_pelaporan.update')) {
            return false;
        }

        return $this->scopeService->canUpdate($user, $report);
    }

    public function delete(User $user, PilotProjectNaskahPelaporanReport $report): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'pilot_project_naskah_pelaporan.delete')) {
            return false;
        }

        return $this->view($user, $report);
    }
}
