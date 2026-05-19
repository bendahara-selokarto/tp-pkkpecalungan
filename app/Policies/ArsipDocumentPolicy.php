<?php

namespace App\Policies;

use App\Models\User;
use App\Support\RoleScopeMatrix;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArsipDocumentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'arsip_document.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'arsip_document.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'arsip_document.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'arsip_document.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'arsip_document.delete');
    }

    /**
     * Determine whether the user can export models.
     */
    public function export(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'arsip_document.export');
    }
}
