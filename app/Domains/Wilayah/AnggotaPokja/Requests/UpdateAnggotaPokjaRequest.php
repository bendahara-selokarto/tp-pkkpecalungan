<?php

namespace App\Domains\Wilayah\AnggotaPokja\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Throwable;

class UpdateAnggotaPokjaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => [
                'required',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $tanggalLahir = $this->parseDate((string) $value);
                    if ($tanggalLahir === null) {
                        $fail('Format tanggal harus DD/MM/YYYY.');
                        return;
                    }

                    if ($tanggalLahir->isAfter(today())) {
                        $fail('Tanggal lahir tidak boleh lebih dari hari ini.');
                    }
                },
            ],
            'status_perkawinan' => 'required|in:kawin,tidak_kawin',
            'alamat' => 'required|string',
            'pendidikan' => 'required|string|max:255',
            'pekerjaan' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'pokja' => 'required|string|max:50',
        ];
    }

    protected function passedValidation(): void
    {
        $this->merge([
            'tanggal_lahir' => $this->normalizeDate($this->string('tanggal_lahir')->toString()),
        ]);
    }

    private function normalizeDate(string $value): string
    {
        return $this->parseDate($value)?->format('Y-m-d') ?? $value;
    }

    private function parseDate(string $value): ?Carbon
    {
        foreach (['d/m/Y', 'Y-m-d'] as $format) {
            try {
                $date = Carbon::createFromFormat($format, $value);
            } catch (Throwable) {
                continue;
            }

            if ($date->format($format) === $value) {
                return $date;
            }
        }

        return null;
    }
}
