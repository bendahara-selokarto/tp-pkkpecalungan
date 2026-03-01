<?php

namespace App\Http\Requests\SuperAdmin;

use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\Services\RoleMenuVisibilityService;
use App\Support\RoleScopeMatrix;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePilotModuleOverrideRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->hasRole('super-admin');
    }

    public function rules(): array
    {
        return [
            'scope' => ['required', Rule::in(ScopeLevel::values())],
            'role' => [
                'required',
                'string',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $scope = (string) $this->input('scope', '');
                    $role = (string) $value;

                    if ($role === 'super-admin' || ! RoleScopeMatrix::isRoleCompatibleWithScope($role, $scope)) {
                        $fail('Role tidak kompatibel dengan scope.');
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

    protected function passedValidation(): void
    {
        $moduleSlug = (string) $this->route('moduleSlug', RoleMenuVisibilityService::PILOT_MODULE_SLUG);
        if (! in_array($moduleSlug, RoleMenuVisibilityService::pilotModuleSlugs(), true)) {
            abort(422, 'Modul pilot tidak valid.');
        }
    }
}
