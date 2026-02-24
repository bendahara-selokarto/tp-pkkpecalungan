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
        $legacyJadwalFlags = [
            'jadwal_i' => $this->boolean('jadwal_i'),
            'jadwal_ii' => $this->boolean('jadwal_ii'),
            'jadwal_iii' => $this->boolean('jadwal_iii'),
            'jadwal_iv' => $this->boolean('jadwal_iv'),
        ];

        $monthlyJadwalFlags = [];
        foreach ($this->monthlyJadwalKeys() as $key) {
            $monthlyJadwalFlags[$key] = $this->boolean($key);
        }

        if (! $this->hasAnyTruthyValue($monthlyJadwalFlags) && $this->hasAnyTruthyValue($legacyJadwalFlags)) {
            $monthlyJadwalFlags = [
                'jadwal_bulan_1' => $legacyJadwalFlags['jadwal_i'],
                'jadwal_bulan_2' => $legacyJadwalFlags['jadwal_i'],
                'jadwal_bulan_3' => $legacyJadwalFlags['jadwal_i'],
                'jadwal_bulan_4' => $legacyJadwalFlags['jadwal_ii'],
                'jadwal_bulan_5' => $legacyJadwalFlags['jadwal_ii'],
                'jadwal_bulan_6' => $legacyJadwalFlags['jadwal_ii'],
                'jadwal_bulan_7' => $legacyJadwalFlags['jadwal_iii'],
                'jadwal_bulan_8' => $legacyJadwalFlags['jadwal_iii'],
                'jadwal_bulan_9' => $legacyJadwalFlags['jadwal_iii'],
                'jadwal_bulan_10' => $legacyJadwalFlags['jadwal_iv'],
                'jadwal_bulan_11' => $legacyJadwalFlags['jadwal_iv'],
                'jadwal_bulan_12' => $legacyJadwalFlags['jadwal_iv'],
            ];
        }

        $normalizedLegacyJadwalFlags = [
            'jadwal_i' => $monthlyJadwalFlags['jadwal_bulan_1'] || $monthlyJadwalFlags['jadwal_bulan_2'] || $monthlyJadwalFlags['jadwal_bulan_3'],
            'jadwal_ii' => $monthlyJadwalFlags['jadwal_bulan_4'] || $monthlyJadwalFlags['jadwal_bulan_5'] || $monthlyJadwalFlags['jadwal_bulan_6'],
            'jadwal_iii' => $monthlyJadwalFlags['jadwal_bulan_7'] || $monthlyJadwalFlags['jadwal_bulan_8'] || $monthlyJadwalFlags['jadwal_bulan_9'],
            'jadwal_iv' => $monthlyJadwalFlags['jadwal_bulan_10'] || $monthlyJadwalFlags['jadwal_bulan_11'] || $monthlyJadwalFlags['jadwal_bulan_12'],
        ];

        $this->merge([
            ...$monthlyJadwalFlags,
            ...$normalizedLegacyJadwalFlags,
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
