<?php

namespace App\Domains\Wilayah\Arsip\Repositories;

use App\Domains\Wilayah\Arsip\Models\ArsipDocument;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ArsipDocumentRepository implements ArsipDocumentRepositoryInterface
{
    public function listVisibleForUser(User $user): Collection
    {
        return ArsipDocument::query()
            ->with([
                'area:id,name,parent_id,level',
                'creator:id,name,scope,area_id',
            ])
            ->where(static function ($builder) use ($user): void {
                $builder
                    ->where('is_global', true)
                    ->orWhere('created_by', $user->id);
            })
            ->orderByDesc('is_global')
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->get();
    }

    public function paginateGlobalForManagement(int $perPage = 10): LengthAwarePaginator
    {
        return ArsipDocument::query()
            ->with([
                'area:id,name,parent_id,level',
                'creator:id,name,scope,area_id',
            ])
            ->where('is_global', true)
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function paginateDesaByKecamatan(
        int $kecamatanAreaId,
        int $perPage = 10,
        ?int $desaId = null,
        ?string $keyword = null
    ): LengthAwarePaginator {
        return ArsipDocument::query()
            ->with([
                'area:id,name,parent_id,level',
                'creator:id,name,scope,area_id',
            ])
            ->where('is_global', false)
            ->where('level', 'desa')
            ->whereHas('area', static function ($query) use ($kecamatanAreaId): void {
                $query
                    ->where('level', 'desa')
                    ->where('parent_id', $kecamatanAreaId);
            })
            ->when(is_numeric($desaId), static function ($query) use ($desaId): void {
                $query->where('area_id', (int) $desaId);
            })
            ->when(is_string($keyword) && $keyword !== '', static function ($query) use ($keyword): void {
                $query->where(function ($builder) use ($keyword): void {
                    $builder
                        ->where('title', 'like', '%'.$keyword.'%')
                        ->orWhere('description', 'like', '%'.$keyword.'%')
                        ->orWhere('original_name', 'like', '%'.$keyword.'%')
                        ->orWhereHas('creator', static function ($creatorQuery) use ($keyword): void {
                            $creatorQuery->where('name', 'like', '%'.$keyword.'%');
                        });
                });
            })
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
