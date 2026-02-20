<?php

namespace Tests\Feature;

use App\Domains\Wilayah\AgendaSurat\Models\AgendaSurat;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KecamatanAgendaSuratTest extends TestCase
{
    use RefreshDatabase;

    protected Area $kecamatanA;
    protected Area $kecamatanB;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin-kecamatan']);
        Role::create(['name' => 'admin-desa']);

        $this->kecamatanA = Area::create([
            'name' => 'Pecalungan',
            'level' => 'kecamatan',
        ]);

        $this->kecamatanB = Area::create([
            'name' => 'Limpung',
            'level' => 'kecamatan',
        ]);
    }

    #[Test]
    public function admin_kecamatan_dapat_melihat_daftar_agenda_surat_di_kecamatannya_sendiri()
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        AgendaSurat::create([
            'jenis_surat' => 'masuk',
            'tanggal_terima' => '2026-02-20',
            'tanggal_surat' => '2026-02-19',
            'nomor_surat' => '001/KCA/II/2026',
            'asal_surat' => 'Kabupaten',
            'dari' => 'Sekretariat Kabupaten',
            'kepada' => null,
            'perihal' => 'Instruksi',
            'lampiran' => null,
            'diteruskan_kepada' => 'Ketua',
            'tembusan' => null,
            'keterangan' => null,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $adminKecamatan->id,
        ]);

        AgendaSurat::create([
            'jenis_surat' => 'keluar',
            'tanggal_terima' => null,
            'tanggal_surat' => '2026-02-21',
            'nomor_surat' => '002/KCB/II/2026',
            'asal_surat' => null,
            'dari' => null,
            'kepada' => 'Kabupaten',
            'perihal' => 'Laporan',
            'lampiran' => '1 berkas',
            'diteruskan_kepada' => null,
            'tembusan' => 'Arsip',
            'keterangan' => null,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/agenda-surat');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page) {
            $page
                ->component('Kecamatan/AgendaSurat/Index')
                ->has('agendaSurats', 1)
                ->where('agendaSurats.0.nomor_surat', '001/KCA/II/2026');
        });
    }

    #[Test]
    public function admin_kecamatan_tidak_bisa_melihat_detail_agenda_surat_kecamatan_lain()
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $agendaLuar = AgendaSurat::create([
            'jenis_surat' => 'masuk',
            'tanggal_terima' => '2026-02-20',
            'tanggal_surat' => '2026-02-19',
            'nomor_surat' => '005/KCB/II/2026',
            'asal_surat' => 'Kabupaten',
            'dari' => 'Sekretariat Kabupaten',
            'kepada' => null,
            'perihal' => 'Instruksi',
            'lampiran' => null,
            'diteruskan_kepada' => null,
            'tembusan' => null,
            'keterangan' => null,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get(route('kecamatan.agenda-surat.show', $agendaLuar->id));

        $response->assertStatus(403);
    }

    #[Test]
    public function pengguna_non_admin_kecamatan_tidak_bisa_mengakses_modul_agenda_surat_kecamatan()
    {
        $desa = Area::create([
            'name' => 'Gombong',
            'level' => 'desa',
            'parent_id' => $this->kecamatanA->id,
        ]);

        $adminDesa = User::factory()->create([
            'area_id' => $desa->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        $response = $this->actingAs($adminDesa)->get('/kecamatan/agenda-surat');

        $response->assertStatus(403);
    }
}
