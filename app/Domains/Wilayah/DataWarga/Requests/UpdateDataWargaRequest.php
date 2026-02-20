<?php

namespace App\Domains\Wilayah\DataWarga\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDataWargaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'dasawisma' => 'required|string|max:255',
            'nama_kepala_keluarga' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'jumlah_warga_laki_laki' => 'required|integer|min:0',
            'jumlah_warga_perempuan' => 'required|integer|min:0',
            'keterangan' => 'nullable|string',
        ];
    }
}
