<?php

namespace Tests\Domains\Wilayah\Unit;
use PHPUnit\Framework\Attributes\Test;

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
            'code'  => '1001',
            'name'  => 'Pecalungan',
            'level' => 'kecamatan',
        ]);

        $desa = Area::create([
            'code'      => '2002',
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

    #[Test]
    public function area_code_tersedia_dan_unik()
    {
        $area = Area::create([
            'code' => '2001',
            'name' => 'Pecalungan',
            'level' => 'desa',
        ]);

        $this->assertSame('2001', $area->code);
        $this->assertDatabaseHas('areas', [
            'code' => '2001',
            'name' => 'Pecalungan',
        ]);
    }
}


