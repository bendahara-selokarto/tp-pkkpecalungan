<?php

namespace App\Domains\Wilayah\LiterasiWarga\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLiterasiWargaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jumlah_tiga_buta' => 'required|integer|min:0',
            'keterangan' => 'nullable|string|max:1000',
        ];
    }
}
