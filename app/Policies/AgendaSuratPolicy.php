<?php

namespace App\Policies;

use App\Support\RoleScopeMatrix;

use App\Domains\Wilayah\AgendaSurat\Models\AgendaSurat;
use App\Domains\Wilayah\AgendaSurat\Services\AgendaSuratScopeService;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AgendaSuratPolicy
{
    use HandlesAuthorization;

    public function __construct(
        private readonly AgendaSuratScopeService $agendaSuratScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'agenda_surat.view');
    }

    public function create(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'agenda_surat.create');
    }

    public function view(User $user, AgendaSurat $agendaSurat): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'agenda_surat.view')) {
            return false;
        }

        return $this->agendaSuratScopeService->canView($user, $agendaSurat);
    }

    public function update(User $user, AgendaSurat $agendaSurat): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'agenda_surat.update')) {
            return false;
        }

        return $this->agendaSuratScopeService->canUpdate($user, $agendaSurat);
    }

    public function delete(User $user, AgendaSurat $agendaSurat): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'agenda_surat.delete')) {
            return false;
        }

        return $this->view($user, $agendaSurat);
    }

    public function print(User $user, AgendaSurat $agendaSurat): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'agenda_surat.print')) {
            return false;
        }

        return $this->view($user, $agendaSurat);
    }
}
