<?php

namespace App\Http\Requests\User;

use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Models\User;
use App\Support\RoleScopeMatrix;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('create', User::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role' => [
                'required',
                'exists:roles,name',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (! RoleScopeMatrix::isRoleCompatibleWithScope((string) $value, (string) $this->input('scope'))) {
                        $fail('Role tidak sesuai dengan scope yang dipilih.');
                    }
                },
            ],
            'scope' => ['required', Rule::in(ScopeLevel::values())],
            'area_id' => [
                'required',
                Rule::exists('areas', 'id')->where(
                    fn ($query) => $query->where('level', $this->input('scope'))
                ),
            ],
        ];
    }
}
