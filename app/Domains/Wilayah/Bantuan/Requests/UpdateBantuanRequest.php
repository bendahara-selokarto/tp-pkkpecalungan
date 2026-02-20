<?php

namespace App\Domains\Wilayah\Bantuan\Requests;

use App\Http\Requests\Concerns\ParsesUiDate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBantuanRequest extends FormRequest
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

    protected function passedValidation(): void
    {
        $this->merge([
            'received_date' => $this->normalizeUiDate($this->string('received_date')->toString()),
        ]);
    }
}
