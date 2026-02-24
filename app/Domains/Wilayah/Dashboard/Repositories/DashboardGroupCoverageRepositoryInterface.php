<?php

namespace App\Domains\Wilayah\Dashboard\Repositories;

use App\Models\User;

interface DashboardGroupCoverageRepositoryInterface
{
    /**
     * @return list<array{
     *     desa_id: int,
     *     desa_name: string,
     *     total: int,
     *     per_module: array<string, int>
     * }>
     */
    public function buildBreakdownByDesaForGroup(User $user, string $groupKey, ?int $month = null): array;

    /**
     * @param list<string> $moduleSlugs
     * @return list<array{
     *     desa_id: int,
     *     desa_name: string,
     *     total: int,
     *     per_module: array<string, int>
     * }>
     */
    public function buildBreakdownByDesaForModules(User $user, array $moduleSlugs, ?int $month = null): array;
}
