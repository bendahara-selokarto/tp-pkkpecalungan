<?php

namespace App\Domains\Wilayah\AgendaSuratTugas\Repositories;

use App\Domains\Wilayah\AgendaSuratTugas\DTOs\AgendaSuratTugasData;
use App\Domains\Wilayah\AgendaSuratTugas\Models\AgendaSuratTugas;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class AgendaSuratTugasRepository implements AgendaSuratTugasRepositoryInterface
{
    public function listScoped(User $user, string $level, int $perPage): LengthAwarePaginator
    {
        return AgendaSuratTugas::query()
            ->where('level', $level)
            ->where('area_id', $user->area_id)
            ->where('tahun_anggaran', $user->active_budget_year)
            ->orderByDesc('tanggal_surat')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findScoped(int $id, string $level, int $areaId): ?AgendaSuratTugas
    {
        return AgendaSuratTugas::query()
            ->where('id', $id)
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->first();
    }

    public function create(AgendaSuratTugasData $data): AgendaSuratTugas
    {
        return AgendaSuratTugas::create([
            'nomor_surat' => $data->nomor_surat,
            'tanggal_surat' => $data->tanggal_surat,
            'kepada' => $data->kepada,
            'perihal' => $data->perihal,
            'lampiran' => $data->lampiran,
            'tembusan' => $data->tembusan,
            'file_path' => $data->file_path,
            'original_name' => $data->original_name,
            'mime_type' => $data->mime_type,
            'extension' => $data->extension,
            'size_bytes' => $data->size_bytes,
            'level' => $data->level,
            'area_id' => $data->area_id,
            'created_by' => $data->created_by,
            'tahun_anggaran' => $data->tahun_anggaran,
        ]);
    }

    public function update(AgendaSuratTugas $model, array $data): bool
    {
        return $model->update($data);
    }

    public function delete(AgendaSuratTugas $model): bool
    {
        return $model->delete();
    }
}
