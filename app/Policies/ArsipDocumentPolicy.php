<?php

namespace App\Policies;

use App\Domains\Wilayah\Arsip\Models\ArsipDocument;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use App\Support\RoleScopeMatrix;

class ArsipDocumentPolicy
{
    public function viewAny(User $authUser): bool
    {
        return true;
    }

    public function view(User $authUser, ArsipDocument $arsipDocument): bool
    {
        if ((bool) $arsipDocument->is_global) {
            return true;
        }

        if ((int) $arsipDocument->created_by === (int) $authUser->id) {
            return true;
        }

        return $this->canMonitorDesaArsip($authUser, $arsipDocument);
    }

    public function create(User $authUser): bool
    {
        if (! is_numeric($authUser->area_id)) {
            return false;
        }

        $scope = (string) ($authUser->scope ?? '');
        if (! in_array($scope, ['desa', 'kecamatan'], true)) {
            return false;
        }

        if (! RoleScopeMatrix::userHasRoleForScope($authUser, $scope)) {
            return false;
        }

        $areaLevel = Area::query()
            ->whereKey((int) $authUser->area_id)
            ->value('level');

        return $areaLevel === $scope;
    }

    public function update(User $authUser, ArsipDocument $arsipDocument): bool
    {
        return (int) $arsipDocument->created_by === (int) $authUser->id;
    }

    public function delete(User $authUser, ArsipDocument $arsipDocument): bool
    {
        return (int) $arsipDocument->created_by === (int) $authUser->id;
    }

    private function canMonitorDesaArsip(User $authUser, ArsipDocument $arsipDocument): bool
    {
        if (
            ! $authUser->hasAnyRole(['kecamatan-sekretaris', 'admin-kecamatan'])
            || $authUser->scope !== 'kecamatan'
            || ! is_numeric($authUser->area_id)
            || $arsipDocument->level !== 'desa'
        ) {
            return false;
        }

        $arsipDocument->loadMissing(['area', 'creator']);

        $area = $arsipDocument->area;
        if (! $area || $area->level !== 'desa') {
            return false;
        }

        if ((int) $area->parent_id !== (int) $authUser->area_id) {
            return false;
        }

        return $arsipDocument->creator?->scope === 'desa';
    }
}
