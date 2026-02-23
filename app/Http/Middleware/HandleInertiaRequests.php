<?php

namespace App\Http\Middleware;

use App\Domains\Wilayah\Services\RoleMenuVisibilityService;
use App\Domains\Wilayah\Services\UserAreaContextService;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    public function __construct(
        private readonly UserAreaContextService $userAreaContextService,
        private readonly RoleMenuVisibilityService $roleMenuVisibilityService
    ) {
    }

    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'appName' => config('app.name'),
            'auth' => [
                'user' => function () use ($request): ?array {
                    $user = $request->user();

                    if (! $user) {
                        return null;
                    }

                    $scope = $this->userAreaContextService->resolveEffectiveScope($user);
                    $visibility = is_string($scope)
                        ? $this->roleMenuVisibilityService->resolveForScope($user, $scope)
                        : ['groups' => [], 'modules' => []];

                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'scope' => $scope,
                        'roles' => $user->getRoleNames()->values(),
                        'menuGroupModes' => $visibility['groups'],
                        'moduleModes' => $visibility['modules'],
                    ];
                },
            ],
            'flash' => [
                'status' => fn () => $request->session()->get('status'),
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ]);
    }
}
