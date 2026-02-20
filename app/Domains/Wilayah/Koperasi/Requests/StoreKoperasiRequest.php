<?php

namespace App\Domains\Wilayah\Koperasi\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreKoperasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_koperasi' => 'required|string|max:255',
            'jenis_usaha' => 'required|string|max:255',
            'berbadan_hukum' => 'required|boolean',
            'belum_berbadan_hukum' => 'required|boolean',
            'jumlah_anggota_l' => 'required|integer|min:0',
            'jumlah_anggota_p' => 'required|integer|min:0',
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $berbadanHukum = $this->boolean('berbadan_hukum');
                $belumBerbadanHukum = $this->boolean('belum_berbadan_hukum');

                if ($berbadanHukum === $belumBerbadanHukum) {
                    $validator->errors()->add('berbadan_hukum', 'Status hukum harus dipilih salah satu.');
                }
            },
        ];
    }
}


