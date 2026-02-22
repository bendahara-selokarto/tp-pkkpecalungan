<?php

namespace App\Domains\Wilayah\CatatanKeluarga\Repositories;

use Illuminate\Support\Collection;

interface CatatanKeluargaRepositoryInterface
{
    public function getByLevelAndArea(string $level, int $areaId): Collection;

    public function getRekapDasaWismaByLevelAndArea(string $level, int $areaId): Collection;

    public function getRekapPkkRtByLevelAndArea(string $level, int $areaId): Collection;

    public function getCatatanPkkRwByLevelAndArea(string $level, int $areaId): Collection;

    public function getRekapRwByLevelAndArea(string $level, int $areaId): Collection;

    public function getCatatanTpPkkDesaKelurahanByLevelAndArea(string $level, int $areaId): Collection;

    public function getCatatanTpPkkKecamatanByLevelAndArea(string $level, int $areaId): Collection;

    public function getCatatanTpPkkKabupatenKotaByLevelAndArea(string $level, int $areaId): Collection;

    public function getCatatanTpPkkProvinsiByLevelAndArea(string $level, int $areaId): Collection;

    public function getRekapIbuHamilDasaWismaByLevelAndArea(string $level, int $areaId): Collection;
}
