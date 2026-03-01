<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Models\Area;
use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Models\PilotProjectNaskahPelaporanAttachment;
use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Models\PilotProjectNaskahPelaporanReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DesaPilotProjectNaskahPelaporanTest extends TestCase
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
    public function admin_desa_dapat_crud_naskah_dengan_lampiran_sesuai_kategori(): void
    {
        Storage::fake('public');

        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        $this->actingAs($adminDesa)->post('/desa/pilot-project-naskah-pelaporan', [
            'judul_laporan' => 'Naskah Pelaporan Desa A',
            'surat_kepada' => 'TP PKK Kecamatan Pecalungan',
            'surat_dari' => 'Tim Penggerak PKK Desa Gombong',
            'surat_tanggal' => '2026-02-22',
            'surat_nomor' => '001/PKK/II/2026',
            'dasar_pelaksanaan' => 'Dasar pelaksanaan contoh',
            'pendahuluan' => 'Pendahuluan contoh',
            'pelaksanaan_1' => 'Pelaksanaan 1',
            'pelaksanaan_2' => 'Pelaksanaan 2',
            'pelaksanaan_3' => 'Pelaksanaan 3',
            'pelaksanaan_4' => 'Pelaksanaan 4',
            'pelaksanaan_5' => 'Pelaksanaan 5',
            'penutup' => 'Penutup contoh',
            'lampiran_6a_foto' => [$this->fakeJpegUpload('6a.jpg')],
            'lampiran_6b_foto' => [$this->fakeJpegUpload('6b.jpg')],
            'lampiran_6d_dokumen' => [UploadedFile::fake()->create('6d.pdf', 10, 'application/pdf')],
            'lampiran_6e_foto' => [$this->fakeJpegUpload('6e.jpg')],
        ])->assertStatus(302);

        $report = PilotProjectNaskahPelaporanReport::query()
            ->where('area_id', $this->desaA->id)
            ->firstOrFail();

        $this->assertDatabaseHas('pilot_project_naskah_pelaporan_reports', [
            'id' => $report->id,
            'judul_laporan' => 'Naskah Pelaporan Desa A',
            'surat_kepada' => 'TP PKK Kecamatan Pecalungan',
            'surat_nomor' => '001/PKK/II/2026',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
        ]);

        $this->assertDatabaseHas('pilot_project_naskah_pelaporan_attachments', [
            'report_id' => $report->id,
            'category' => '6a_photo',
        ]);

        $this->assertDatabaseHas('pilot_project_naskah_pelaporan_attachments', [
            'report_id' => $report->id,
            'category' => '6d_document',
            'mime_type' => 'application/pdf',
        ]);

        $attachmentToRemove = PilotProjectNaskahPelaporanAttachment::query()
            ->where('report_id', $report->id)
            ->where('category', '6b_photo')
            ->firstOrFail();

        $this->actingAs($adminDesa)->put(route('desa.pilot-project-naskah-pelaporan.update', $report->id), [
            'judul_laporan' => 'Naskah Pelaporan Desa A Revisi',
            'dasar_pelaksanaan' => 'Dasar pelaksanaan revisi',
            'pendahuluan' => 'Pendahuluan revisi',
            'pelaksanaan_1' => 'Pelaksanaan 1 revisi',
            'pelaksanaan_2' => 'Pelaksanaan 2 revisi',
            'pelaksanaan_3' => 'Pelaksanaan 3 revisi',
            'pelaksanaan_4' => 'Pelaksanaan 4 revisi',
            'pelaksanaan_5' => 'Pelaksanaan 5 revisi',
            'penutup' => 'Penutup revisi',
            'remove_attachment_ids' => [$attachmentToRemove->id],
            'lampiran_6e_foto' => [$this->fakeJpegUpload('6e-baru.jpg')],
        ])->assertStatus(302);

        $this->assertDatabaseMissing('pilot_project_naskah_pelaporan_attachments', [
            'id' => $attachmentToRemove->id,
        ]);

        $this->assertDatabaseHas('pilot_project_naskah_pelaporan_attachments', [
            'report_id' => $report->id,
            'category' => '6e_photo',
            'original_name' => '6e-baru.jpg',
        ]);

        $this->actingAs($adminDesa)
            ->delete(route('desa.pilot-project-naskah-pelaporan.destroy', $report->id))
            ->assertStatus(302);

        $this->assertDatabaseMissing('pilot_project_naskah_pelaporan_reports', [
            'id' => $report->id,
        ]);
    }

    #[Test]
    public function pengguna_non_admin_desa_tidak_bisa_mengakses_modul_naskah_desa(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $response = $this->actingAs($adminKecamatan)->get('/desa/pilot-project-naskah-pelaporan');

        $response->assertStatus(403);
    }

    #[Test]
    public function admin_desa_dengan_level_area_tidak_sinkron_ditolak(): void
    {
        $invalidUser = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'desa',
        ]);
        $invalidUser->assignRole('admin-desa');

        $response = $this->actingAs($invalidUser)->get('/desa/pilot-project-naskah-pelaporan');

        $response->assertStatus(403);
    }

    #[Test]
    public function surat_tanggal_harus_format_yyyy_mm_dd(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        $this->actingAs($adminDesa)->post('/desa/pilot-project-naskah-pelaporan', [
            'judul_laporan' => 'Naskah Tanggal Invalid',
            'surat_kepada' => 'TP PKK Kecamatan Pecalungan',
            'surat_dari' => 'Tim Penggerak PKK Desa Gombong',
            'surat_tanggal' => '22/02/2026',
            'dasar_pelaksanaan' => 'Dasar pelaksanaan contoh',
            'pendahuluan' => 'Pendahuluan contoh',
            'pelaksanaan_1' => 'Pelaksanaan 1',
            'pelaksanaan_2' => 'Pelaksanaan 2',
            'pelaksanaan_3' => 'Pelaksanaan 3',
            'pelaksanaan_4' => 'Pelaksanaan 4',
            'pelaksanaan_5' => 'Pelaksanaan 5',
            'penutup' => 'Penutup contoh',
        ])->assertSessionHasErrors(['surat_tanggal']);
    }

    private function fakeJpegUpload(string $fileName): UploadedFile
    {
        $jpegBinary = base64_decode('/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////2wBDAf//////////////////////////////////////////////////////////////////////////////////////wAARCAABAAEDAREAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAb/xAAVEQEBAAAAAAAAAAAAAAAAAAABAP/aAAwDAQACEAMQAAAAqgD/xAAUEAEAAAAAAAAAAAAAAAAAAAAQ/9oACAEBAAEFAm//xAAUEQEAAAAAAAAAAAAAAAAAAAAQ/9oACAEDAQE/AT//xAAUEQEAAAAAAAAAAAAAAAAAAAAQ/9oACAECAQE/AT//2Q==', true);

        return UploadedFile::fake()->createWithContent($fileName, $jpegBinary === false ? '' : $jpegBinary);
    }
}
