<?php

namespace Tests\Domains\Wilayah\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Domains\Wilayah\Models\Area;

class AreaModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function area_can_have_parent_and_children()
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
