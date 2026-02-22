<?php

namespace Tests\Feature;

use App\Domains\Wilayah\DataKegiatanWarga\Models\DataKegiatanWarga;
use App\Domains\Wilayah\DataWarga\Models\DataWarga;
use App\Domains\Wilayah\DataWarga\Models\DataWargaAnggota;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\Feature\Concerns\AssertsPdfReportHeaders;
use Tests\TestCase;

class RekapCatatanDataKegiatanWargaReportPrintTest extends TestCase
{
    use RefreshDatabase;
    use AssertsPdfReportHeaders;

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

    public function test_header_kolom_pdf_rekap_dasa_wisma_tetap_sesuai_mapping_autentik(): void
    {
        $this->assertPdfReportHeadersInOrder('pdf.rekap_catatan_data_kegiatan_warga_dasa_wisma_report', [
            'NO',
            'NAMA KEPALA RUMAH TANGGA',
            'JML KK',
            'JUMLAH ANGGOTA KELUARGA',
            'KRITERIA RUMAH',
            'SUMBER AIR KELUARGA',
            'MAKANAN',
            'WARGA MENGIKUTI KEGIATAN',
            'KET',
            'TOTAL',
            'BALITA',
            'PUS',
            'WUS',
            'IBU HAMIL',
            'IBU MENYUSUI',
            'LANSIA',
            '3 BUTA',
            'BERKEBUTUHAN KHUSUS',
            'SEHAT LAYAK HUNI',
            'TIDAK SEHAT LAYAK HUNI',
            'MEMILIKI TEMPAT PEMBUANGAN SAMPAH',
            'MEMILIKI SPAL/PEMBUANGAN AIR',
            'MEMILIKI SARANA MCK DAN SEPTIC TANK',
            'PDAM',
            'SUMUR',
            'DLL',
            'BERAS',
            'NON BERAS',
            'UP2K',
            'PEMANFAATAN TANAH PEKARANGAN',
            'INDUSTRI RUMAH TANGGA',
            'KESEHATAN LINGKUNGAN',
        ]);
    }

    public function test_header_kolom_pdf_rekap_pkk_rt_tetap_sesuai_mapping_autentik(): void
    {
        $this->assertPdfReportHeadersInOrder('pdf.rekap_catatan_data_kegiatan_warga_pkk_rt_report', [
            'NO',
            'NAMA DASAWISMA',
            'JML KRT',
            'JML KK',
            'JUMLAH ANGGOTA KELUARGA',
            'JUMLAH RUMAH',
            'SUMBER AIR',
            'MAKANAN',
            'WARGA MENGIKUTI KEGIATAN',
            'KET',
            'TOTAL',
            'BALITA',
            'PUS',
            'WUS',
            'IBU HAMIL',
            'IBU MENYUSUI',
            'LANSIA',
            '3 BUTA',
            'BERKEBUTUHAN KHUSUS',
            'SEHAT LAYAK HUNI',
            'TIDAK SEHAT LAYAK HUNI',
            'MEMILIKI TTMP/PEMBUANGAN SAMPAH',
            'MEMILIKI SPAL/PEMBUANGAN AIR',
            'MEMILIKI SARANA MCK DAN SEPTIC TANK',
            'PDAM',
            'SUMUR',
            'DLL',
            'BERAS',
            'NON BERAS',
            'UP2K',
            'PEMANFAATAN TANAH PEKARANGAN',
            'INDUSTRI RUMAH TANGGA',
            'KESEHATAN LINGKUNGAN',
        ]);
    }

    public function test_header_kolom_pdf_catatan_pkk_rw_tetap_sesuai_mapping_autentik(): void
    {
        $this->assertPdfReportHeadersInOrder('pdf.catatan_data_kegiatan_warga_pkk_rw_report', [
            'NO',
            'NOMOR RT',
            'JML DASAWISMA',
            'JML KRT',
            'JML KK',
            'JUMLAH ANGGOTA KELUARGA',
            'KRITERIA RUMAH',
            'SUMBER AIR KELUARGA',
            'JUMLAH SARANA MCK',
            'MAKANAN',
            'WARGA MENGIKUTI KEGIATAN',
            'KET',
            'TOTAL',
            'BALITA',
            'PUS',
            'WUS',
            'IBU HAMIL',
            'IBU MENYUSUI',
            'LANSIA',
            '3 BUTA',
            'SEHAT LAYAK HUNI',
            'TIDAK SEHAT LAYAK HUNI',
            'MEMILIKI TTMP. PEMB. SAMPAH',
            'MEMILIKI SPAL DAN PENYERAPAN AIR',
            'PDAM',
            'SUMUR',
            'SUNGAI',
            'DLL',
            'BERAS',
            'NON BERAS',
            'UP2K',
            'PEMANFAATAN TANAH PEKARANGAN',
            'INDUSTRI RUMAH TANGGA',
            'KESEHATAN LINGKUNGAN',
        ]);
    }

