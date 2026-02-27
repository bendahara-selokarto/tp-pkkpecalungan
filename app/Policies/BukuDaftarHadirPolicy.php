<?php

namespace App\Policies;

use App\Domains\Wilayah\BukuDaftarHadir\Models\BukuDaftarHadir;
use App\Domains\Wilayah\BukuDaftarHadir\Services\BukuDaftarHadirScopeService;
use App\Models\User;

class BukuDaftarHadirPolicy
{
    public function __construct(
        private readonly BukuDaftarHadirScopeService $bukuDaftarHadirScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return $this->bukuDaftarHadirScopeService->canEnterModule($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function view(User $user, BukuDaftarHadir $bukuDaftarHadir): bool
    {
        return $this->bukuDaftarHadirScopeService->canView($user, $bukuDaftarHadir);
    }

    public function update(User $user, BukuDaftarHadir $bukuDaftarHadir): bool
    {
        return $this->bukuDaftarHadirScopeService->canUpdate($user, $bukuDaftarHadir);
    }

    public function delete(User $user, BukuDaftarHadir $bukuDaftarHadir): bool
    {
        return $this->view($user, $bukuDaftarHadir);
    }
}
