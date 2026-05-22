<?php

namespace App\Domains\Wilayah\BukuAgendaSk\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBukuAgendaSkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nomor_sk' => 'required|string|max:255',
            'tanggal_sk' => 'required|date_format:Y-m-d',
            'kepada' => 'required|string|max:255',
            'perihal' => 'required|string',
            'tembusan' => 'nullable|string',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ];
    }
}
