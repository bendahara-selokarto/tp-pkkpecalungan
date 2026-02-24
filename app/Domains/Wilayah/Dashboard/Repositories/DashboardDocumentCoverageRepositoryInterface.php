<?php

namespace App\Domains\Wilayah\Dashboard\Repositories;

use App\Models\User;

interface DashboardDocumentCoverageRepositoryInterface
{
    public function buildForUser(User $user): array;

    /**
     * @return list<string>
     */
    public function trackedModuleSlugs(): array;

    /**
     * @param list<string> $moduleSlugs
     * @return list<array{
     *     desa_id: int,
     *     desa_name: string,
     *     total: int,
     *     per_module: array<string, int>
     * }>
     */
    public function buildGroupBreakdownByDesa(User $user, array $moduleSlugs, ?int $month = null): array;
}
