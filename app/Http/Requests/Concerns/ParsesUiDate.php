<?php

namespace App\Http\Requests\Concerns;

use Carbon\Carbon;
use Throwable;

trait ParsesUiDate
{
    protected function normalizeUiDate(string $value): string
    {
        return $this->parseUiDate($value)?->format('Y-m-d') ?? $value;
    }

    protected function parseUiDate(string $value): ?Carbon
    {
        $trimmed = trim($value);

        if (! preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $trimmed)) {
            return null;
        }

        try {
            $date = Carbon::createFromFormat('!d/m/Y', $trimmed);
        } catch (Throwable) {
            return null;
        }

        return $date->format('d/m/Y') === $trimmed ? $date : null;
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
