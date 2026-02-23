<?php

namespace App\Domains\Wilayah\Dashboard\Services;

use Illuminate\Support\Facades\Cache;

class DashboardDocumentCacheVersionService
{
    private const CACHE_VERSION_KEY = 'dashboard:documents:cache_version';

    public function currentVersion(): int
    {
        $cached = Cache::get(self::CACHE_VERSION_KEY);
        if (is_int($cached) && $cached > 0) {
            return $cached;
        }

        Cache::forever(self::CACHE_VERSION_KEY, 1);

        return 1;
    }

    public function bumpVersion(): int
    {
        $nextVersion = $this->currentVersion() + 1;
        Cache::forever(self::CACHE_VERSION_KEY, $nextVersion);

        return $nextVersion;
    }
}
