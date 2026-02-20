<?php

namespace App\Domains\Wilayah\PrestasiLomba\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StorePrestasiLombaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tahun' => 'required|integer|min:1900|max:2100',
            'jenis_lomba' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'prestasi_kecamatan' => 'required|boolean',
            'prestasi_kabupaten' => 'required|boolean',
            'prestasi_provinsi' => 'required|boolean',
            'prestasi_nasional' => 'required|boolean',
            'keterangan' => 'nullable|string',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'prestasi_kecamatan' => $this->boolean('prestasi_kecamatan'),
            'prestasi_kabupaten' => $this->boolean('prestasi_kabupaten'),
            'prestasi_provinsi' => $this->boolean('prestasi_provinsi'),
            'prestasi_nasional' => $this->boolean('prestasi_nasional'),
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (! $this->hasAnyPrestasi()) {
                $validator->errors()->add('prestasi_kecamatan', 'Minimal satu capaian prestasi harus dipilih.');
            }
        });
    }

    private function hasAnyPrestasi(): bool
    {
        return $this->boolean('prestasi_kecamatan')
            || $this->boolean('prestasi_kabupaten')
            || $this->boolean('prestasi_provinsi')
            || $this->boolean('prestasi_nasional');
    }
}
