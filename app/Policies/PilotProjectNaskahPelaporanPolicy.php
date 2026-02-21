<?php

namespace App\Policies;

use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Models\PilotProjectNaskahPelaporanReport;
use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Services\PilotProjectNaskahPelaporanScopeService;
use App\Models\User;

class PilotProjectNaskahPelaporanPolicy
{
    public function __construct(
        private readonly PilotProjectNaskahPelaporanScopeService $scopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return $this->scopeService->canEnterModule($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function view(User $user, PilotProjectNaskahPelaporanReport $report): bool
    {
        return $this->scopeService->canView($user, $report);
    }

    public function update(User $user, PilotProjectNaskahPelaporanReport $report): bool
    {
        return $this->view($user, $report);
    }

    public function delete(User $user, PilotProjectNaskahPelaporanReport $report): bool
    {
        return $this->view($user, $report);
    }
}
