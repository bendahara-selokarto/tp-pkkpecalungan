<?php

namespace App\Domains\Wilayah\Dashboard\Observers;

use App\Domains\Wilayah\Dashboard\Services\DashboardDocumentCacheVersionService;

class InvalidateDashboardDocumentCacheObserver
{
    public function __construct(
        private readonly DashboardDocumentCacheVersionService $dashboardDocumentCacheVersionService
    ) {
    }

    public function created(mixed $model): void
    {
        $this->dashboardDocumentCacheVersionService->bumpVersion();
    }

    public function updated(mixed $model): void
    {
        $this->dashboardDocumentCacheVersionService->bumpVersion();
    }

    public function deleted(mixed $model): void
    {
        $this->dashboardDocumentCacheVersionService->bumpVersion();
    }

    public function restored(mixed $model): void
    {
        $this->dashboardDocumentCacheVersionService->bumpVersion();
    }

    public function forceDeleted(mixed $model): void
    {
        $this->dashboardDocumentCacheVersionService->bumpVersion();
    }
}
