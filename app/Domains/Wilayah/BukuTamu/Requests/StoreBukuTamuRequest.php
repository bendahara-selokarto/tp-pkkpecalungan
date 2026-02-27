<?php

namespace App\Domains\Wilayah\BukuTamu\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBukuTamuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'visit_date' => 'required|date_format:Y-m-d',
            'guest_name' => 'required|string|max:255',
            'purpose' => 'required|string|max:255',
            'institution' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ];
    }
}
