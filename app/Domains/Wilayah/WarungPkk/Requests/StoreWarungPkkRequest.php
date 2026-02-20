<?php

namespace App\Domains\Wilayah\WarungPkk\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWarungPkkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_warung_pkk' => 'required|string|max:255',
            'nama_pengelola' => 'required|string|max:255',
            'komoditi' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'volume' => 'required|string|max:255',
        ];
    }
}
