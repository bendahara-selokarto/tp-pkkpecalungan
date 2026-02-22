<?php

namespace App\Domains\Wilayah\KaderKhusus\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKaderKhususRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date_format:Y-m-d|before_or_equal:today',
            'status_perkawinan' => 'required|in:kawin,tidak_kawin',
            'alamat' => 'required|string',
            'pendidikan' => 'required|string|max:255',
            'jenis_kader_khusus' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ];
    }
}
