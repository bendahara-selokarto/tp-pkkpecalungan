<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Dashboard cache
    |--------------------------------------------------------------------------
    |
    | Cache time-to-live for document coverage blocks. The value is purposely
    | short to balance heavy aggregate queries and freshness on dashboard.
    |
    */
    'documents_cache_ttl_seconds' => (int) env('DASHBOARD_DOCUMENTS_CACHE_TTL_SECONDS', 60),
];

