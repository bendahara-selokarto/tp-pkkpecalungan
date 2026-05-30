<?php

namespace App\Domains\Wilayah\AgendaSuratTugas\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAgendaSuratTugasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nomor_surat' => ['required', 'string', 'max:255'],
            'tanggal_surat' => ['required', 'date'],
            'kepada' => ['required', 'string', 'max:255'],
            'perihal' => ['required', 'string'],
            'lampiran' => ['nullable', 'string', 'max:255'],
            'tembusan' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'max:5120'], // 5MB limit
        ];
    }
}
