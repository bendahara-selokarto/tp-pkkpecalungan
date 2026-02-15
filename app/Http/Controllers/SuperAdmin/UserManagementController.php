<?php

namespace App\Http\Controllers\SuperAdmin;


use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Services\User\UserService;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    
    public function __construct(
        protected UserService $userService
    ) {
        $this->authorizeResource(User::class, 'user');
    }

    public function index()
    {
        return view('super-admin.users.index', [
            'users' => User::with('roles')->paginate(10)
        ]);
    }

    public function create()
    {
        $roles = Role::all();
        $areas = Area::orderBy('level')->orderBy('name')->get();

        return view('super-admin.users.create', compact('roles', 'areas'));
    }

    public function store(StoreUserRequest $request)
    {
        $this->userService->create($request->validated());

        return redirect()->back()->with('success', 'User berhasil dibuat');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $areas = Area::orderBy('level')->orderBy('name')->get();

        return view('super-admin.users.edit', compact('user', 'roles', 'areas'));
    }
    
    public function update(UpdateUserRequest $request, User $user)
    {
        $this->userService->update($user, $request->validated());

        return redirect()->back()->with('success', 'User berhasil diupdate');
    }

    public function destroy(User $user)
    {
        $this->userService->delete($user);

        return redirect()->back()->with('success', 'User berhasil dihapus');
    }
}
