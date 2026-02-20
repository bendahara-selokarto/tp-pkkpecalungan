<?php

namespace App\Domains\Wilayah\ProgramPrioritas\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreProgramPrioritasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'program' => 'required|string|max:255',
            'prioritas_program' => 'required|string|max:255',
            'kegiatan' => 'required|string',
            'sasaran_target' => 'required|string',
            'jadwal_i' => 'required|boolean',
            'jadwal_ii' => 'required|boolean',
            'jadwal_iii' => 'required|boolean',
            'jadwal_iv' => 'required|boolean',
            'sumber_dana_pusat' => 'required|boolean',
            'sumber_dana_apbd' => 'required|boolean',
            'sumber_dana_swd' => 'required|boolean',
            'sumber_dana_bant' => 'required|boolean',
            'keterangan' => 'nullable|string',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'jadwal_i' => $this->boolean('jadwal_i'),
            'jadwal_ii' => $this->boolean('jadwal_ii'),
            'jadwal_iii' => $this->boolean('jadwal_iii'),
            'jadwal_iv' => $this->boolean('jadwal_iv'),
            'sumber_dana_pusat' => $this->boolean('sumber_dana_pusat'),
            'sumber_dana_apbd' => $this->boolean('sumber_dana_apbd'),
            'sumber_dana_swd' => $this->boolean('sumber_dana_swd'),
            'sumber_dana_bant' => $this->boolean('sumber_dana_bant'),
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (! $this->hasAnyJadwalWaktu()) {
                $validator->errors()->add('jadwal_i', 'Minimal satu jadwal waktu (I/II/III/IV) harus dipilih.');
            }

            if (! $this->hasAnySumberDana()) {
                $validator->errors()->add('sumber_dana_pusat', 'Minimal satu sumber dana harus dipilih.');
            }
        });
    }

    private function hasAnyJadwalWaktu(): bool
    {
        return $this->boolean('jadwal_i')
            || $this->boolean('jadwal_ii')
            || $this->boolean('jadwal_iii')
            || $this->boolean('jadwal_iv');
    }

    private function hasAnySumberDana(): bool
    {
        return $this->boolean('sumber_dana_pusat')
            || $this->boolean('sumber_dana_apbd')
            || $this->boolean('sumber_dana_swd')
            || $this->boolean('sumber_dana_bant');
    }
}
