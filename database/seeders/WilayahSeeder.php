<?php

namespace Database\Seeders;

use App\Models\Desa;
use App\Models\Kecamatan;
use App\Domains\Wilayah\Models\Area;
use Illuminate\Database\Seeder;

class WilayahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kecamatanName = 'Pecalungan';
        $desaList = [
            'Pecalungan',
            'Bandung',
            'Gombong',
            'Randu',
            'Siguci',
            'Pretek',
            'Selokarto',
            'Gemuh',
            'Gumawang',
            'Keniten',
        ];

        // Seed struktur wilayah baru (areas) yang dipakai fitur current domain.
        $kecamatanArea = Area::firstOrCreate([
            'name' => $kecamatanName,
            'level' => 'kecamatan',
            'parent_id' => null,
        ]);

        foreach ($desaList as $namaDesa) {
            Area::firstOrCreate([
                'name' => $namaDesa,
                'level' => 'desa',
                'parent_id' => $kecamatanArea->id,
            ]);
        }

        // Tetap seed tabel legacy agar modul lama tetap konsisten.
        $legacyKecamatan = Kecamatan::firstOrCreate(['nama' => $kecamatanName]);

        foreach ($desaList as $namaDesa) {
            Desa::firstOrCreate([
                'kecamatan_id' => $legacyKecamatan->id,
                'nama' => $namaDesa,
            ]);
        }
    }
}
