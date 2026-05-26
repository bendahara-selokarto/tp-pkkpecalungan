<?php

namespace App\Domains\Wilayah\Simulasi\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBukuTamuSimulasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'visit_date' => ['required', 'date'],
            'description' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'max:5120'], // 5MB limit
        ];
    }
}
