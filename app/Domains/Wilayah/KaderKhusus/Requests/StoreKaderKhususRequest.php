<?php

namespace App\Domains\Wilayah\KaderKhusus\Requests;

use App\Http\Requests\Concerns\ParsesUiDate;
use Illuminate\Foundation\Http\FormRequest;

class StoreKaderKhususRequest extends FormRequest
{
    use ParsesUiDate;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => [
                'required',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $tanggalLahir = $this->parseUiDate((string) $value);
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
            'jenis_kader_khusus' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ];
    }

    protected function passedValidation(): void
    {
        $this->merge([
            'tanggal_lahir' => $this->normalizeUiDate($this->string('tanggal_lahir')->toString()),
        ]);
    }
}
