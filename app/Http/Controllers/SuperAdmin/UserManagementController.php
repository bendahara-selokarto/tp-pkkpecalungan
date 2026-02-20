<?php

namespace App\Http\Controllers\SuperAdmin;


use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Domains\Wilayah\Models\Area;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Services\User\UserService;
use App\UseCases\User\GetUserManagementFormOptionsUseCase;
use App\UseCases\User\ListUsersForManagementUseCase;
use DomainException;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class UserManagementController extends Controller
{
    
    public function __construct(
        protected UserService $userService,
        private readonly ListUsersForManagementUseCase $listUsersForManagementUseCase,
        private readonly GetUserManagementFormOptionsUseCase $getUserManagementFormOptionsUseCase,
        private readonly UserAreaContextService $userAreaContextService
    ) {
        $this->authorizeResource(User::class, 'user');
    }

    public function index(): Response
    {
        $users = $this->listUsersForManagementUseCase
            ->execute(10)
            ->through(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'scope' => $this->userAreaContextService->resolveUserAreaLevel($user),
                'area' => $user->area
                    ? [
                        'id' => $user->area->id,
                        'name' => $user->area->name,
                        'level' => $user->area->level,
                    ]
                    : null,
                'roles' => $user->roles->pluck('name')->values(),
            ]);

        return Inertia::render('SuperAdmin/Users/Index', [
            'users' => $users,
        ]);
    }

    public function create(): Response
    {
        $roleOptionsByScope = $this->getUserManagementFormOptionsUseCase->roleOptionsByScope();
        $roleLabels = $this->getUserManagementFormOptionsUseCase->roleLabels($roleOptionsByScope);
        $areas = $this->getUserManagementFormOptionsUseCase->areas();

        return Inertia::render('SuperAdmin/Users/Create', [
            'roleOptionsByScope' => $roleOptionsByScope,
            'roleLabels' => $roleLabels,
            'areas' => $areas->map(fn (Area $area) => [
                'id' => $area->id,
                'name' => $area->name,
                'level' => $area->level,
            ])->values(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->userService->create($request->validated());

        return redirect()->route('super-admin.users.index')->with('success', 'User berhasil dibuat');
    }

    public function edit(User $user): Response
    {
        $roleOptionsByScope = $this->getUserManagementFormOptionsUseCase->roleOptionsByScope();
        $roleLabels = $this->getUserManagementFormOptionsUseCase->roleLabels($roleOptionsByScope);
        $areas = $this->getUserManagementFormOptionsUseCase->areas();
        $user->load('roles:id,name', 'area:id,level');

        return Inertia::render('SuperAdmin/Users/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'scope' => $this->userAreaContextService->resolveUserAreaLevel($user),
                'area_id' => $user->area_id,
                'roles' => $user->roles->pluck('name')->values(),
            ],
            'roleOptionsByScope' => $roleOptionsByScope,
            'roleLabels' => $roleLabels,
            'areas' => $areas->map(fn (Area $area) => [
                'id' => $area->id,
                'name' => $area->name,
                'level' => $area->level,
            ])->values(),
        ]);
    }
    
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        try {
            $this->userService->update($user, $request->validated());
        } catch (DomainException $exception) {
            return redirect()
                ->route('super-admin.users.index')
                ->with('error', $exception->getMessage());
        }

        return redirect()->route('super-admin.users.index')->with('success', 'User berhasil diupdate');
    }

    public function destroy(User $user): RedirectResponse
    {
        try {
            $this->userService->delete($user);
        } catch (DomainException $exception) {
            return redirect()
                ->route('super-admin.users.index')
                ->with('error', $exception->getMessage());
        }

        return redirect()->route('super-admin.users.index')->with('success', 'User berhasil dihapus');
    }
}
