<?php

namespace App\Domains\Wilayah\BukuTamu\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBukuTamuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'visit_date' => 'nullable|date_format:Y-m-d',
            'guest_name' => 'nullable|string|max:255',
            'purpose' => 'nullable|string|max:255',
            'institution' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ];
    }
}
