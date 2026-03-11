<?php

namespace App\Domains\Wilayah\PelatihanKaderPokjaIi\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePelatihanKaderPokjaIiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kategori_pelatihan' => 'required|string|in:lp3,tpk_3_pkk,damas_pkk',
            'jumlah_kader' => 'required|integer|min:0',
            'keterangan' => 'nullable|string|max:1000',
        ];
    }
}
