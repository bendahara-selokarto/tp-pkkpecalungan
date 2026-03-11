<?php

namespace App\Domains\Wilayah\BkbKegiatan\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBkbKegiatanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jumlah_kelompok' => 'required|integer|min:0',
            'jumlah_ibu_peserta' => 'required|integer|min:0',
            'jumlah_ape_set' => 'required|integer|min:0',
            'jumlah_kelompok_simulasi' => 'required|integer|min:0',
            'keterangan' => 'nullable|string|max:1000',
        ];
    }
}
