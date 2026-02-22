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
            'anggota' => 'sometimes|array',
            'anggota.*.nomor_urut' => 'nullable|integer|min:1',
            'anggota.*.nomor_registrasi' => 'nullable|string|max:100',
            'anggota.*.nomor_ktp_kk' => 'nullable|string|max:100',
            'anggota.*.nama' => 'required_with:anggota|string|max:255',
            'anggota.*.jabatan' => 'nullable|string|max:120',
            'anggota.*.jenis_kelamin' => 'nullable|in:L,P,l,p',
            'anggota.*.tempat_lahir' => 'nullable|string|max:120',
            'anggota.*.tanggal_lahir' => 'nullable|date_format:Y-m-d',
            'anggota.*.umur_tahun' => 'nullable|integer|min:0|max:150',
            'anggota.*.status_perkawinan' => 'nullable|string|max:100',
            'anggota.*.status_dalam_keluarga' => 'nullable|string|max:120',
            'anggota.*.agama' => 'nullable|string|max:100',
            'anggota.*.alamat' => 'nullable|string|max:255',
            'anggota.*.desa_kel_sejenis' => 'nullable|string|max:150',
            'anggota.*.pendidikan' => 'nullable|string|max:120',
            'anggota.*.pekerjaan' => 'nullable|string|max:120',
            'anggota.*.akseptor_kb' => 'nullable|boolean',
            'anggota.*.aktif_posyandu' => 'nullable|boolean',
            'anggota.*.ikut_bkb' => 'nullable|boolean',
            'anggota.*.memiliki_tabungan' => 'nullable|boolean',
            'anggota.*.ikut_kelompok_belajar' => 'nullable|boolean',
            'anggota.*.jenis_kelompok_belajar' => 'nullable|string|max:120',
            'anggota.*.ikut_paud' => 'nullable|boolean',
            'anggota.*.ikut_koperasi' => 'nullable|boolean',
            'anggota.*.keterangan' => 'nullable|string',
        ];
    }
}
