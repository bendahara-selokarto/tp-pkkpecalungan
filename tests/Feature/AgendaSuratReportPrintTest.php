<?php

namespace Tests\Feature;

use App\Domains\Wilayah\AgendaSurat\Models\AgendaSurat;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AgendaSuratReportPrintTest extends TestCase
{
    use RefreshDatabase;

    protected Area $kecamatanA;
    protected Area $kecamatanB;
    protected Area $desaA;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin-desa']);
        Role::create(['name' => 'admin-kecamatan']);

        $this->kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $this->kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);
        $this->desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $this->kecamatanA->id]);
    }

    public function test_admin_desa_dapat_mencetak_laporan_pdf_agenda_surat_desanya_sendiri(): void
    {
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $this->desaA->id]);
        $user->assignRole('admin-desa');

        AgendaSurat::create([
            'jenis_surat' => 'masuk',
            'tanggal_terima' => '2026-02-20',
            'tanggal_surat' => '2026-02-19',
            'nomor_surat' => '001/DSA/II/2026',
            'asal_surat' => 'Kecamatan',
            'dari' => 'Sekretariat Kecamatan',
            'kepada' => null,
            'perihal' => 'Undangan Rapat',
            'lampiran' => null,
            'diteruskan_kepada' => 'Ketua',
            'tembusan' => null,
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('desa.agenda-surat.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_admin_kecamatan_dapat_mencetak_laporan_pdf_agenda_surat_kecamatannya_sendiri(): void
    {
        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $this->kecamatanA->id]);
        $user->assignRole('admin-kecamatan');

        AgendaSurat::create([
            'jenis_surat' => 'keluar',
            'tanggal_terima' => null,
            'tanggal_surat' => '2026-02-21',
            'nomor_surat' => '002/KCA/II/2026',
            'asal_surat' => null,
            'dari' => null,
            'kepada' => 'Kabupaten',
            'perihal' => 'Laporan Rutin',
            'lampiran' => '1 berkas',
            'diteruskan_kepada' => null,
            'tembusan' => 'Arsip',
            'keterangan' => null,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('kecamatan.agenda-surat.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_admin_desa_dapat_mencetak_laporan_pdf_ekspedisi_dari_surat_keluar_desanya(): void
    {
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $this->desaA->id]);
        $user->assignRole('admin-desa');

        AgendaSurat::create([
            'jenis_surat' => 'keluar',
            'tanggal_terima' => null,
            'tanggal_surat' => '2026-02-22',
            'nomor_surat' => '003/DSA/II/2026',
            'asal_surat' => null,
            'dari' => null,
            'kepada' => 'Kecamatan Pecalungan',
            'perihal' => 'Pengiriman Rekap Kegiatan',
            'lampiran' => '1 berkas',
            'diteruskan_kepada' => null,
            'tembusan' => null,
            'keterangan' => 'Dikirim via kurir',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $user->id,
        ]);

        AgendaSurat::create([
            'jenis_surat' => 'masuk',
            'tanggal_terima' => '2026-02-22',
            'tanggal_surat' => '2026-02-21',
            'nomor_surat' => '004/DSA/II/2026',
            'asal_surat' => 'Kecamatan Pecalungan',
            'dari' => 'Sekretariat',
            'kepada' => null,
            'perihal' => 'Undangan Koordinasi',
            'lampiran' => null,
            'diteruskan_kepada' => 'Ketua',
            'tembusan' => null,
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('desa.agenda-surat.ekspedisi.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_admin_kecamatan_dapat_mencetak_laporan_pdf_ekspedisi_dari_surat_keluar_kecamatannya(): void
    {
        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $this->kecamatanA->id]);
        $user->assignRole('admin-kecamatan');

        AgendaSurat::create([
            'jenis_surat' => 'keluar',
            'tanggal_terima' => null,
            'tanggal_surat' => '2026-02-23',
            'nomor_surat' => '005/KCA/II/2026',
            'asal_surat' => null,
            'dari' => null,
            'kepada' => 'Kabupaten Batang',
            'perihal' => 'Laporan Bulanan',
            'lampiran' => '2 lembar',
            'diteruskan_kepada' => null,
            'tembusan' => 'Arsip',
            'keterangan' => null,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('kecamatan.agenda-surat.ekspedisi.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_laporan_pdf_agenda_surat_tetap_aman_saat_scope_metadata_tidak_sinkron(): void
    {
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $this->kecamatanB->id]);
        $user->assignRole('admin-desa');

        $response = $this->actingAs($user)->get(route('desa.agenda-surat.report'));

        $response->assertStatus(403);
    }

    public function test_laporan_pdf_ekspedisi_tetap_aman_saat_scope_metadata_tidak_sinkron(): void
    {
        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $this->desaA->id]);
        $user->assignRole('admin-kecamatan');

        $response = $this->actingAs($user)->get(route('kecamatan.agenda-surat.ekspedisi.report'));

        $response->assertStatus(403);
    }
}
