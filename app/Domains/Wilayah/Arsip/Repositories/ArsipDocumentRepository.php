<?php

namespace App\Domains\Wilayah\Arsip\Repositories;

use App\Domains\Wilayah\Arsip\Models\ArsipDocument;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ArsipDocumentRepository implements ArsipDocumentRepositoryInterface
{
    public function listPublished(): Collection
    {
        return ArsipDocument::query()
            ->where('is_published', true)
            ->orderByDesc('published_at')
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->get();
    }

    public function paginateForManagement(int $perPage = 10): LengthAwarePaginator
    {
        return ArsipDocument::query()
            ->orderByDesc('is_published')
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function store(array $payload): ArsipDocument
    {
        return ArsipDocument::create($payload);
    }

    public function update(ArsipDocument $arsipDocument, array $payload): ArsipDocument
    {
        $arsipDocument->update($payload);

        return $arsipDocument->refresh();
    }

    public function delete(ArsipDocument $arsipDocument): void
    {
        $arsipDocument->delete();
    }

    public function incrementDownloadCount(ArsipDocument $arsipDocument): void
    {
        $arsipDocument->increment('download_count');
    }
}
