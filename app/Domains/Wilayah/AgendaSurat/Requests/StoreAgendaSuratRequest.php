<?php

namespace App\Domains\Wilayah\AgendaSurat\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAgendaSuratRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jenis_surat' => 'required|in:masuk,keluar',
            'tanggal_terima' => [
                'nullable',
                'required_if:jenis_surat,masuk',
                'date_format:Y-m-d',
            ],
            'tanggal_surat' => [
                'required',
                'date_format:Y-m-d',
            ],
            'nomor_surat' => 'required|string|max:100',
            'asal_surat' => 'nullable|string|max:255|required_if:jenis_surat,masuk',
            'dari' => 'nullable|string|max:255|required_if:jenis_surat,masuk',
            'kepada' => 'nullable|string|max:255|required_if:jenis_surat,keluar',
            'perihal' => 'required|string|max:255',
            'lampiran' => 'nullable|string|max:255',
            'diteruskan_kepada' => 'nullable|string|max:255',
            'tembusan' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ];
    }
}
