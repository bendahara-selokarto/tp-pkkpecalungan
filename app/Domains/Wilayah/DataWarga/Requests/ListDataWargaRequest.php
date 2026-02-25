<?php

namespace App\Domains\Wilayah\DataWarga\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListDataWargaRequest extends FormRequest
{
    /**
     * @var list<int>
     */
    private const ALLOWED_PER_PAGE = [10, 25, 50];

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $page = (int) $this->query('page', 1);
        if ($page < 1) {
            $page = 1;
        }

        $perPage = (int) $this->query('per_page', self::ALLOWED_PER_PAGE[0]);
        if (! in_array($perPage, self::ALLOWED_PER_PAGE, true)) {
            $perPage = self::ALLOWED_PER_PAGE[0];
        }

        $this->merge([
            'page' => $page,
            'per_page' => $perPage,
        ]);
    }

    public function rules(): array
    {
        return [
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'in:' . implode(',', self::ALLOWED_PER_PAGE)],
        ];
    }

    public function perPage(): int
    {
        return (int) $this->validated('per_page', self::ALLOWED_PER_PAGE[0]);
    }
}
