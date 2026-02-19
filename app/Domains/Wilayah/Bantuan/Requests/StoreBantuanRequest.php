<?php

namespace App\Domains\Wilayah\Bantuan\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Throwable;

class StoreBantuanRequest extends FormRequest
{
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
                    if ($this->parseDate((string) $value) === null) {
                        $fail('Format tanggal harus DD/MM/YYYY.');
                    }
                },
            ],
        ];
    }

    protected function passedValidation(): void
    {
        $this->merge([
            'received_date' => $this->normalizeDate($this->string('received_date')->toString()),
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
