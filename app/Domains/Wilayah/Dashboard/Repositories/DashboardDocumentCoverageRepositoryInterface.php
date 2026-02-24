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
}
