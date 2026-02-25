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

class DesaAgendaSuratTest extends TestCase
{
    use RefreshDatabase;

    protected Area $kecamatan;
    protected Area $desaA;
    protected Area $desaB;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin-desa']);
        Role::create(['name' => 'admin-kecamatan']);

        $this->kecamatan = Area::create([
            'name' => 'Pecalungan',
            'level' => 'kecamatan',
        ]);

        $this->desaA = Area::create([
            'name' => 'Gombong',
            'level' => 'desa',
            'parent_id' => $this->kecamatan->id,
        ]);

        $this->desaB = Area::create([
            'name' => 'Bandung',
            'level' => 'desa',
            'parent_id' => $this->kecamatan->id,
        ]);
    }

    #[Test]
    public function admin_desa_dapat_melihat_daftar_agenda_surat_desanya_sendiri()
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        AgendaSurat::create([
            'jenis_surat' => 'masuk',
            'tanggal_terima' => '2026-02-20',
            'tanggal_surat' => '2026-02-19',
            'nomor_surat' => '001/DSA/II/2026',
            'asal_surat' => 'Kecamatan',
            'dari' => 'Sekretariat Kecamatan',
            'kepada' => null,
            'perihal' => 'Undangan Rapat',
            'lampiran' => '1 berkas',
            'diteruskan_kepada' => 'Ketua',
            'tembusan' => null,
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
        ]);

        AgendaSurat::create([
            'jenis_surat' => 'keluar',
            'tanggal_terima' => null,
            'tanggal_surat' => '2026-02-21',
            'nomor_surat' => '009/DSB/II/2026',
            'asal_surat' => null,
            'dari' => null,
            'kepada' => 'Kecamatan',
            'perihal' => 'Laporan Kegiatan',
            'lampiran' => null,
            'diteruskan_kepada' => null,
            'tembusan' => 'Arsip',
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $this->desaB->id,
            'created_by' => $adminDesa->id,
        ]);

        $response = $this->actingAs($adminDesa)->get('/desa/agenda-surat');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page) {
            $page
                ->component('Desa/AgendaSurat/Index')
                ->has('agendaSurats.data', 1)
                ->where('agendaSurats.data.0.nomor_surat', '001/DSA/II/2026')
                ->where('agendaSurats.total', 1)
                ->where('filters.per_page', 10);
        });
    }

    #[Test]
    public function daftar_agenda_surat_desa_mendukung_pagination_dan_tetap_scoped(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        for ($index = 1; $index <= 12; $index++) {
            AgendaSurat::create([
                'jenis_surat' => 'masuk',
                'tanggal_terima' => '2026-02-20',
                'tanggal_surat' => now()->subDays($index)->toDateString(),
                'nomor_surat' => sprintf('DSA/%03d/II/2026', $index),
                'asal_surat' => 'Kecamatan',
                'dari' => 'Sekretariat Kecamatan',
                'kepada' => null,
                'perihal' => 'Undangan Rapat',
                'lampiran' => '1 berkas',
                'diteruskan_kepada' => 'Ketua',
                'tembusan' => null,
                'keterangan' => null,
                'level' => 'desa',
                'area_id' => $this->desaA->id,
                'created_by' => $adminDesa->id,
            ]);
        }

        AgendaSurat::create([
            'jenis_surat' => 'masuk',
            'tanggal_terima' => '2026-02-20',
            'tanggal_surat' => now()->toDateString(),
            'nomor_surat' => 'DSB/BOCOR/II/2026',
            'asal_surat' => 'Kecamatan',
            'dari' => 'Sekretariat Kecamatan',
            'kepada' => null,
            'perihal' => 'Data Bocor',
            'lampiran' => null,
            'diteruskan_kepada' => null,
            'tembusan' => null,
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $this->desaB->id,
            'created_by' => $adminDesa->id,
        ]);

        $response = $this->actingAs($adminDesa)->get('/desa/agenda-surat?page=2&per_page=10');

        $response->assertOk();
        $response->assertDontSee('DSB/BOCOR/II/2026');
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Desa/AgendaSurat/Index')
                ->has('agendaSurats.data', 2)
                ->where('agendaSurats.current_page', 2)
                ->where('agendaSurats.per_page', 10)
                ->where('agendaSurats.total', 12)
                ->where('filters.per_page', 10);
        });
    }

    #[Test]
    public function per_page_tidak_valid_di_agenda_surat_desa_kembali_ke_default(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        AgendaSurat::create([
            'jenis_surat' => 'masuk',
            'tanggal_terima' => '2026-02-20',
            'tanggal_surat' => '2026-02-19',
            'nomor_surat' => 'DSA/DEFAULT/II/2026',
            'asal_surat' => 'Kecamatan',
            'dari' => 'Sekretariat Kecamatan',
            'kepada' => null,
            'perihal' => 'Undangan',
            'lampiran' => null,
            'diteruskan_kepada' => null,
            'tembusan' => null,
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
        ]);

        $response = $this->actingAs($adminDesa)->get('/desa/agenda-surat?per_page=999');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Desa/AgendaSurat/Index')
                ->where('filters.per_page', 10)
                ->where('agendaSurats.per_page', 10);
        });
    }

    #[Test]
    public function admin_desa_dapat_menambah_memperbarui_dan_menghapus_agenda_surat()
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        $this->actingAs($adminDesa)->post('/desa/agenda-surat', [
            'jenis_surat' => 'masuk',
            'tanggal_terima' => '2026-02-20',
            'tanggal_surat' => '2026-02-19',
            'nomor_surat' => '010/DSA/II/2026',
            'asal_surat' => 'Kecamatan',
            'dari' => 'Sekretariat Kecamatan',
            'perihal' => 'Undangan',
            'lampiran' => '1 lembar',
            'diteruskan_kepada' => 'Ketua',
            'keterangan' => 'Segera ditindaklanjuti',
        ])->assertStatus(302);

        $agenda = AgendaSurat::where('nomor_surat', '010/DSA/II/2026')->firstOrFail();

        $this->actingAs($adminDesa)->put(route('desa.agenda-surat.update', $agenda->id), [
            'jenis_surat' => 'keluar',
            'tanggal_surat' => '2026-02-21',
            'nomor_surat' => '011/DSA/II/2026',
            'kepada' => 'Kecamatan',
            'perihal' => 'Laporan',
            'tembusan' => 'Arsip Desa',
            'keterangan' => 'Sudah dikirim',
        ])->assertStatus(302);

        $this->assertDatabaseHas('agenda_surats', [
            'id' => $agenda->id,
            'jenis_surat' => 'keluar',
            'tanggal_surat' => '2026-02-21',
            'nomor_surat' => '011/DSA/II/2026',
            'kepada' => 'Kecamatan',
            'tembusan' => 'Arsip Desa',
        ]);

        $this->actingAs($adminDesa)->delete(route('desa.agenda-surat.destroy', $agenda->id))
            ->assertStatus(302);

        $this->assertDatabaseMissing('agenda_surats', ['id' => $agenda->id]);
    }

    #[Test]
    public function pengguna_non_admin_desa_tidak_bisa_mengakses_modul_agenda_surat_desa()
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $response = $this->actingAs($adminKecamatan)->get('/desa/agenda-surat');

        $response->assertStatus(403);
    }

    #[Test]
    public function pengguna_role_desa_tetapi_area_bukan_desa_tidak_dapat_mengakses_modul_desa()
    {
        $user = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'desa',
        ]);
        $user->assignRole('admin-desa');

        $response = $this->actingAs($user)->get('/desa/agenda-surat');

        $response->assertStatus(403);
    }
}
