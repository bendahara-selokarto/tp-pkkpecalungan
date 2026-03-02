<?php

namespace Tests\Feature;

use App\Domains\Wilayah\AgendaSurat\Models\AgendaSurat;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
                ->has('agendaSurats.data', 1)
                ->where('agendaSurats.data.0.nomor_surat', '001/KCA/II/2026')
                ->where('agendaSurats.total', 1)
                ->where('filters.per_page', 10);
        });
    }

    #[Test]
    public function daftar_agenda_surat_kecamatan_menggunakan_payload_pagination(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        for ($index = 1; $index <= 11; $index++) {
            AgendaSurat::create([
                'jenis_surat' => 'masuk',
                'tanggal_terima' => '2026-02-20',
                'tanggal_surat' => now()->subDays($index)->toDateString(),
                'nomor_surat' => sprintf('KCA/%03d/II/2026', $index),
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
        }

        AgendaSurat::create([
            'jenis_surat' => 'keluar',
            'tanggal_terima' => null,
            'tanggal_surat' => now()->toDateString(),
            'nomor_surat' => 'KCB/BOCOR/II/2026',
            'asal_surat' => null,
            'dari' => null,
            'kepada' => 'Kabupaten',
            'perihal' => 'Tidak Boleh Muncul',
            'lampiran' => null,
            'diteruskan_kepada' => null,
            'tembusan' => null,
            'keterangan' => null,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/agenda-surat?page=2&per_page=10');

        $response->assertOk();
        $response->assertDontSee('KCB/BOCOR/II/2026');
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Kecamatan/AgendaSurat/Index')
                ->has('agendaSurats.data', 1)
                ->where('agendaSurats.current_page', 2)
                ->where('agendaSurats.per_page', 10)
                ->where('agendaSurats.total', 11)
                ->where('filters.per_page', 10);
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
    public function admin_kecamatan_dapat_menambah_dan_memperbarui_agenda_surat(): void
    {
        Storage::fake('public');

        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $this->actingAs($adminKecamatan)->post('/kecamatan/agenda-surat', [
            'jenis_surat' => 'masuk',
            'tanggal_terima' => '2026-02-20',
            'tanggal_surat' => '2026-02-19',
            'nomor_surat' => '010/KCA/II/2026',
            'asal_surat' => 'Kabupaten',
            'dari' => 'Sekretariat Kabupaten',
            'kepada' => null,
            'perihal' => 'Arahan',
            'lampiran' => '1 berkas',
            'diteruskan_kepada' => 'Ketua',
            'tembusan' => null,
            'keterangan' => 'Catatan awal',
            'data_dukung_upload' => UploadedFile::fake()->create('instruksi-kabupaten.pdf', 120, 'application/pdf'),
        ])->assertStatus(302);

        $agenda = AgendaSurat::query()
            ->where('area_id', $this->kecamatanA->id)
            ->where('nomor_surat', '010/KCA/II/2026')
            ->firstOrFail();
        $oldAttachmentPath = $agenda->data_dukung_path;

        $this->assertNotNull($oldAttachmentPath);
        Storage::disk('public')->assertExists($oldAttachmentPath);

        $this->actingAs($adminKecamatan)->post(route('kecamatan.agenda-surat.update', $agenda->id), [
            '_method' => 'put',
            'jenis_surat' => 'keluar',
            'tanggal_terima' => null,
            'tanggal_surat' => '2026-02-21',
            'nomor_surat' => '011/KCA/II/2026',
            'asal_surat' => null,
            'dari' => null,
            'kepada' => 'Kabupaten',
            'perihal' => 'Laporan',
            'lampiran' => '2 berkas',
            'diteruskan_kepada' => null,
            'tembusan' => 'Arsip Kecamatan',
            'keterangan' => 'Sudah dikirim',
            'data_dukung_upload' => UploadedFile::fake()->create('laporan-kecamatan.pdf', 100, 'application/pdf'),
        ])->assertStatus(302);

        $agenda->refresh();
        $newAttachmentPath = $agenda->data_dukung_path;

        $this->assertNotNull($newAttachmentPath);
        $this->assertNotSame($oldAttachmentPath, $newAttachmentPath);
        Storage::disk('public')->assertMissing($oldAttachmentPath);
        Storage::disk('public')->assertExists($newAttachmentPath);

        $this->assertDatabaseHas('agenda_surats', [
            'id' => $agenda->id,
            'jenis_surat' => 'keluar',
            'tanggal_surat' => '2026-02-21',
            'nomor_surat' => '011/KCA/II/2026',
            'kepada' => 'Kabupaten',
            'tembusan' => 'Arsip Kecamatan',
            'data_dukung_path' => $newAttachmentPath,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
        ]);
    }

    #[Test]
    public function admin_kecamatan_dapat_mengakses_berkas_data_dukung_di_kecamatannya(): void
    {
        Storage::fake('public');

        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $agenda = AgendaSurat::create([
            'jenis_surat' => 'masuk',
            'tanggal_terima' => '2026-02-20',
            'tanggal_surat' => '2026-02-19',
            'nomor_surat' => '012/KCA/II/2026',
            'asal_surat' => 'Kabupaten',
            'dari' => 'Sekretariat Kabupaten',
            'kepada' => null,
            'perihal' => 'Arahan',
            'lampiran' => null,
            'diteruskan_kepada' => null,
            'tembusan' => null,
            'keterangan' => null,
            'data_dukung_path' => 'agenda-surat/kecamatan/'.$this->kecamatanA->id.'/data-dukung/arahan.pdf',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $adminKecamatan->id,
        ]);
        Storage::disk('public')->put($agenda->data_dukung_path, 'dokumen-data-dukung');

        $response = $this->actingAs($adminKecamatan)->get(route('kecamatan.agenda-surat.attachments.show', $agenda->id));

        $response->assertOk();
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
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
