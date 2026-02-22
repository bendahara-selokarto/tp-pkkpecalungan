<?php

namespace App\Http\Requests\Concerns;

use Carbon\Carbon;
use Throwable;

trait ParsesUiDate
{
    /**
     * @deprecated Kebijakan canonical aktif memakai validasi strict `date_format:Y-m-d`
     *             per request. Trait ini dipertahankan sementara untuk kompatibilitas lama.
     */
    protected function normalizeUiDate(string $value): string
    {
        return $this->parseUiDate($value)?->format('Y-m-d') ?? $value;
    }

    protected function parseUiDate(string $value): ?Carbon
    {
        $trimmed = trim($value);

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $trimmed)) {
            return $this->parseDateByFormat($trimmed, '!Y-m-d', 'Y-m-d');
        }

        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $trimmed)) {
            return $this->parseDateByFormat($trimmed, '!d/m/Y', 'd/m/Y');
        }

        return null;
    }

    private function parseDateByFormat(string $value, string $carbonFormat, string $expectedFormat): ?Carbon
    {
        try {
            $date = Carbon::createFromFormat($carbonFormat, $value);
        } catch (Throwable) {
            return null;
        }

        return $date->format($expectedFormat) === $value ? $date : null;
    }

    protected function uiDateFields(): array
    {
        return [];
    }

    public function validated($key = null, $default = null): mixed
    {
        $validated = parent::validated($key, $default);

        if ($key !== null || ! is_array($validated)) {
            return $validated;
        }

        foreach ($this->uiDateFields() as $field) {
            if (! array_key_exists($field, $validated) || ! is_string($validated[$field])) {
                continue;
            }

            $normalized = $this->normalizeUiDate($validated[$field]);
            $validated[$field] = $normalized;
        }

        return $validated;
    }
}
