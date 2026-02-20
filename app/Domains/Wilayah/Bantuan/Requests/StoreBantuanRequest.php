<?php

namespace App\Domains\Wilayah\Bantuan\Requests;

use App\Http\Requests\Concerns\ParsesUiDate;
use Illuminate\Foundation\Http\FormRequest;

class StoreBantuanRequest extends FormRequest
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
            'category' => 'required|string|max:100',
            'description' => 'nullable|string',
            'source' => 'required|in:pusat,provinsi,kabupaten,pihak_ketiga,lainnya',
            'amount' => 'required|numeric|min:0',
            'received_date' => [
                'required',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if ($this->parseUiDate((string) $value) === null) {
                        $fail('Format tanggal harus DD/MM/YYYY.');
                    }
                },
            ],
        ];
    }

    protected function uiDateFields(): array
    {
        return ['received_date'];
    }
}
