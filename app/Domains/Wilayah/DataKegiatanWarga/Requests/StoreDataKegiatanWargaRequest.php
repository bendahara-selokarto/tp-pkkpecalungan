<?php

namespace App\Domains\Wilayah\DataKegiatanWarga\Requests;

use App\Domains\Wilayah\DataKegiatanWarga\Models\DataKegiatanWarga;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDataKegiatanWargaRequest extends FormRequest
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
            'source_module' => 'nullable|string',
            'source_id' => 'nullable|integer',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'aktivitas' => $this->boolean('aktivitas'),
        ]);
    }
}
