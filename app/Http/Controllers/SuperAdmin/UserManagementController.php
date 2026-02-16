<?php

namespace App\Http\Controllers\SuperAdmin;


use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Services\User\UserService;
use App\Domains\Wilayah\Models\Area;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Inertia\Inertia;
use Inertia\Response;

class UserManagementController extends Controller
{
    
    public function __construct(
        protected UserService $userService
    ) {
        $this->authorizeResource(User::class, 'user');
    }

    public function index(): Response
    {
        $users = User::query()
            ->with(['roles:id,name', 'area:id,name,level'])
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString()
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
        $roles = Role::all();
        $areas = Area::orderBy('level')->orderBy('name')->get();

        return Inertia::render('SuperAdmin/Users/Create', [
            'roles' => $roles->pluck('name')->values(),
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
        $roles = Role::all();
        $areas = Area::orderBy('level')->orderBy('name')->get();
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
            'roles' => $roles->pluck('name')->values(),
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
        $this->userService->delete($user);

        return redirect()->route('super-admin.users.index')->with('success', 'User berhasil dihapus');
    }
}
