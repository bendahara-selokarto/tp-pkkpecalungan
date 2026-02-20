<?php

namespace App\Domains\Wilayah\SimulasiPenyuluhan\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSimulasiPenyuluhanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_kegiatan' => 'required|string|max:255',
            'jenis_simulasi_penyuluhan' => 'required|string|max:255',
            'jumlah_kelompok' => 'required|integer|min:0',
            'jumlah_sosialisasi' => 'required|integer|min:0',
            'jumlah_kader_l' => 'required|integer|min:0',
            'jumlah_kader_p' => 'required|integer|min:0',
            'keterangan' => 'nullable|string',
        ];
    }
}
