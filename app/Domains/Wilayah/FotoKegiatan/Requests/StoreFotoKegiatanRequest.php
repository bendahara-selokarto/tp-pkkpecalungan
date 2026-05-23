<?php

namespace App\Domains\Wilayah\FotoKegiatan\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFotoKegiatanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'activity_date' => ['required', 'date'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image_upload' => ['nullable', 'image', 'max:5120'], // 5MB
        ];
    }
}
