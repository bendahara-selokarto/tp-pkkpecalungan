<?php

namespace App\Policies;

use App\Domains\Wilayah\BukuAgendaSk\Models\BukuAgendaSk;
use App\Domains\Wilayah\BukuAgendaSk\Services\BukuAgendaSkScopeService;
use App\Models\User;
use App\Support\RoleScopeMatrix;
use Illuminate\Auth\Access\HandlesAuthorization;

class BukuAgendaSkPolicy
{
    use HandlesAuthorization;

    public function __construct(private readonly BukuAgendaSkScopeService $scopeService)
    {
    }

    public function viewAny(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'buku_agenda_sk.view');
    }

    public function view(User $user, BukuAgendaSk $bukuAgendaSk): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'buku_agenda_sk.view')) {
            return false;
        }

        return $this->scopeService->canView($user, $bukuAgendaSk);
    }

    public function create(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'buku_agenda_sk.create');
    }

    public function update(User $user, BukuAgendaSk $bukuAgendaSk): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'buku_agenda_sk.update')) {
            return false;
        }

        return $this->scopeService->canUpdate($user, $bukuAgendaSk);
    }

    public function delete(User $user, BukuAgendaSk $bukuAgendaSk): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'buku_agenda_sk.delete')) {
            return false;
        }

        return $this->scopeService->canView($user, $bukuAgendaSk);
    }

    public function print(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'buku_agenda_sk.print');
    }
}
