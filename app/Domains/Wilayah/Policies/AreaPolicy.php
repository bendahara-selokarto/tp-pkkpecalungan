<?php

namespace App\Domains\Wilayah\Policies;

use App\Models\User;
use App\Domains\Wilayah\Models\Area;

class AreaPolicy
{
    public function view(User $user, Area $area)
    {
        if ($user->isKecamatan()) {
            return true;
        }

        return $user->area_id === $area->id;
    }
}
