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

