<?php

namespace App\Domains\Wilayah\Activities\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'nama_petugas' => 'nullable|string|max:255',
            'jabatan_petugas' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'uraian' => 'nullable|string',
            'activity_date' => 'required|date_format:Y-m-d',
            'tempat_kegiatan' => 'nullable|string|max:255',
            'status' => 'required|in:draft,published',
            'tanda_tangan' => 'nullable|string|max:255',
        ];
    }
}
