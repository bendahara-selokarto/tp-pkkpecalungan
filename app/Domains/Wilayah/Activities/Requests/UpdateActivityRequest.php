<?php

namespace App\Domains\Wilayah\Activities\Requests;

use App\Http\Requests\Concerns\ParsesUiDate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateActivityRequest extends FormRequest
{
    use ParsesUiDate;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'activity_date' => [
                'required',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if ($this->parseUiDate((string) $value) === null) {
                        $fail('Format tanggal harus DD/MM/YYYY.');
                    }
                },
            ],
            'status' => 'required|in:draft,published',
        ];
    }

    protected function passedValidation(): void
    {
        $this->merge([
            'activity_date' => $this->normalizeUiDate($this->string('activity_date')->toString()),
        ]);
    }
}
