<?php

namespace App\Http\Controllers\SuperAdmin;


use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Domains\Wilayah\Models\Area;
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
        private readonly GetUserManagementFormOptionsUseCase $getUserManagementFormOptionsUseCase
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
                'scope' => $user->scope,
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
        $roles = $this->getUserManagementFormOptionsUseCase->roles();
        $areas = $this->getUserManagementFormOptionsUseCase->areas();

        return Inertia::render('SuperAdmin/Users/Create', [
            'roles' => $roles,
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
        $roles = $this->getUserManagementFormOptionsUseCase->roles();
        $areas = $this->getUserManagementFormOptionsUseCase->areas();
        $user->load('roles:id,name');

        return Inertia::render('SuperAdmin/Users/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'scope' => $user->scope,
                'area_id' => $user->area_id,
                'roles' => $user->roles->pluck('name')->values(),
            ],
            'roles' => $roles,
            'areas' => $areas->map(fn (Area $area) => [
                'id' => $area->id,
                'name' => $area->name,
                'level' => $area->level,
            ])->values(),
        ]);
    }
    
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->userService->update($user, $request->validated());

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
