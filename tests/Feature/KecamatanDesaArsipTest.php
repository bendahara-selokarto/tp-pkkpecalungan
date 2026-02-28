<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Arsip\Models\ArsipDocument;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KecamatanDesaArsipTest extends TestCase
{
    use RefreshDatabase;

    private Area $kecamatanA;

    private Area $kecamatanB;

    private Area $desaA1;

    private Area $desaA2;

    private Area $desaB1;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['admin-kecamatan', 'kecamatan-sekretaris', 'admin-desa'] as $roleName) {
            Role::create(['name' => $roleName]);
        }

        $this->kecamatanA = Area::create([
            'name' => 'Pecalungan',
            'level' => 'kecamatan',
        ]);

        $this->kecamatanB = Area::create([
            'name' => 'Limpung',
            'level' => 'kecamatan',
        ]);

        $this->desaA1 = Area::create([
            'name' => 'Gombong',
            'level' => 'desa',
            'parent_id' => $this->kecamatanA->id,
        ]);

        $this->desaA2 = Area::create([
            'name' => 'Karanganyar',
            'level' => 'desa',
            'parent_id' => $this->kecamatanA->id,
        ]);

        $this->desaB1 = Area::create([
            'name' => 'Kalisalak',
            'level' => 'desa',
            'parent_id' => $this->kecamatanB->id,
        ]);
    }

    public function test_pengguna_kecamatan_dapat_melihat_arsip_desa_di_kecamatannya_saja(): void
    {
        $kecamatanUser = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
        ]);
        $kecamatanUser->assignRole('admin-kecamatan');

        $desaUserA = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $this->desaA1->id,
        ]);
        $desaUserA->assignRole('admin-desa');

        $desaUserB = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $this->desaB1->id,
        ]);
        $desaUserB->assignRole('admin-desa');

        ArsipDocument::factory()->create([
            'title' => 'Arsip Desa Gombong',
            'is_global' => false,
            'level' => 'desa',
            'area_id' => $this->desaA1->id,
            'created_by' => $desaUserA->id,
        ]);

        ArsipDocument::factory()->create([
            'title' => 'Arsip Desa Kalisalak',
            'is_global' => false,
            'level' => 'desa',
            'area_id' => $this->desaB1->id,
            'created_by' => $desaUserB->id,
        ]);

        ArsipDocument::factory()->create([
            'title' => 'Arsip Global',
            'is_global' => true,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $kecamatanUser->id,
        ]);

        $this->actingAs($kecamatanUser)
            ->get(route('kecamatan.desa-arsip.index'))
            ->assertOk()
            ->assertSee('Arsip Desa Gombong')
            ->assertDontSee('Arsip Desa Kalisalak')
            ->assertDontSee('Arsip Global');
    }

    public function test_filter_monitoring_arsip_desa_berjalan_berdasarkan_desa_dan_kata_kunci(): void
    {
        $kecamatanSekretaris = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
        ]);
        $kecamatanSekretaris->assignRole('kecamatan-sekretaris');

        $desaUserA = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $this->desaA1->id,
        ]);
        $desaUserA->assignRole('admin-desa');

        $desaUserA2 = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $this->desaA2->id,
        ]);
        $desaUserA2->assignRole('admin-desa');

        ArsipDocument::factory()->create([
            'title' => 'Rencana Kerja Gombong',
            'is_global' => false,
            'level' => 'desa',
            'area_id' => $this->desaA1->id,
            'created_by' => $desaUserA->id,
        ]);

        ArsipDocument::factory()->create([
            'title' => 'Rencana Kerja Karanganyar',
            'is_global' => false,
            'level' => 'desa',
            'area_id' => $this->desaA2->id,
            'created_by' => $desaUserA2->id,
        ]);

        $this->actingAs($kecamatanSekretaris)
            ->get(route('kecamatan.desa-arsip.index', [
                'desa_id' => $this->desaA1->id,
                'q' => 'Gombong',
            ]))
            ->assertOk()
            ->assertSee('Rencana Kerja Gombong')
            ->assertDontSee('Rencana Kerja Karanganyar')
            ->assertInertia(fn (AssertableInertia $page): AssertableInertia => $page
                ->component('Kecamatan/DesaArsip/Index')
                ->where('filters.desa_id', $this->desaA1->id)
                ->where('filters.q', 'Gombong'));
    }

    public function test_peran_non_kecamatan_tidak_dapat_mengakses_monitoring_arsip_desa(): void
    {
        $desaUser = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $this->desaA1->id,
        ]);
        $desaUser->assignRole('admin-desa');

        $this->actingAs($desaUser)
            ->get(route('kecamatan.desa-arsip.index'))
            ->assertStatus(403);
    }

    public function test_role_kecamatan_dengan_area_desa_ditolak_mengakses_monitoring_arsip_desa(): void
    {
        $staleKecamatanUser = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $this->desaA1->id,
        ]);
        $staleKecamatanUser->assignRole('admin-kecamatan');

        $this->actingAs($staleKecamatanUser)
            ->get(route('kecamatan.desa-arsip.index'))
            ->assertStatus(403);
    }
}
