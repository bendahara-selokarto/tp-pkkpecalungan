<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Kecamatan;
use App\Models\Desa;

class WilayahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
                // 1. Kecamatan
        $kecamatan = Kecamatan::firstOrCreate(
            ['nama' => 'Pecalungan']
        );

        // 2. Daftar Desa
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

        foreach ($desaList as $namaDesa) {
            Desa::firstOrCreate([
                'kecamatan_id' => $kecamatan->id,
                'nama' => $namaDesa,
            ]);
        }
        
    }
}
