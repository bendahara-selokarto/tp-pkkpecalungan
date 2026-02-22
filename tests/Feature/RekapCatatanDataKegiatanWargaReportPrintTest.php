<?php

namespace Tests\Feature;

use App\Domains\Wilayah\CatatanKeluarga\Repositories\CatatanKeluargaRepositoryInterface;
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

    public function test_header_kolom_pdf_rekap_rw_tetap_sesuai_mapping_autentik_416d(): void
    {
        $this->assertPdfReportHeadersInOrder('pdf.rekap_catatan_data_kegiatan_warga_rw_report', [
            'NO',
            'NOMOR RW',
            'JML RT',
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

    public function test_header_kolom_pdf_catatan_tp_pkk_desa_kelurahan_tetap_sesuai_mapping_autentik_417a(): void
    {
        $this->assertPdfReportHeadersInOrder('pdf.catatan_data_kegiatan_warga_tp_pkk_desa_kelurahan_report', [
            'NO',
            'NAMA DUSUN/LINGKUNGAN',
            'JML RW',
            'JML RT',
            'JML DASAWISMA',
            'JML KRT',
            'JML KK',
            'JUMLAH ANGGOTA KELUARGA',
            'KRITERIA RUMAH',
            'SUMBER AIR KELUARGA',
            'JUMLAH SARANA MCK',
            'MAKANAN POKOK',
            'WARGA MENGIKUTI KEGIATAN',
            'KETERANGAN',
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
            'MEMILIKI TTMP. PEMB SAMPAH',
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

    public function test_header_kolom_pdf_catatan_tp_pkk_kecamatan_tetap_sesuai_mapping_autentik_417b(): void
    {
        $this->assertPdfReportHeadersInOrder('pdf.catatan_data_kegiatan_warga_tp_pkk_kecamatan_report', [
            'NO',
            'NAMA DESA/KELURAHAN',
            'JML DUSUN/LINGK',
            'JUML RW',
            'JUML RT',
            'JML DASAWISMA',
            'JUML KRT',
            'JUML KK',
            'JUMLAH ANGGOTA KELUARGA',
            'KRITERIA RUMAH',
            'SUMBER AIR KELUARGA',
            'JUMLAH SARANA MCK',
            'MAKANAN POKOK',
            'WARGA MENGIKUTI KEGIATAN',
            'KETERANGAN',
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
            'MEMILIKI TTMP. PEMB SAMPAH',
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

    public function test_header_kolom_pdf_catatan_tp_pkk_kabupaten_kota_tetap_sesuai_mapping_autentik_417c(): void
    {
        $this->assertPdfReportHeadersInOrder('pdf.catatan_data_kegiatan_warga_tp_pkk_kabupaten_kota_report', [
            'NO',
            'NAMA KECAMATAN',
            'JML DESA/KEL',
            'JML DUSUN/LINGK',
            'JUML RW',
            'JUML RT',
            'JUML DASAWISMA',
            'JUML KRT',
            'JUML KK',
            'JUMLAH ANGGOTA KELUARGA',
            'KRITERIA RUMAH',
            'SUMBER AIR KELUARGA',
            'JUMLAH SARANA MCK',
            'MAKANAN POKOK',
            'WARGA MENGIKUTI KEGIATAN',
            'KETERANGAN',
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
            'MEMILIKI TTMP. PEMB SAMPAH',
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

    public function test_header_kolom_pdf_catatan_tp_pkk_provinsi_tetap_sesuai_mapping_autentik_417d(): void
    {
        $this->assertPdfReportHeadersInOrder('pdf.catatan_data_kegiatan_warga_tp_pkk_provinsi_report', [
            'NO',
            'NAMA KAB/KOTA',
            'JML KEC',
            'JUML DESA/KEL',
            'JUML DUSUN/LINGK',
            'JUML RW',
            'JUML RT',
            'JUML DASAWISMA',
            'JUML KRT',
            'JUML KK',
            'JUMLAH ANGGOTA KELUARGA',
            'KRITERIA RUMAH',
            'SUMBER AIR KELUARGA',
            'JUMLAH SARANA MCK',
            'MAKANAN POKOK',
            'WARGA MENGIKUTI KEGIATAN',
            'KETERANGAN',
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
            'MEMILIKI TTMP. PEMB SAMPAH',
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

    public function test_header_kolom_pdf_rekap_ibu_hamil_dasawisma_tetap_sesuai_mapping_autentik_418a(): void
    {
        $this->assertPdfReportHeadersInOrder('pdf.rekap_ibu_hamil_melahirkan_dasawisma_report', [
            'NO.',
            'NAMA IBU',
            'NAMA SUAMI',
            'STATUS(HAMIL/MELAHIRKAN/NIFAS)',
            'CATATAN KELAHIRAN',
            'CATATAN KEMATIAN',
            'NAMA BAYI',
            'JENIS KELAMIN',
            'TGL. LAHIR',
            'AKTE KELAHIRAN',
            'NAMA IBU/BAYI/BALITA',
            'STATUS(IBU/BALITA/BAYI)',
            'TGL. MENINGGAL',
            'SEBAB MENINGGAL',
            'KETERANGAN',
        ]);
    }

    public function test_header_kolom_pdf_rekap_ibu_hamil_pkk_rt_tetap_sesuai_mapping_autentik_418b(): void
    {
        $this->assertPdfReportHeadersInOrder('pdf.rekap_ibu_hamil_melahirkan_pkk_rt_report', [
            'NO.',
            'NAMA KELOMPOK DASA WISMA',
            'JUMLAH IBU',
            'JUMLAH BAYI',
            'JUMLAH BALITA MENINGGAL',
            'KETERANGAN',
            'HAMIL',
            'MELAHIRKAN',
            'NIFAS',
            'MENINGGAL',
            'LAHIR',
            'AKTE KELAHIRAN',
            'MENINGGAL',
        ]);
    }

    public function test_header_kolom_pdf_rekap_ibu_hamil_pkk_rw_tetap_sesuai_mapping_autentik_418c(): void
    {
        $this->assertPdfReportHeadersInOrder('pdf.rekap_ibu_hamil_melahirkan_pkk_rw_report', [
            'NO.',
            'NOMOR RT',
            'JUMLAH KELOMPOK DASAWISMA',
            'JUMLAH IBU',
            'JUMLAH BAYI',
            'JUMLAH BALITA MENINGGAL',
            'KETERANGAN',
            'HAMIL',
            'MELAHIRKAN',
            'NIFAS',
            'MENINGGAL',
            'LAHIR',
            'AKTE KELAHIRAN',
            'MENINGGAL',
        ]);
    }

    public function test_header_kolom_pdf_rekap_ibu_hamil_dusun_lingkungan_tetap_sesuai_mapping_autentik_418d(): void
    {
        $this->assertPdfReportHeadersInOrder('pdf.rekap_ibu_hamil_melahirkan_dusun_lingkungan_report', [
            'NO',
            'NOMOR RW',
            'JUMLAH',
            'JUMLAH IBU',
            'JUMLAH BAYI',
            'JML. BALITA MENINGGAL',
            'KETERANGAN',
            'RT',
            'DASA WISMA',
            'HAMIL',
            'MELAHIRKAN',
            'NIFAS',
            'MENINGGAL',
            'LAHIR',
            'AKTE KELAHIRAN',
            'MENINGGAL',
        ]);
    }

    public function test_rekap_418d_menghitung_jumlah_dasawisma_sebagai_penjumlahan_per_rt_dalam_rw(): void
    {
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $this->desaA->id]);

        DataWarga::create([
            'dasawisma' => 'Melati',
            'nama_kepala_keluarga' => 'Kepala 1',
            'alamat' => 'Dusun Anggrek RT 01 / RW 07',
            'jumlah_warga_laki_laki' => 0,
            'jumlah_warga_perempuan' => 0,
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $user->id,
        ]);

        DataWarga::create([
            'dasawisma' => 'Melati',
            'nama_kepala_keluarga' => 'Kepala 2',
            'alamat' => 'Dusun Anggrek RT 02 / RW 07',
            'jumlah_warga_laki_laki' => 0,
            'jumlah_warga_perempuan' => 0,
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $user->id,
        ]);

        $repository = app(CatatanKeluargaRepositoryInterface::class);
        $rows = $repository->getRekapIbuHamilPkkDusunLingkunganByLevelAndArea('desa', $this->desaA->id);

        $this->assertCount(1, $rows);
        $row = $rows->first();
        $this->assertSame('07', $row['nomor_rw']);
        $this->assertSame(2, $row['jumlah_rt']);
        $this->assertSame(2, $row['jumlah_kelompok_dasawisma']);
    }

    public function test_rekap_tp_pkk_desa_kelurahan_mengagregasi_data_418d_per_desa_kelurahan(): void
    {
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $this->desaA->id]);

        DataWarga::create([
            'dasawisma' => 'Melati',
            'nama_kepala_keluarga' => 'Kepala 1',
            'alamat' => 'Dusun Anggrek RT 01 / RW 07',
            'jumlah_warga_laki_laki' => 0,
            'jumlah_warga_perempuan' => 0,
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $user->id,
        ]);

        DataWarga::create([
            'dasawisma' => 'Melati',
            'nama_kepala_keluarga' => 'Kepala 2',
            'alamat' => 'Dusun Anggrek RT 02 / RW 07',
            'jumlah_warga_laki_laki' => 0,
            'jumlah_warga_perempuan' => 0,
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $user->id,
        ]);

        DataWarga::create([
            'dasawisma' => 'Dahlia',
            'nama_kepala_keluarga' => 'Kepala 3',
            'alamat' => 'Dusun Melati RT 01 / RW 08',
            'jumlah_warga_laki_laki' => 0,
            'jumlah_warga_perempuan' => 0,
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $user->id,
        ]);

        $repository = app(CatatanKeluargaRepositoryInterface::class);
        $rows = $repository->getRekapIbuHamilTpPkkDesaKelurahanByLevelAndArea('desa', $this->desaA->id);

        $this->assertCount(1, $rows);
        $row = $rows->first();
        $this->assertSame('DESA Gombong', $row['desa_kelurahan']);
        $this->assertSame(2, $row['jumlah_dusun_lingkungan']);
        $this->assertSame(2, $row['jumlah_rw']);
        $this->assertSame(3, $row['jumlah_rt']);
        $this->assertSame(3, $row['jumlah_kelompok_dasawisma']);
        $this->assertArrayHasKey('jumlah_ibu_hamil', $row);
        $this->assertArrayHasKey('jumlah_bayi_lahir_l', $row);
    }

    public function test_admin_desa_dapat_mencetak_pdf_rekap_416a_416b_416c_416d_417a_417b_417c_417d_418a_418b_418c_dan_418d_desanya_sendiri(): void
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

        $responseRekapRw = $this->actingAs($user)->get(route('desa.catatan-keluarga.rekap-rw.report'));
        $responseRekapRw->assertOk();
        $responseRekapRw->assertHeader('content-type', 'application/pdf');

        $response417a = $this->actingAs($user)->get(route('desa.catatan-keluarga.tp-pkk-desa-kelurahan.report'));
        $response417a->assertOk();
        $response417a->assertHeader('content-type', 'application/pdf');

        $response417b = $this->actingAs($user)->get(route('desa.catatan-keluarga.tp-pkk-kecamatan.report'));
        $response417b->assertOk();
        $response417b->assertHeader('content-type', 'application/pdf');

        $response417c = $this->actingAs($user)->get(route('desa.catatan-keluarga.tp-pkk-kabupaten-kota.report'));
        $response417c->assertOk();
        $response417c->assertHeader('content-type', 'application/pdf');

        $response417d = $this->actingAs($user)->get(route('desa.catatan-keluarga.tp-pkk-provinsi.report'));
        $response417d->assertOk();
        $response417d->assertHeader('content-type', 'application/pdf');

        $response418a = $this->actingAs($user)->get(route('desa.catatan-keluarga.rekap-ibu-hamil-dasawisma.report'));
        $response418a->assertOk();
        $response418a->assertHeader('content-type', 'application/pdf');

        $response418b = $this->actingAs($user)->get(route('desa.catatan-keluarga.rekap-ibu-hamil-pkk-rt.report'));
        $response418b->assertOk();
        $response418b->assertHeader('content-type', 'application/pdf');

        $response418c = $this->actingAs($user)->get(route('desa.catatan-keluarga.rekap-ibu-hamil-pkk-rw.report'));
        $response418c->assertOk();
        $response418c->assertHeader('content-type', 'application/pdf');

        $response418d = $this->actingAs($user)->get(route('desa.catatan-keluarga.rekap-ibu-hamil-pkk-dusun-lingkungan.report'));
        $response418d->assertOk();
        $response418d->assertHeader('content-type', 'application/pdf');
    }

    public function test_admin_kecamatan_dapat_mencetak_pdf_rekap_416a_416b_416c_416d_417a_417b_417c_417d_418a_418b_418c_dan_418d_kecamatannya_sendiri(): void
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

        $responseRekapRw = $this->actingAs($user)->get(route('kecamatan.catatan-keluarga.rekap-rw.report'));
        $responseRekapRw->assertOk();
        $responseRekapRw->assertHeader('content-type', 'application/pdf');

        $response417a = $this->actingAs($user)->get(route('kecamatan.catatan-keluarga.tp-pkk-desa-kelurahan.report'));
        $response417a->assertOk();
        $response417a->assertHeader('content-type', 'application/pdf');

        $response417b = $this->actingAs($user)->get(route('kecamatan.catatan-keluarga.tp-pkk-kecamatan.report'));
        $response417b->assertOk();
        $response417b->assertHeader('content-type', 'application/pdf');

        $response417c = $this->actingAs($user)->get(route('kecamatan.catatan-keluarga.tp-pkk-kabupaten-kota.report'));
        $response417c->assertOk();
        $response417c->assertHeader('content-type', 'application/pdf');

        $response417d = $this->actingAs($user)->get(route('kecamatan.catatan-keluarga.tp-pkk-provinsi.report'));
        $response417d->assertOk();
        $response417d->assertHeader('content-type', 'application/pdf');

        $response418a = $this->actingAs($user)->get(route('kecamatan.catatan-keluarga.rekap-ibu-hamil-dasawisma.report'));
        $response418a->assertOk();
        $response418a->assertHeader('content-type', 'application/pdf');

        $response418b = $this->actingAs($user)->get(route('kecamatan.catatan-keluarga.rekap-ibu-hamil-pkk-rt.report'));
        $response418b->assertOk();
        $response418b->assertHeader('content-type', 'application/pdf');

        $response418c = $this->actingAs($user)->get(route('kecamatan.catatan-keluarga.rekap-ibu-hamil-pkk-rw.report'));
        $response418c->assertOk();
        $response418c->assertHeader('content-type', 'application/pdf');

        $response418d = $this->actingAs($user)->get(route('kecamatan.catatan-keluarga.rekap-ibu-hamil-pkk-dusun-lingkungan.report'));
        $response418d->assertOk();
        $response418d->assertHeader('content-type', 'application/pdf');
    }

    public function test_laporan_pdf_rekap_416_417_dan_418_tetap_aman_saat_scope_metadata_tidak_sinkron(): void
    {
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $this->kecamatanB->id]);
        $user->assignRole('admin-desa');

        $responseDasaWisma = $this->actingAs($user)->get(route('desa.catatan-keluarga.rekap-dasa-wisma.report'));
        $responseDasaWisma->assertStatus(403);

        $responsePkkRt = $this->actingAs($user)->get(route('desa.catatan-keluarga.rekap-pkk-rt.report'));
        $responsePkkRt->assertStatus(403);

        $responsePkkRw = $this->actingAs($user)->get(route('desa.catatan-keluarga.catatan-pkk-rw.report'));
        $responsePkkRw->assertStatus(403);

        $responseRekapRw = $this->actingAs($user)->get(route('desa.catatan-keluarga.rekap-rw.report'));
        $responseRekapRw->assertStatus(403);

        $response417a = $this->actingAs($user)->get(route('desa.catatan-keluarga.tp-pkk-desa-kelurahan.report'));
        $response417a->assertStatus(403);

        $response417b = $this->actingAs($user)->get(route('desa.catatan-keluarga.tp-pkk-kecamatan.report'));
        $response417b->assertStatus(403);

        $response417c = $this->actingAs($user)->get(route('desa.catatan-keluarga.tp-pkk-kabupaten-kota.report'));
        $response417c->assertStatus(403);

        $response417d = $this->actingAs($user)->get(route('desa.catatan-keluarga.tp-pkk-provinsi.report'));
        $response417d->assertStatus(403);

        $response418a = $this->actingAs($user)->get(route('desa.catatan-keluarga.rekap-ibu-hamil-dasawisma.report'));
        $response418a->assertStatus(403);

        $response418b = $this->actingAs($user)->get(route('desa.catatan-keluarga.rekap-ibu-hamil-pkk-rt.report'));
        $response418b->assertStatus(403);

        $response418c = $this->actingAs($user)->get(route('desa.catatan-keluarga.rekap-ibu-hamil-pkk-rw.report'));
        $response418c->assertStatus(403);

        $response418d = $this->actingAs($user)->get(route('desa.catatan-keluarga.rekap-ibu-hamil-pkk-dusun-lingkungan.report'));
        $response418d->assertStatus(403);
    }

    private function seedDataWargaDenganAnggota(User $user, string $level, int $areaId, string $dasaWisma, string $namaKepala): void
    {
        $dataWarga = DataWarga::create([
            'dasawisma' => $dasaWisma,
            'nama_kepala_keluarga' => $namaKepala,
            'alamat' => 'Dusun Melati RT 01 / RW 02',
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
