<?php

namespace App\Domains\Wilayah\BukuKonsultasi\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBukuKonsultasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'activity_date' => ['required', 'date'],
            'description' => ['required', 'string'],
            'disposition' => ['nullable', 'string'],
        ];
    }
}
