<?php

namespace App\Domains\Wilayah\CatatanKeluarga\Repositories;

use Illuminate\Support\Collection;

interface CatatanKeluargaRepositoryInterface
{
    // 4.15 catatan keluarga dasar
    public function getByLevelAndArea(string $level, int $areaId): Collection;

    // 4.16 rekap/catatan data kegiatan warga
    public function getRekapDasaWismaByLevelAndArea(string $level, int $areaId): Collection;

    public function getRekapPkkRtByLevelAndArea(string $level, int $areaId): Collection;

    public function getCatatanPkkRwByLevelAndArea(string $level, int $areaId): Collection;

    public function getRekapRwByLevelAndArea(string $level, int $areaId): Collection;

    // 4.17 catatan data kegiatan warga tp pkk bertingkat
    public function getCatatanTpPkkDesaKelurahanByLevelAndArea(string $level, int $areaId): Collection;

    public function getCatatanTpPkkKecamatanByLevelAndArea(string $level, int $areaId): Collection;

    public function getCatatanTpPkkKabupatenKotaByLevelAndArea(string $level, int $areaId): Collection;

    public function getCatatanTpPkkProvinsiByLevelAndArea(string $level, int $areaId): Collection;

    // 4.18 rekap ibu hamil/melahirkan bertingkat
    public function getRekapIbuHamilDasaWismaByLevelAndArea(string $level, int $areaId): Collection;

    public function getRekapIbuHamilPkkRtByLevelAndArea(string $level, int $areaId): Collection;

    public function getRekapIbuHamilPkkRwByLevelAndArea(string $level, int $areaId): Collection;

    public function getRekapIbuHamilPkkDusunLingkunganByLevelAndArea(string $level, int $areaId): Collection;

    // Agregasi tingkat desa/kelurahan dibangun dari rekap 4.18d (dusun/lingkungan).
    public function getRekapIbuHamilTpPkkDesaKelurahanByLevelAndArea(string $level, int $areaId): Collection;

    // Agregasi tingkat kecamatan dibangun dari rekap tingkat desa/kelurahan.
    public function getRekapIbuHamilTpPkkKecamatanByLevelAndArea(string $level, int $areaId): Collection;
}
