<?php

namespace Database\Seeders;

use App\Domains\Wilayah\Models\Area;
use Illuminate\Database\Seeder;

class WilayahSeeder extends Seeder
{
    private const KECAMATAN_CODE = '1001';

    private const DESA_CODES = [
        'Pecalungan' => '2001',
        'Bandung' => '2002',
        'Gombong' => '2003',
        'Randu' => '2004',
        'Siguci' => '2005',
        'Pretek' => '2006',
        'Selokarto' => '2007',
        'Gemuh' => '2008',
        'Gumawang' => '2009',
        'Keniten' => '2010',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kecamatanName = 'Pecalungan';

        // Seed struktur wilayah baru (areas) yang dipakai fitur current domain.
        $kecamatanArea = Area::updateOrCreate(
            [
                'code' => self::KECAMATAN_CODE,
            ],
            [
                'name' => $kecamatanName,
                'level' => 'kecamatan',
                'parent_id' => null,
            ]
        );

        foreach (self::DESA_CODES as $namaDesa => $code) {
            Area::updateOrCreate(
                [
                    'code' => $code,
                ],
                [
                    'name' => $namaDesa,
                    'level' => 'desa',
                    'parent_id' => $kecamatanArea->id,
                ]
            );
        }
    }
}
