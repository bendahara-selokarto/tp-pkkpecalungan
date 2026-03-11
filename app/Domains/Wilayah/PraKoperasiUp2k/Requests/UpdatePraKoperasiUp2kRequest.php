<?php

namespace App\Domains\Wilayah\PraKoperasiUp2k\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePraKoperasiUp2kRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tingkat' => 'required|string|in:pemula,madya,utama,mandiri',
            'jumlah_kelompok' => 'required|integer|min:0',
            'jumlah_peserta' => 'required|integer|min:0',
            'keterangan' => 'nullable|string|max:1000',
        ];
    }
}
