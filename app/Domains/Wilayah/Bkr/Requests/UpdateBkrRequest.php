<?php

namespace App\Domains\Wilayah\Bkr\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBkrRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'desa' => 'required|string|max:255',
            'nama_bkr' => 'required|string|max:255',
            'no_tgl_sk' => 'required|string|max:255',
            'nama_ketua_kelompok' => 'required|string|max:255',
            'jumlah_anggota' => 'required|integer|min:0',
            'kegiatan' => 'required|string',
        ];
    }
}

