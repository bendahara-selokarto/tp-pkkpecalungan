<?php

namespace App\Domains\Wilayah\DataKegiatanWarga\Requests;

use App\Domains\Wilayah\DataKegiatanWarga\Models\DataKegiatanWarga;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDataKegiatanWargaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kegiatan' => ['required', 'string', Rule::in(DataKegiatanWarga::kegiatanOptions())],
            'aktivitas' => 'required|boolean',
            'keterangan' => 'nullable|string',
            'is_pkg' => 'nullable|boolean',
            'is_tbc' => 'nullable|boolean',
            'source_module' => 'nullable|string',
            'source_id' => 'nullable|integer',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'aktivitas' => $this->boolean('aktivitas'),
            'is_pkg' => $this->boolean('is_pkg'),
            'is_tbc' => $this->boolean('is_tbc'),
        ]);
    }
}
