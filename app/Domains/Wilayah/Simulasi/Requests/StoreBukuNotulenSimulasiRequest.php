<?php

namespace App\Domains\Wilayah\Simulasi\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBukuNotulenSimulasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'entry_date' => ['required', 'date'],
            'title' => ['required', 'string', 'max:255'],
            'person_name' => ['nullable', 'string', 'max:255'],
            'institution' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'max:5120'],
        ];
    }
}
