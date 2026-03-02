<?php

namespace App\Http\Requests\SuperAdmin;

use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\Services\RoleMenuVisibilityService;
use App\Support\RoleScopeMatrix;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePilotCatatanKeluargaOverrideRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->hasRole('super-admin');
    }

    public function rules(): array
    {
        /** @var RoleMenuVisibilityService $visibilityService */
        $visibilityService = app(RoleMenuVisibilityService::class);

        return [
            'module' => [
                'required',
                'string',
                Rule::in($visibilityService->overrideManageableModules()),
            ],
            'scope' => ['required', Rule::in(ScopeLevel::values())],
            'role' => [
                'required',
                'string',
                function (string $attribute, mixed $value, \Closure $fail) use ($visibilityService): void {
                    $scope = (string) $this->input('scope', '');
                    $role = (string) $value;
                    $module = (string) $this->input('module', '');

                    if ($role === 'super-admin' || ! RoleScopeMatrix::isRoleCompatibleWithScope($role, $scope)) {
                        $fail('Role tidak kompatibel dengan scope.');
                        return;
                    }

                    if (! $visibilityService->isModuleAssignableForRoleScope($module, $role, $scope)) {
                        $fail('Modul tidak kompatibel dengan role dan scope.');
                    }
                },
            ],
            'mode' => ['required', Rule::in([
                RoleMenuVisibilityService::MODE_READ_ONLY,
                RoleMenuVisibilityService::MODE_READ_WRITE,
                RoleMenuVisibilityService::MODE_HIDDEN,
            ])],
        ];
    }
}
