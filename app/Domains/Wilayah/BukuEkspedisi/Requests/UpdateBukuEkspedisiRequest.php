<?php

namespace App\Domains\Wilayah\BukuEkspedisi\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBukuEkspedisiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ];
    }
}
