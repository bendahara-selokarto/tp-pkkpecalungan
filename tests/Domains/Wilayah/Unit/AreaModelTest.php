<?php


use PHPUnit\\Framework\\Attributes\\Test;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Domains\Wilayah\Models\Area;

class AreaModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function area_dapat_memiliki_induk_dan_anak()
    {
        $kecamatan = Area::create([
            'name'  => 'Pecalungan',
            'level' => 'kecamatan',
        ]);

        $desa = Area::create([
            'name'      => 'Bandung',
            'level'     => 'desa',
            'parent_id' => $kecamatan->id,
        ]);

        // Parent
        $this->assertEquals(
            $kecamatan->id,
            $desa->parent->id
        );

        // Child
        $this->assertCount(1, $kecamatan->children);
    }
}


