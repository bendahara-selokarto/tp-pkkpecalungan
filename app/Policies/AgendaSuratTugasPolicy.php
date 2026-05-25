<?php

namespace App\Policies;

use App\Domains\Wilayah\AgendaSuratTugas\Models\AgendaSuratTugas;
use App\Models\User;
use App\Support\RoleScopeMatrix;
use Illuminate\Auth\Access\HandlesAuthorization;

class AgendaSuratTugasPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'agenda_surat_tugas.view');
    }

    public function create(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'agenda_surat_tugas.create');
    }

    public function view(User $user, AgendaSuratTugas $model): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'agenda_surat_tugas.view')) {
            return false;
        }

        return (int) $model->area_id === (int) $user->area_id
            && (int) $model->tahun_anggaran === (int) $user->active_budget_year;
    }

    public function update(User $user, AgendaSuratTugas $model): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'agenda_surat_tugas.update')) {
            return false;
        }

        return $this->view($user, $model);
    }

    public function delete(User $user, AgendaSuratTugas $model): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'agenda_surat_tugas.delete')) {
            return false;
        }

        return $this->view($user, $model);
    }

    public function print(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'agenda_surat_tugas.print');
    }
}
