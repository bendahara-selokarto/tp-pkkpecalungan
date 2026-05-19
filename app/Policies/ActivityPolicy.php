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
        return RoleScopeMatrix::hasPermission($user->role, 'activities.view');
    }

    public function create(User $user): bool
    {
        return RoleScopeMatrix::hasPermission($user->role, 'activities.create');
    }

    public function view(User $user, Activity $activity): bool
    {
        // Permission check
        if (!RoleScopeMatrix::hasPermission($user->role, 'activities.view')) {
            return false;
        }

        // Scoping logic will be handled by ActivityScopeService::canView or Global Scope
        // For now, we delegate the complex scoping to the service to ensure no regression,
        // but the core permission check is moved here.
        return app(\App\Domains\Wilayah\Activities\Services\ActivityScopeService::class)->canView($user, $activity);
    }

    public function update(User $user, Activity $activity): bool
    {
        if (!RoleScopeMatrix::hasPermission($user->role, 'activities.update')) {
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
        if (!RoleScopeMatrix::hasPermission($user->role, 'activities.print')) {
            return false;
        }

        return $this->view($user, $activity);
    }
}
