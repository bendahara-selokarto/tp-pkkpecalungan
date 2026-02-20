<?php

namespace App\Domains\Wilayah\DataKeluarga\Requests;

use App\Domains\Wilayah\DataKeluarga\Models\DataKeluarga;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDataKeluargaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kategori_keluarga' => ['required', 'string', Rule::in(DataKeluarga::kategoriOptions())],
            'jumlah_keluarga' => 'required|integer|min:0',
            'keterangan' => 'nullable|string',
        ];
    }
}

