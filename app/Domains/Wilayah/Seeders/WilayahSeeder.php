<?php

namespace App\Domains\Wilayah\Seeders;

use Illuminate\Database\Seeder;
use App\Domains\Wilayah\Models\Area;

class WilayahSeeder extends Seeder
{
    public function run(): void
    {
        $kecamatan = Area::create([
            'name'  => 'Pecalungan',
            'level' => 'kecamatan'
        ]);

        $desa = [
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

        foreach ($desa as $name) {

            Area::create([
                'name'      => $name,
                'level'     => 'desa',
                'parent_id' => $kecamatan->id
            ]);
        }
    }
}
