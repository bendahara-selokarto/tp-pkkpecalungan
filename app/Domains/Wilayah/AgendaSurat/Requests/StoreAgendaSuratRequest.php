<?php

namespace App\Domains\Wilayah\AgendaSurat\Requests;

use App\Http\Requests\Concerns\ParsesUiDate;
use Closure;
use Illuminate\Foundation\Http\FormRequest;

class StoreAgendaSuratRequest extends FormRequest
{
    use ParsesUiDate;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jenis_surat' => 'required|in:masuk,keluar',
            'tanggal_terima' => [
                'nullable',
                'required_if:jenis_surat,masuk',
                $this->dateRule(),
            ],
            'tanggal_surat' => [
                'required',
                $this->dateRule(),
            ],
            'nomor_surat' => 'required|string|max:100',
            'asal_surat' => 'nullable|string|max:255|required_if:jenis_surat,masuk',
            'dari' => 'nullable|string|max:255|required_if:jenis_surat,masuk',
            'kepada' => 'nullable|string|max:255|required_if:jenis_surat,keluar',
            'perihal' => 'required|string|max:255',
            'lampiran' => 'nullable|string|max:255',
            'diteruskan_kepada' => 'nullable|string|max:255',
            'tembusan' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ];
    }

    protected function uiDateFields(): array
    {
        return ['tanggal_terima', 'tanggal_surat'];
    }

    private function dateRule(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            if ($value === null || $value === '') {
                return;
            }

            if ($this->parseUiDate((string) $value) === null) {
                $fail('Format tanggal harus DD/MM/YYYY.');
            }
        };
    }
}
