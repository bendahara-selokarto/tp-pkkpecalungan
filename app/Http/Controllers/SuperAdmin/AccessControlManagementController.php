<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Domains\Wilayah\AccessControl\Actions\RollbackPilotCatatanKeluargaOverrideAction;
use App\Domains\Wilayah\AccessControl\Actions\UpsertPilotCatatanKeluargaOverrideAction;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\Services\RoleMenuVisibilityService;
use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\RollbackPilotCatatanKeluargaOverrideRequest;
use App\Http\Requests\SuperAdmin\UpdatePilotCatatanKeluargaOverrideRequest;
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
        private readonly UpsertPilotCatatanKeluargaOverrideAction $upsertPilotCatatanKeluargaOverrideAction,
        private readonly RollbackPilotCatatanKeluargaOverrideAction $rollbackPilotCatatanKeluargaOverrideAction
    ) {
    }

    public function index(Request $request): Response
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

    public function updatePilotCatatanKeluarga(UpdatePilotCatatanKeluargaOverrideRequest $request): RedirectResponse
    {
        $scope = (string) $request->validated('scope');
        $role = (string) $request->validated('role');
        $mode = (string) $request->validated('mode');

        $result = $this->upsertPilotCatatanKeluargaOverrideAction->execute(
            $scope,
            $role,
            $mode,
            $request->user()
        );

        return redirect()
            ->back()
            ->with(
                'success',
                sprintf(
                    'Pilot override catatan keluarga untuk %s (%s) diperbarui: %s -> %s.',
                    RoleLabelFormatter::label($role),
                    ucfirst($scope),
                    $this->modeLabel($result['before_mode']),
                    $this->modeLabel($result['after_mode'])
                )
            );
    }

    public function rollbackPilotCatatanKeluarga(RollbackPilotCatatanKeluargaOverrideRequest $request): RedirectResponse
    {
        $scope = (string) $request->validated('scope');
        $role = (string) $request->validated('role');

        $result = $this->rollbackPilotCatatanKeluargaOverrideAction->execute(
            $scope,
            $role,
            $request->user()
        );

        return redirect()
            ->back()
            ->with(
                'success',
                sprintf(
                    'Rollback pilot catatan keluarga untuk %s (%s): %s -> %s.',
                    RoleLabelFormatter::label($role),
                    ucfirst($scope),
                    $this->modeLabel($result['before_mode']),
                    $this->modeLabel($result['after_mode'])
                )
            );
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
