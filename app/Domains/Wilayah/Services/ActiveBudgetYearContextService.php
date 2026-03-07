<?php

namespace App\Domains\Wilayah\Services;

use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ActiveBudgetYearContextService
{
    public function resolveForUser(User $user): int
    {
        $activeBudgetYear = $user->active_budget_year;

        if (is_numeric($activeBudgetYear)) {
            return (int) $activeBudgetYear;
        }

        return (int) now()->format('Y');
    }

    public function requireForAuthenticatedUser(): int
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            throw new HttpException(403, 'Tahun anggaran aktif pengguna tidak tersedia.');
        }

        return $this->resolveForUser($user);
    }
}
