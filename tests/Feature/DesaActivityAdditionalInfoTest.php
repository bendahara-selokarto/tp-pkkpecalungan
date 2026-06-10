<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DesaActivityAdditionalInfoTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function pokja_desa_dapat_menyimpan_metadata_kegiatan_terstruktur(): void
    {
        Role::firstOrCreate(['name' => 'desa-pokja-i']);

        $kecamatan = Area::query()->create([
            'name' => 'Pecalungan',
            'level' => 'kecamatan',
        ]);

        $desa = Area::query()->create([
            'name' => 'Gombong',
            'level' => 'desa',
            'parent_id' => $kecamatan->id,
        ]);

        $user = User::factory()->create([
            'area_id' => $desa->id,
            'scope' => 'desa',
            'active_budget_year' => 2026,
        ]);
        $user->assignRole('desa-pokja-i');

        $this->actingAs($user)->post('/desa/activities', [
            'title' => 'Penyuluhan PAAR',
            'nama_petugas' => 'Petugas Pokja I',
            'jabatan_petugas' => 'Pokja I',
            'tempat_kegiatan' => 'Balai Desa',
            'uraian' => 'Penyuluhan pola asuh anak dan remaja',
            'activity_date' => '2026-03-10',
            'additional_info' => [
                'program_category' => 'PAAR',
                'volume' => 2,
                'sasaran' => '30 orang tua',
                'metode' => 'Penyuluhan',
            ],
        ])->assertRedirect(route('desa.activities.index'));

        $activity = Activity::query()
            ->where('title', 'Penyuluhan PAAR')
            ->firstOrFail();

        $this->assertSame('pokja-i', $activity->group);
        $this->assertSame([
            'program_category' => 'PAAR',
            'volume' => 2,
            'sasaran' => '30 orang tua',
            'metode' => 'Penyuluhan',
        ], $activity->additional_info);
    }
}
