<?php

namespace App\Policies;

use App\Support\RoleScopeMatrix;

use App\Domains\Wilayah\LaporanTahunanPkk\Models\LaporanTahunanPkkReport;
use App\Domains\Wilayah\LaporanTahunanPkk\Services\LaporanTahunanPkkScopeService;
use App\Models\User;

class LaporanTahunanPkkPolicy
{
    use \Illuminate\Auth\Access\HandlesAuthorization;

    public function __construct(
        private readonly LaporanTahunanPkkScopeService $scopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'laporan_tahunan_pkk.view');
    }

    public function create(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'laporan_tahunan_pkk.create');
    }

    public function view(User $user, LaporanTahunanPkkReport $report): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'laporan_tahunan_pkk.view')) {
            return false;
        }

        return $this->scopeService->canView($user, $report);
    }

    public function update(User $user, LaporanTahunanPkkReport $report): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'laporan_tahunan_pkk.update')) {
            return false;
        }

        return $this->scopeService->canUpdate($user, $report);
    }

    public function delete(User $user, LaporanTahunanPkkReport $report): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'laporan_tahunan_pkk.delete')) {
            return false;
        }

        return $this->view($user, $report);
    }
}

