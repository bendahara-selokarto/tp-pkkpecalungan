<?php

namespace App\Domains\Wilayah\ProgramPrioritas\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateProgramPrioritasRequest extends FormRequest
{
    /**
     * @return array<int, string>
     */
    private function monthlyJadwalKeys(): array
    {
        return array_map(
            static fn (int $month): string => "jadwal_bulan_{$month}",
            range(1, 12)
        );
    }

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
            'jadwal_bulan_1' => 'required|boolean',
            'jadwal_bulan_2' => 'required|boolean',
            'jadwal_bulan_3' => 'required|boolean',
            'jadwal_bulan_4' => 'required|boolean',
            'jadwal_bulan_5' => 'required|boolean',
            'jadwal_bulan_6' => 'required|boolean',
            'jadwal_bulan_7' => 'required|boolean',
            'jadwal_bulan_8' => 'required|boolean',
            'jadwal_bulan_9' => 'required|boolean',
            'jadwal_bulan_10' => 'required|boolean',
            'jadwal_bulan_11' => 'required|boolean',
            'jadwal_bulan_12' => 'required|boolean',
            'sumber_dana_pusat' => 'required|boolean',
            'sumber_dana_apbd' => 'required|boolean',
            'sumber_dana_swd' => 'required|boolean',
            'sumber_dana_bant' => 'required|boolean',
            'keterangan' => 'nullable|string',
        ];
    }

    protected function prepareForValidation(): void
    {
        $monthlyJadwalFlags = [];
        foreach ($this->monthlyJadwalKeys() as $key) {
            $monthlyJadwalFlags[$key] = $this->boolean($key);
        }

        $this->merge([
            ...$monthlyJadwalFlags,
            'sumber_dana_pusat' => $this->boolean('sumber_dana_pusat'),
            'sumber_dana_apbd' => $this->boolean('sumber_dana_apbd'),
            'sumber_dana_swd' => $this->boolean('sumber_dana_swd'),
            'sumber_dana_bant' => $this->boolean('sumber_dana_bant'),
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (! $this->hasAnyMonthlyJadwalWaktu()) {
                $validator->errors()->add('jadwal_bulan_1', 'Minimal satu jadwal waktu bulan (1-12) harus dipilih.');
            }

            if (! $this->hasAnySumberDana()) {
                $validator->errors()->add('sumber_dana_pusat', 'Minimal satu sumber dana harus dipilih.');
            }
        });
    }

    private function hasAnyMonthlyJadwalWaktu(): bool
    {
        foreach ($this->monthlyJadwalKeys() as $key) {
            if ($this->boolean($key)) {
                return true;
            }
        }

        return false;
    }

    private function hasAnySumberDana(): bool
    {
        return $this->boolean('sumber_dana_pusat')
            || $this->boolean('sumber_dana_apbd')
            || $this->boolean('sumber_dana_swd')
            || $this->boolean('sumber_dana_bant');
    }

    /**
     * @param  array<string, bool>  $flags
     */
    private function hasAnyTruthyValue(array $flags): bool
    {
        foreach ($flags as $flag) {
            if ($flag) {
                return true;
            }
        }

        return false;
    }
}
