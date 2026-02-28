<?php

namespace App\Domains\Wilayah\Arsip\UseCases;

use App\Domains\Wilayah\Arsip\Repositories\ArsipDocumentRepositoryInterface;
use App\Models\User;

class ListArsipDocumentsUseCase
{
    public function __construct(
        private readonly ArsipDocumentRepositoryInterface $arsipDocumentRepository
    ) {
    }

    /**
     * @return list<array{
     *     id: int,
     *     title: string,
     *     description: string|null,
     *     original_name: string,
     *     name: string,
     *     extension: string,
     *     size_bytes: int,
     *     is_global: bool,
     *     owner_name: string,
     *     area_name: string|null,
     *     can_manage: bool,
     *     updated_at: string|null,
     *     download_count: int
     * }>
     */
    public function execute(User $user): array
    {
        return $this->arsipDocumentRepository->listVisibleForUser($user)
            ->map(static function ($document) use ($user): array {
                return [
                    'id' => (int) $document->id,
                    'title' => (string) $document->title,
                    'description' => $document->description,
                    'original_name' => (string) $document->original_name,
                    'name' => (string) $document->original_name,
                    'extension' => strtoupper((string) $document->extension),
                    'size_bytes' => (int) $document->size_bytes,
                    'is_global' => (bool) $document->is_global,
                    'owner_name' => (string) ($document->creator?->name ?? '-'),
                    'area_name' => $document->area?->name,
                    'can_manage' => (int) $document->created_by === (int) $user->id,
                    'updated_at' => $document->updated_at?->toIso8601String(),
                    'download_count' => (int) $document->download_count,
                ];
            })
            ->values()
            ->all();
    }
}
