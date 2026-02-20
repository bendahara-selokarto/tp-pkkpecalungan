<?php

namespace App\Http\Middleware;

use App\Domains\Wilayah\Repositories\AreaRepositoryInterface;
use App\Support\RoleScopeMatrix;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureScopeRole
{
    public function __construct(
        private readonly AreaRepositoryInterface $areaRepository
    ) {
    }

    public function handle(Request $request, Closure $next, string $scope): Response
    {
        $user = $request->user();

        if (! $user || ! RoleScopeMatrix::userHasRoleForScope($user, $scope)) {
            abort(403, 'Anda tidak memiliki role untuk scope ini.');
        }

        if (! is_numeric($user->area_id)) {
            abort(403, 'Area pengguna belum ditentukan.');
        }

        $areaLevel = $user->relationLoaded('area')
            ? $user->area?->level
            : $this->areaRepository->getLevelById((int) $user->area_id);

        if ($areaLevel !== $scope) {
            abort(403, 'Scope pengguna tidak sesuai area.');
        }

        return $next($request);
    }
}
