<?php

namespace App\Domains\Wilayah\Inventaris\Requests;

use App\Http\Requests\Concerns\ParsesUiDate;
use Illuminate\Foundation\Http\FormRequest;

class StoreInventarisRequest extends FormRequest
{
    use ParsesUiDate;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'asal_barang' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
            'unit' => 'required|string|max:50',
            'tanggal_penerimaan' => [
                'nullable',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if ($value === null || $value === '') {
                        return;
                    }

                    if ($this->parseUiDate((string) $value) === null) {
                        $fail('Format tanggal harus DD/MM/YYYY.');
                    }
                },
            ],
            'tempat_penyimpanan' => 'nullable|string|max:255',
            'condition' => 'required|in:baik,rusak_ringan,rusak_berat',
        ];
    }

    protected function uiDateFields(): array
    {
        return ['tanggal_penerimaan'];
    }
}
