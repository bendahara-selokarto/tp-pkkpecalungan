<?php

namespace App\Domains\Wilayah\BukuEkspedisi\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBukuEkspedisiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ];
    }
}
