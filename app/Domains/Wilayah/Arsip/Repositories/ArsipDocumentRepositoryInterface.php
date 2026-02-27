<?php

namespace App\Domains\Wilayah\Arsip\Repositories;

use Carbon\CarbonImmutable;

interface ArsipDocumentRepositoryInterface
{
    /**
     * @return list<array{
     *     name: string,
     *     path: string,
     *     extension: string,
     *     size_bytes: int,
     *     last_modified_at: CarbonImmutable|null
     * }>
     */
    public function listDocuments(): array;

    /**
     * @return array{
     *     name: string,
     *     path: string,
     *     extension: string,
     *     size_bytes: int,
     *     last_modified_at: CarbonImmutable|null
     * }|null
     */
    public function findDocumentByName(string $documentName): ?array;
}
