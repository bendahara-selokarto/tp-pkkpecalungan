<?php

namespace App\Domains\Wilayah\CetakLampiran\Controllers;

use App\Domains\Wilayah\Services\UserAreaContextService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CetakLampiranController
{
    public function __construct(
        private readonly UserAreaContextService $userAreaContextService
    ) {
    }

    public function __invoke(Request $request): Response|RedirectResponse
    {
        $user = $request->user();
        abort_if(! $user, 403);

        if ($user->hasRole('super-admin')) {
            return redirect()->route('super-admin.users.index');
        }

        $scope = $this->userAreaContextService->resolveEffectiveScope($user);
        abort_if(! is_string($scope), 403, 'Scope pengguna tidak valid.');

        return Inertia::render('CetakLampiran/Index');
    }
}
