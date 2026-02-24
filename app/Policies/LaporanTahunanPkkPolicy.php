<?php

namespace App\Policies;

use App\Domains\Wilayah\LaporanTahunanPkk\Models\LaporanTahunanPkkReport;
use App\Domains\Wilayah\LaporanTahunanPkk\Services\LaporanTahunanPkkScopeService;
use App\Models\User;

class LaporanTahunanPkkPolicy
{
    public function __construct(
        private readonly LaporanTahunanPkkScopeService $scopeService
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

    public function view(User $user, LaporanTahunanPkkReport $report): bool
    {
        return $this->scopeService->canView($user, $report);
    }

    public function update(User $user, LaporanTahunanPkkReport $report): bool
    {
        return $this->scopeService->canUpdate($user, $report);
    }

    public function delete(User $user, LaporanTahunanPkkReport $report): bool
    {
        return $this->view($user, $report);
    }
}

