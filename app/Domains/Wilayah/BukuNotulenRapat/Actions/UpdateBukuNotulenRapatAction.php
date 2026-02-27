<?php

namespace App\Domains\Wilayah\BukuNotulenRapat\Actions;

use App\Domains\Wilayah\BukuNotulenRapat\DTOs\BukuNotulenRapatData;
use App\Domains\Wilayah\BukuNotulenRapat\Models\BukuNotulenRapat;
use App\Domains\Wilayah\BukuNotulenRapat\Repositories\BukuNotulenRapatRepositoryInterface;

class UpdateBukuNotulenRapatAction
{
    public function __construct(
        private readonly BukuNotulenRapatRepositoryInterface $bukuNotulenRapatRepository
    ) {
    }

    public function execute(BukuNotulenRapat $bukuNotulenRapat, array $payload): BukuNotulenRapat
    {
        $data = BukuNotulenRapatData::fromArray([
            'entry_date' => $payload['entry_date'],
            'title' => $payload['title'],
            'person_name' => $payload['person_name'] ?? null,
            'institution' => $payload['institution'] ?? null,
            'description' => $payload['description'] ?? null,
            'level' => $bukuNotulenRapat->level,
            'area_id' => $bukuNotulenRapat->area_id,
            'created_by' => $bukuNotulenRapat->created_by,
        ]);

        return $this->bukuNotulenRapatRepository->update($bukuNotulenRapat, $data);
    }
}
