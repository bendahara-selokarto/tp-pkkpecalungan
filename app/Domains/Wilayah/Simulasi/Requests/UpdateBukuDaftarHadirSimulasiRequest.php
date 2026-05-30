<?php

namespace App\Domains\Wilayah\Simulasi\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBukuDaftarHadirSimulasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'attendance_date' => ['required', 'date'],
            'title' => ['required', 'string', 'max:255'],
            'file' => ['nullable', 'file', 'max:5120'],
        ];
    }
}
