<?php

namespace App\Policies;

use App\Domains\Wilayah\CatatanKeluarga\Models\CatatanKeluarga;
use App\Domains\Wilayah\CatatanKeluarga\Services\CatatanKeluargaScopeService;
use App\Models\User;

class CatatanKeluargaPolicy
{
    public function __construct(
        private readonly CatatanKeluargaScopeService $catatanKeluargaScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return $this->catatanKeluargaScopeService->canEnterModule($user);
    }

    public function view(User $user, CatatanKeluarga $catatanKeluarga): bool
    {
        return $this->viewAny($user);
    }
}

