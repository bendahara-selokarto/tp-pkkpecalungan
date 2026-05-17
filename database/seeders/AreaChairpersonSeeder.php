<?php

namespace Database\Seeders;

use App\Domains\Wilayah\Models\Area;
use Illuminate\Database\Seeder;

class AreaChairpersonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update Kecamatan Chairperson
        Area::where('level', 'kecamatan')
            ->where('name', 'Pecalungan')
            ->update([
                'chairperson_name' => 'NY. NURUL FAIZAH',
                'chairperson_role' => 'KETUA TP PKK KECAMATAN PECALUNGAN',
            ]);

        // Set generic placeholders for Desa Chairpersons if not set
        // Note: For Desa, we can use a more dynamic role if needed, 
        // but for now we set the name and let the role be set or use default logic.
        Area::where('level', 'desa')
            ->get()
            ->each(function (Area $area) {
                if (!$area->chairperson_name) {
                    $area->update([
                        'chairperson_name' => 'NY. ..........................',
                        'chairperson_role' => 'KETUA TP PKK DESA ' . strtoupper($area->name),
                    ]);
                } else {
                    $area->update([
                        'chairperson_role' => 'KETUA TP PKK DESA ' . strtoupper($area->name),
                    ]);
                }
            });
            
        // Example: Specific Desa Chairperson
        Area::where('level', 'desa')
            ->where('name', 'Selokarto')
            ->update([
                'chairperson_name' => 'NY. SITI AMINAH',
                'chairperson_role' => 'KETUA TP PKK DESA SELOKARTO',
            ]);
    }
}
