<?php

namespace App\Policies;

use App\Domains\Wilayah\BkbKegiatan\Models\BkbKegiatan;
use App\Domains\Wilayah\BkbKegiatan\Services\BkbKegiatanScopeService;
use App\Models\User;

class BkbKegiatanPolicy
{
    public function __construct(
        private readonly BkbKegiatanScopeService $bkbKegiatanScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return $this->bkbKegiatanScopeService->canEnterModule($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function view(User $user, BkbKegiatan $bkbKegiatan): bool
    {
        return $this->bkbKegiatanScopeService->canView($user, $bkbKegiatan);
    }

    public function update(User $user, BkbKegiatan $bkbKegiatan): bool
    {
        return $this->bkbKegiatanScopeService->canUpdate($user, $bkbKegiatan);
    }

    public function delete(User $user, BkbKegiatan $bkbKegiatan): bool
    {
        return $this->view($user, $bkbKegiatan);
    }
}
