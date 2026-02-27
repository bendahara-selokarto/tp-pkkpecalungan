<?php

namespace App\Domains\Wilayah\BukuNotulenRapat\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBukuNotulenRapatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'entry_date' => 'required|date_format:Y-m-d',
            'title' => 'required|string|max:255',
            'person_name' => 'nullable|string|max:255',
            'institution' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ];
    }
}