    public function test_admin_desa_dapat_mencetak_pdf_rekap_416a_416b_dan_416c_desanya_sendiri(): void
    {
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $this->desaA->id]);
        $user->assignRole('admin-desa');

        $this->seedDataWargaDenganAnggota($user, 'desa', $this->desaA->id, 'Melati', 'Kepala Desa');

        $responseDasaWisma = $this->actingAs($user)->get(route('desa.catatan-keluarga.rekap-dasa-wisma.report'));
        $responseDasaWisma->assertOk();
        $responseDasaWisma->assertHeader('content-type', 'application/pdf');

        $responsePkkRt = $this->actingAs($user)->get(route('desa.catatan-keluarga.rekap-pkk-rt.report'));
        $responsePkkRt->assertOk();
        $responsePkkRt->assertHeader('content-type', 'application/pdf');

        $responsePkkRw = $this->actingAs($user)->get(route('desa.catatan-keluarga.catatan-pkk-rw.report'));
        $responsePkkRw->assertOk();
        $responsePkkRw->assertHeader('content-type', 'application/pdf');
    }

    public function test_admin_kecamatan_dapat_mencetak_pdf_rekap_416a_416b_dan_416c_kecamatannya_sendiri(): void
    {
        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $this->kecamatanA->id]);
        $user->assignRole('admin-kecamatan');

        $this->seedDataWargaDenganAnggota($user, 'kecamatan', $this->kecamatanA->id, 'Mawar', 'Kepala Kecamatan');

        $responseDasaWisma = $this->actingAs($user)->get(route('kecamatan.catatan-keluarga.rekap-dasa-wisma.report'));
        $responseDasaWisma->assertOk();
        $responseDasaWisma->assertHeader('content-type', 'application/pdf');

        $responsePkkRt = $this->actingAs($user)->get(route('kecamatan.catatan-keluarga.rekap-pkk-rt.report'));
        $responsePkkRt->assertOk();
        $responsePkkRt->assertHeader('content-type', 'application/pdf');

        $responsePkkRw = $this->actingAs($user)->get(route('kecamatan.catatan-keluarga.catatan-pkk-rw.report'));
        $responsePkkRw->assertOk();
        $responsePkkRw->assertHeader('content-type', 'application/pdf');
    }

    public function test_laporan_pdf_rekap_416_tetap_aman_saat_scope_metadata_tidak_sinkron(): void
    {
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $this->kecamatanB->id]);
        $user->assignRole('admin-desa');

        $responseDasaWisma = $this->actingAs($user)->get(route('desa.catatan-keluarga.rekap-dasa-wisma.report'));
        $responseDasaWisma->assertStatus(403);

        $responsePkkRt = $this->actingAs($user)->get(route('desa.catatan-keluarga.rekap-pkk-rt.report'));
        $responsePkkRt->assertStatus(403);

        $responsePkkRw = $this->actingAs($user)->get(route('desa.catatan-keluarga.catatan-pkk-rw.report'));
        $responsePkkRw->assertStatus(403);
    }

    private function seedDataWargaDenganAnggota(User $user, string $level, int $areaId, string $dasaWisma, string $namaKepala): void
    {
        $dataWarga = DataWarga::create([
            'dasawisma' => $dasaWisma,
            'nama_kepala_keluarga' => $namaKepala,
            'alamat' => 'Alamat',
            'jumlah_warga_laki_laki' => 0,
            'jumlah_warga_perempuan' => 0,
            'keterangan' => null,
            'level' => $level,
            'area_id' => $areaId,
            'created_by' => $user->id,
        ]);

        DataWargaAnggota::create([
            'data_warga_id' => $dataWarga->id,
            'nomor_urut' => 1,
            'nama' => 'Anggota L',
            'jenis_kelamin' => 'L',
            'umur_tahun' => 33,
            'status_perkawinan' => 'Kawin',
            'level' => $level,
            'area_id' => $areaId,
            'created_by' => $user->id,
        ]);

        DataWargaAnggota::create([
            'data_warga_id' => $dataWarga->id,
            'nomor_urut' => 2,
            'nama' => 'Anggota P',
            'jenis_kelamin' => 'P',
            'umur_tahun' => 31,
            'status_perkawinan' => 'Kawin',
            'level' => $level,
            'area_id' => $areaId,
            'created_by' => $user->id,
        ]);

        DataWargaAnggota::create([
            'data_warga_id' => $dataWarga->id,
            'nomor_urut' => 3,
            'nama' => 'Anggota Balita',
            'jenis_kelamin' => 'P',
            'umur_tahun' => 4,
            'status_perkawinan' => null,
            'level' => $level,
            'area_id' => $areaId,
            'created_by' => $user->id,
        ]);

        DataKegiatanWarga::create([
            'kegiatan' => 'Penghayatan dan Pengamalan Pancasila',
            'aktivitas' => true,
            'keterangan' => null,
            'level' => $level,
            'area_id' => $areaId,
            'created_by' => $user->id,
        ]);

        DataKegiatanWarga::create([
            'kegiatan' => 'Kerja Bakti',
            'aktivitas' => true,
            'keterangan' => null,
            'level' => $level,
            'area_id' => $areaId,
            'created_by' => $user->id,
        ]);
    }
}
