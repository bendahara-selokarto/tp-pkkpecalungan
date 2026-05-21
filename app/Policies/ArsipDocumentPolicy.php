<?php

namespace App\Policies;

use App\Support\RoleScopeMatrix;
use App\Domains\Wilayah\Arsip\Models\ArsipDocument;
use App\Models\User;
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
    public function view(User $user, ArsipDocument $arsipDocument): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'arsip_document.view')) {
            return false;
        }

        // If it's a global document (uploaded by super admin), anyone with view permission can see it
        if ($arsipDocument->is_global) {
            return true;
        }

        // Personal archives are strictly for the owner
        if ((int) $arsipDocument->created_by === (int) $user->id) {
            return true;
        }

        // Super admin cannot view other's personal archives
        if (RoleScopeMatrix::userIsSuperAdmin($user)) {
            return false;
        }

        // Special rule for kecamatan sekretaris viewing their village archives
        if (RoleScopeMatrix::userHasRole($user, RoleScopeMatrix::ROLE_SEKRETARIS_KECAMATAN)) {
             if ((int) $arsipDocument->area_id === (int) $user->area_id) {
                 return true;
             }

             $documentArea = $arsipDocument->area;
             if ($documentArea && $documentArea->level === 'desa' && (int) $documentArea->parent_id === (int) $user->area_id) {
                 return true;
             }
        }

        return false;
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
    public function update(User $user, ArsipDocument $arsipDocument): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'arsip_document.update')) {
            return false;
        }

        if (RoleScopeMatrix::userIsSuperAdmin($user)) {
            return $arsipDocument->is_global || (int) $arsipDocument->created_by === (int) $user->id;
        }

        return (int) $arsipDocument->created_by === (int) $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ArsipDocument $arsipDocument): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'arsip_document.delete')) {
            return false;
        }

        if (RoleScopeMatrix::userIsSuperAdmin($user)) {
            return $arsipDocument->is_global || (int) $arsipDocument->created_by === (int) $user->id;
        }

        return (int) $arsipDocument->created_by === (int) $user->id;
    }

    /**
     * Determine whether the user can export models.
     */
    public function export(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'arsip_document.export');
    }
}
