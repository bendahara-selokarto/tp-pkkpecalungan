<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Domains\Wilayah\AccessControl\Actions\RollbackPilotModuleOverrideAction;
use App\Domains\Wilayah\AccessControl\Actions\UpsertPilotModuleOverrideAction;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\Services\RoleMenuVisibilityService;
use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\RollbackPilotModuleOverrideRequest;
use App\Http\Requests\SuperAdmin\UpdatePilotModuleOverrideRequest;
use App\Support\RoleScopeMatrix;
use App\Support\RoleLabelFormatter;
use App\UseCases\SuperAdmin\ListAccessControlMatrixUseCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class AccessControlManagementController extends Controller
{
    public function __construct(
        private readonly ListAccessControlMatrixUseCase $listAccessControlMatrixUseCase,
        private readonly UpsertPilotModuleOverrideAction $upsertPilotModuleOverrideAction,
        private readonly RollbackPilotModuleOverrideAction $rollbackPilotModuleOverrideAction
    ) {
    }

    public function index(Request $request): Response
    {
        $allowedRoles = collect(RoleScopeMatrix::scopedRoles())
            ->flatten()
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
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', Rule::in(ListAccessControlMatrixUseCase::PER_PAGE_OPTIONS)],
        ]);

        return Inertia::render('SuperAdmin/AccessControl/Index', $this->listAccessControlMatrixUseCase->execute($filters));
    }

    public function updatePilotCatatanKeluarga(UpdatePilotModuleOverrideRequest $request): RedirectResponse
    {
        return $this->updatePilotByModuleSlug($request, RoleMenuVisibilityService::PILOT_MODULE_SLUG);
    }

    public function updatePilotModule(UpdatePilotModuleOverrideRequest $request, string $moduleSlug): RedirectResponse
    {
        return $this->updatePilotByModuleSlug($request, $moduleSlug);
    }

    public function rollbackPilotCatatanKeluarga(RollbackPilotModuleOverrideRequest $request): RedirectResponse
    {
        return $this->rollbackPilotByModuleSlug($request, RoleMenuVisibilityService::PILOT_MODULE_SLUG);
    }

    public function rollbackPilotModule(RollbackPilotModuleOverrideRequest $request, string $moduleSlug): RedirectResponse
    {
        return $this->rollbackPilotByModuleSlug($request, $moduleSlug);
    }

    private function updatePilotByModuleSlug(UpdatePilotModuleOverrideRequest $request, string $moduleSlug): RedirectResponse
    {
        $scope = (string) $request->validated('scope');
        $role = (string) $request->validated('role');
        $mode = (string) $request->validated('mode');

        $result = $this->upsertPilotModuleOverrideAction->execute(
            $scope,
            $role,
            $moduleSlug,
            $mode,
            $request->user()
        );

        return redirect()
            ->back()
            ->with(
                'success',
                sprintf(
                    'Pilot override %s untuk %s (%s) diperbarui: %s -> %s.',
                    $this->moduleLabel($moduleSlug),
                    RoleLabelFormatter::label($role),
                    ucfirst($scope),
                    $this->modeLabel($result['before_mode']),
                    $this->modeLabel($result['after_mode'])
                )
            );
    }

    private function rollbackPilotByModuleSlug(RollbackPilotModuleOverrideRequest $request, string $moduleSlug): RedirectResponse
    {
        $scope = (string) $request->validated('scope');
        $role = (string) $request->validated('role');

        $result = $this->rollbackPilotModuleOverrideAction->execute(
            $scope,
            $role,
            $moduleSlug,
            $request->user()
        );

        return redirect()
            ->back()
            ->with(
                'success',
                sprintf(
                    'Rollback pilot %s untuk %s (%s): %s -> %s.',
                    $this->moduleLabel($moduleSlug),
                    RoleLabelFormatter::label($role),
                    ucfirst($scope),
                    $this->modeLabel($result['before_mode']),
                    $this->modeLabel($result['after_mode'])
                )
            );
    }

    private function moduleLabel(string $moduleSlug): string
    {
        return ucfirst(str_replace('-', ' ', $moduleSlug));
    }

    private function modeLabel(string $mode): string
    {
        return match ($mode) {
            RoleMenuVisibilityService::MODE_READ_WRITE => 'Baca dan Tulis',
            RoleMenuVisibilityService::MODE_READ_ONLY => 'Baca Saja',
            RoleMenuVisibilityService::MODE_HIDDEN => 'Tidak Tampil',
            default => $mode,
        };
    }
}
