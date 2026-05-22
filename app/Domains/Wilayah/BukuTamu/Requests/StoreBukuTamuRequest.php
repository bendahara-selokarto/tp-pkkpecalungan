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
            'description' => 'required|string',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ];
    }
}
