<?php

namespace App\Domains\Wilayah\Posyandu\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePosyanduRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_posyandu' => 'required|string|max:255',
            'nama_pengelola' => 'required|string|max:255',
            'nama_sekretaris' => 'required|string|max:255',
            'jenis_posyandu' => 'required|string|max:255',
            'jumlah_kader' => 'required|integer|min:0',
            'jenis_kegiatan' => 'required|string|max:255',
            'frekuensi_layanan' => 'required|integer|min:0',
            'jumlah_pengunjung_l' => 'required|integer|min:0',
            'jumlah_pengunjung_p' => 'required|integer|min:0',
            'jumlah_petugas_l' => 'required|integer|min:0',
            'jumlah_petugas_p' => 'required|integer|min:0',
        ];
    }
}





