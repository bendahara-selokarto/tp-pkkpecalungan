<?php

namespace App\Domains\Wilayah\Simulasi\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBukuTamuSimulasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'visit_date' => ['required', 'date'],
            'guest_name' => ['required', 'string', 'max:255'],
            'purpose' => ['required', 'string', 'max:255'],
            'institution' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'max:5120'],
        ];
    }
}
