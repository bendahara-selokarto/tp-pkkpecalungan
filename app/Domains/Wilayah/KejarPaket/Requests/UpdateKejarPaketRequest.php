<?php

namespace App\Domains\Wilayah\KejarPaket\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKejarPaketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_kejar_paket' => 'required|string|max:255',
            'jenis_kejar_paket' => 'required|string|max:255',
            'jumlah_warga_belajar_l' => 'required|integer|min:0',
            'jumlah_warga_belajar_p' => 'required|integer|min:0',
            'jumlah_pengajar_l' => 'required|integer|min:0',
            'jumlah_pengajar_p' => 'required|integer|min:0',
        ];
    }
}





