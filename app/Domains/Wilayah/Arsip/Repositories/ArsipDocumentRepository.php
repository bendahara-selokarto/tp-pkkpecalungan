<?php

namespace App\Domains\Wilayah\Arsip\Repositories;

use Carbon\CarbonImmutable;

class ArsipDocumentRepository implements ArsipDocumentRepositoryInterface
{
    /**
     * @var list<string>
     */
    private const ALLOWED_EXTENSIONS = [
        'pdf',
        'doc',
        'docx',
        'xls',
        'xlsx',
    ];

    public function listDocuments(): array
    {
        $referenceDirectory = base_path('docs/referensi');
        if (! is_dir($referenceDirectory)) {
            return [];
        }

        $filePaths = glob($referenceDirectory.DIRECTORY_SEPARATOR.'*');
        if (! is_array($filePaths)) {
            return [];
        }

        return collect($filePaths)
            ->filter(static fn ($path): bool => is_string($path) && is_file($path))
            ->map(fn (string $path): ?array => $this->buildDocumentPayload($path))
            ->filter(static fn ($document): bool => is_array($document))
            ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->all();
    }

    public function findDocumentByName(string $documentName): ?array
    {
        $normalizedName = $this->normalizeDocumentName($documentName);
        if ($normalizedName === null) {
            return null;
        }

        return collect($this->listDocuments())
            ->first(static fn (array $document): bool => $document['name'] === $normalizedName);
    }

    /**
     * @return array{
     *     name: string,
     *     path: string,
     *     extension: string,
     *     size_bytes: int,
     *     last_modified_at: CarbonImmutable|null
     * }|null
     */
    private function buildDocumentPayload(string $path): ?array
    {
        $name = basename($path);
        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if (! in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            return null;
        }

        $lastModifiedTimestamp = @filemtime($path);
        $lastModifiedAt = is_int($lastModifiedTimestamp)
            ? CarbonImmutable::createFromTimestamp($lastModifiedTimestamp)
            : null;

        return [
            'name' => $name,
            'path' => $path,
            'extension' => $extension,
            'size_bytes' => (int) filesize($path),
            'last_modified_at' => $lastModifiedAt,
        ];
    }

    private function normalizeDocumentName(string $documentName): ?string
    {
        $trimmedName = trim($documentName);
        if ($trimmedName === '') {
            return null;
        }

        $basename = basename($trimmedName);

        return $basename === $trimmedName ? $basename : null;
    }
}
