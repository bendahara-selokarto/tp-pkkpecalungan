<?php

namespace App\Domains\Wilayah\Activities\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_kegiatan' => 'required|string|max:255',
            'deskripsi'     => 'nullable|string',
            'tanggal'       => 'required|date',
        ];
    }
}
