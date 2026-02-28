<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\Services\RoleMenuVisibilityService;
use App\Http\Controllers\Controller;
use App\Support\RoleScopeMatrix;
use App\UseCases\SuperAdmin\ListAccessControlMatrixUseCase;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class AccessControlManagementController extends Controller
{
    public function __construct(
        private readonly ListAccessControlMatrixUseCase $listAccessControlMatrixUseCase
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $allowedRoles = collect(RoleScopeMatrix::scopedRoles())
            ->flatten()
            ->push('super-admin')
            ->unique()
            ->values()
            ->all();

        $filters = $request->validate([
            'scope' => ['nullable', Rule::in(ScopeLevel::values())],
            'role' => ['nullable', Rule::in($allowedRoles)],
            'mode' => ['nullable', Rule::in([
                RoleMenuVisibilityService::MODE_READ_WRITE,
                RoleMenuVisibilityService::MODE_READ_ONLY,
                'hidden',
            ])],
        ]);

        return Inertia::render('SuperAdmin/AccessControl/Index', $this->listAccessControlMatrixUseCase->execute($filters));
    }
}
