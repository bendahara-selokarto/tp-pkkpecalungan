<?php

namespace App\Domains\Wilayah\AgendaSuratTugas\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListAgendaSuratTugasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function perPage(): int
    {
        return (int) $this->input('per_page', 10);
    }
}
