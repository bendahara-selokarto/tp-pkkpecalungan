<?php

namespace App\Policies;

use App\Domains\Wilayah\AgendaSurat\Models\AgendaSurat;
use App\Domains\Wilayah\AgendaSurat\Services\AgendaSuratScopeService;
use App\Models\User;

class AgendaSuratPolicy
{
    public function __construct(
        private readonly AgendaSuratScopeService $agendaSuratScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return $this->agendaSuratScopeService->canEnterModule($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function view(User $user, AgendaSurat $agendaSurat): bool
    {
        return $this->agendaSuratScopeService->canView($user, $agendaSurat);
    }

    public function update(User $user, AgendaSurat $agendaSurat): bool
    {
        return $this->agendaSuratScopeService->canUpdate($user, $agendaSurat);
    }

    public function delete(User $user, AgendaSurat $agendaSurat): bool
    {
        return $this->view($user, $agendaSurat);
    }
}
