<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAreaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('super-admin');
    }

    public function rules(): array
    {
        return [
            'chairperson_name' => ['nullable', 'string', 'max:255'],
            'chairperson_role' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'chairperson_name' => 'Nama Ketua TP PKK',
            'chairperson_role' => 'Jabatan Ketua TP PKK',
        ];
    }
}
