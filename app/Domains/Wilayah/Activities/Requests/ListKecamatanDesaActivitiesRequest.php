<?php

namespace App\Domains\Wilayah\Activities\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListKecamatanDesaActivitiesRequest extends FormRequest
{
    /**
     * @var list<int>
     */
    private const ALLOWED_PER_PAGE = [10, 25, 50];

    /**
     * @var list<string>
     */
    private const ALLOWED_STATUS = ['draft', 'published'];

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

        $desaId = (int) $this->query('desa_id', 0);
        if ($desaId < 1) {
            $desaId = null;
        }

        $status = strtolower(trim((string) $this->query('status', '')));
        if (! in_array($status, self::ALLOWED_STATUS, true)) {
            $status = null;
        }

        $keyword = trim((string) $this->query('q', ''));
        if ($keyword === '') {
            $keyword = null;
        }

        $this->merge([
            'page' => $page,
            'per_page' => $perPage,
            'desa_id' => $desaId,
            'status' => $status,
            'q' => $keyword,
        ]);
    }

    public function rules(): array
    {
        return [
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'in:' . implode(',', self::ALLOWED_PER_PAGE)],
            'desa_id' => [
                'nullable',
                'integer',
                Rule::exists('areas', 'id')->where(static fn ($query) => $query->where('level', 'desa')),
            ],
            'status' => ['nullable', 'string', Rule::in(self::ALLOWED_STATUS)],
            'q' => ['nullable', 'string', 'max:150'],
        ];
    }

    public function perPage(): int
    {
        return (int) $this->validated('per_page', self::ALLOWED_PER_PAGE[0]);
    }

    public function desaId(): ?int
    {
        $value = $this->validated('desa_id');

        return is_numeric($value) ? (int) $value : null;
    }

    public function status(): ?string
    {
        $value = $this->validated('status');

        return is_string($value) ? $value : null;
    }

    public function keyword(): ?string
    {
        $value = $this->validated('q');

        return is_string($value) ? $value : null;
    }
}
