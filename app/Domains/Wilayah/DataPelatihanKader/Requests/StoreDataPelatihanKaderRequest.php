<?php

namespace App\Domains\Wilayah\DataPelatihanKader\Requests;

use App\Domains\Wilayah\DataPelatihanKader\Models\DataPelatihanKader;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDataPelatihanKaderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nomor_registrasi' => 'required|string|max:100',
            'nama_lengkap_kader' => 'required|string|max:255',
            'tanggal_masuk_tp_pkk' => 'required|string|max:100',
            'jabatan_fungsi' => 'required|string|max:255',
            'nomor_urut_pelatihan' => 'required|integer|min:1',
            'judul_pelatihan' => 'required|string|max:255',
            'jenis_kriteria_kaderisasi' => 'required|string|max:255',
            'tahun_penyelenggaraan' => 'required|integer|digits:4|min:1900|max:2100',
            'institusi_penyelenggara' => 'required|string|max:255',
            'status_sertifikat' => ['required', 'string', Rule::in(DataPelatihanKader::statusSertifikatOptions())],
        ];
    }
}
