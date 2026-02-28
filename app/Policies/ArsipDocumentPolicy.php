<?php

namespace App\Policies;

use App\Domains\Wilayah\Arsip\Models\ArsipDocument;
use App\Models\User;

class ArsipDocumentPolicy
{
    public function viewAny(User $authUser): bool
    {
        return true;
    }

    public function view(User $authUser, ArsipDocument $arsipDocument): bool
    {
        if ($authUser->hasRole('super-admin')) {
            return true;
        }

        return $arsipDocument->is_published;
    }

    public function create(User $authUser): bool
    {
        return $authUser->hasRole('super-admin');
    }

    public function update(User $authUser, ArsipDocument $arsipDocument): bool
    {
        return $authUser->hasRole('super-admin');
    }

    public function delete(User $authUser, ArsipDocument $arsipDocument): bool
    {
        return $authUser->hasRole('super-admin');
    }
}
