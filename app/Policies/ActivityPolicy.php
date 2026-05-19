<?php

namespace App\Policies;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Models\User;
use App\Support\RoleScopeMatrix;
use Illuminate\Auth\Access\HandlesAuthorization;

class ActivityPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'activities.view');
    }

    public function create(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'activities.create');
    }

    public function view(User $user, Activity $activity): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'activities.view')) {
            return false;
        }

        return app(\App\Domains\Wilayah\Activities\Services\ActivityScopeService::class)->canView($user, $activity);
    }

    public function update(User $user, Activity $activity): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'activities.update')) {
            return false;
        }

        return app(\App\Domains\Wilayah\Activities\Services\ActivityScopeService::class)->canUpdate($user, $activity);
    }

    public function delete(User $user, Activity $activity): bool
    {
        return $this->update($user, $activity);
    }

    public function print(User $user, Activity $activity): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'activities.print')) {
            return false;
        }

        return $this->view($user, $activity);
    }
}
