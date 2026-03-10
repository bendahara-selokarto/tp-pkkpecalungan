<?php

namespace App\Domains\Wilayah\AgendaSurat\Repositories;

use App\Domains\Wilayah\AgendaSurat\DTOs\AgendaSuratData;
use App\Domains\Wilayah\AgendaSurat\Models\AgendaSurat;
use App\Domains\Wilayah\AgendaSurat\Models\AgendaSuratLampiranItem;
use App\Domains\Wilayah\AgendaSurat\Models\AgendaSuratTembusanItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AgendaSuratRepository implements AgendaSuratRepositoryInterface
{
    public function store(AgendaSuratData $data): AgendaSurat
    {
        return DB::transaction(function () use ($data): AgendaSurat {
            $agendaSurat = AgendaSurat::create([
                'jenis_surat' => $data->jenis_surat,
                'tanggal_terima' => $data->tanggal_terima,
                'tanggal_surat' => $data->tanggal_surat,
                'nomor_surat' => $data->nomor_surat,
                'asal_surat' => $data->asal_surat,
                'dari' => $data->dari,
                'kepada' => $data->kepada,
                'perihal' => $data->perihal,
                'lampiran' => $data->lampiran,
                'diteruskan_kepada' => $data->diteruskan_kepada,
                'tembusan' => $data->tembusan,
                'keterangan' => $data->keterangan,
                'data_dukung_path' => $data->data_dukung_path,
                'level' => $data->level,
                'area_id' => $data->area_id,
                'created_by' => $data->created_by,
                'tahun_anggaran' => $data->tahun_anggaran,
            ]);

            $this->syncLampiranItems($agendaSurat, $data->lampiran);
            $this->syncTembusanItems($agendaSurat, $data->tembusan);

            return $agendaSurat;
        });
    }

    public function paginateByLevelAndArea(string $level, int $areaId, int $tahunAnggaran, int $perPage, ?int $creatorIdFilter = null): LengthAwarePaginator
    {
        return AgendaSurat::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->where('tahun_anggaran', $tahunAnggaran)
            ->when(is_int($creatorIdFilter), static fn ($query) => $query->where('created_by', $creatorIdFilter))
            ->with(['lampiranItems', 'tembusanItems'])
            ->latest('tanggal_surat')
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getByLevelAndArea(string $level, int $areaId, int $tahunAnggaran, ?int $creatorIdFilter = null): Collection
    {
        return AgendaSurat::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->where('tahun_anggaran', $tahunAnggaran)
            ->when(is_int($creatorIdFilter), static fn ($query) => $query->where('created_by', $creatorIdFilter))
            ->with(['lampiranItems', 'tembusanItems'])
            ->latest('tanggal_surat')
            ->latest('id')
            ->get();
    }

    public function find(int $id): AgendaSurat
    {
        return AgendaSurat::query()
            ->with(['lampiranItems', 'tembusanItems'])
            ->findOrFail($id);
    }

    public function update(AgendaSurat $agendaSurat, AgendaSuratData $data): AgendaSurat
    {
        return DB::transaction(function () use ($agendaSurat, $data): AgendaSurat {
            $agendaSurat->update([
                'jenis_surat' => $data->jenis_surat,
                'tanggal_terima' => $data->tanggal_terima,
                'tanggal_surat' => $data->tanggal_surat,
                'nomor_surat' => $data->nomor_surat,
                'asal_surat' => $data->asal_surat,
                'dari' => $data->dari,
                'kepada' => $data->kepada,
                'perihal' => $data->perihal,
                'lampiran' => $data->lampiran,
                'diteruskan_kepada' => $data->diteruskan_kepada,
                'tembusan' => $data->tembusan,
                'keterangan' => $data->keterangan,
                'data_dukung_path' => $data->data_dukung_path,
                'tahun_anggaran' => $data->tahun_anggaran,
            ]);

            $this->syncLampiranItems($agendaSurat, $data->lampiran);
            $this->syncTembusanItems($agendaSurat, $data->tembusan);

            return $agendaSurat;
        });
    }

    public function delete(AgendaSurat $agendaSurat): void
    {
        $agendaSurat->delete();
    }

    private function syncLampiranItems(AgendaSurat $agendaSurat, ?string $value): void
    {
        AgendaSuratLampiranItem::query()
            ->where('agenda_surat_id', $agendaSurat->id)
            ->delete();

        $values = $this->splitLines($value);
        if ($values === []) {
            return;
        }

        $now = now();
        $rows = [];
        $sequence = 1;

        foreach ($values as $item) {
            $rows[] = [
                'agenda_surat_id' => $agendaSurat->id,
                'sequence' => $sequence,
                'value' => $item,
                'level' => $agendaSurat->level,
                'area_id' => $agendaSurat->area_id,
                'created_by' => $agendaSurat->created_by,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $sequence++;
        }

        AgendaSuratLampiranItem::query()->insert($rows);
    }

    private function syncTembusanItems(AgendaSurat $agendaSurat, ?string $value): void
    {
        AgendaSuratTembusanItem::query()
            ->where('agenda_surat_id', $agendaSurat->id)
            ->delete();

        $values = $this->splitLines($value);
        if ($values === []) {
            return;
        }

        $now = now();
        $rows = [];
        $sequence = 1;

        foreach ($values as $item) {
            $rows[] = [
                'agenda_surat_id' => $agendaSurat->id,
                'sequence' => $sequence,
                'value' => $item,
                'level' => $agendaSurat->level,
                'area_id' => $agendaSurat->area_id,
                'created_by' => $agendaSurat->created_by,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $sequence++;
        }

        AgendaSuratTembusanItem::query()->insert($rows);
    }

    /**
     * @return array<int, string>
     */
    private function splitLines(?string $value): array
    {
        $text = trim((string) ($value ?? ''));
        if ($text === '') {
            return [];
        }

        $parts = preg_split('/\\r\\n|\\r|\\n/', $text) ?: [];

        return array_values(array_filter(array_map('trim', $parts), static fn (string $part): bool => $part !== ''));
    }
}


