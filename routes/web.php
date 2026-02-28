<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ArsipController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UiRuntimeErrorLogController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdmin\AccessControlManagementController;
use App\Http\Controllers\SuperAdmin\UserManagementController;
use App\Http\Controllers\SuperAdmin\ArsipManagementController;
use App\Domains\Wilayah\Activities\Controllers\DesaActivityController;
use App\Domains\Wilayah\Activities\Controllers\ActivityPrintController;
use App\Domains\Wilayah\Activities\Controllers\KecamatanActivityController;
use App\Domains\Wilayah\Activities\Controllers\KecamatanDesaActivityController;
use App\Domains\Wilayah\Arsip\Controllers\KecamatanDesaArsipController;
use App\Domains\Wilayah\AgendaSurat\Controllers\DesaAgendaSuratController;
use App\Domains\Wilayah\AgendaSurat\Controllers\KecamatanAgendaSuratController;
use App\Domains\Wilayah\AgendaSurat\Controllers\AgendaSuratReportPrintController;
use App\Domains\Wilayah\BukuDaftarHadir\Controllers\DesaBukuDaftarHadirController;
use App\Domains\Wilayah\BukuDaftarHadir\Controllers\KecamatanBukuDaftarHadirController;
use App\Domains\Wilayah\BukuDaftarHadir\Controllers\BukuDaftarHadirPrintController;
use App\Domains\Wilayah\BukuTamu\Controllers\DesaBukuTamuController;
use App\Domains\Wilayah\BukuTamu\Controllers\KecamatanBukuTamuController;
use App\Domains\Wilayah\BukuTamu\Controllers\BukuTamuPrintController;
use App\Domains\Wilayah\BukuNotulenRapat\Controllers\DesaBukuNotulenRapatController;
use App\Domains\Wilayah\BukuNotulenRapat\Controllers\KecamatanBukuNotulenRapatController;
use App\Domains\Wilayah\BukuNotulenRapat\Controllers\BukuNotulenRapatPrintController;
use App\Domains\Wilayah\Inventaris\Controllers\DesaInventarisController;
use App\Domains\Wilayah\Inventaris\Controllers\KecamatanInventarisController;
use App\Domains\Wilayah\Inventaris\Controllers\InventarisReportPrintController;
use App\Domains\Wilayah\Bantuan\Controllers\DesaBantuanController;
use App\Domains\Wilayah\Bantuan\Controllers\KecamatanBantuanController;
use App\Domains\Wilayah\Bantuan\Controllers\BantuanReportPrintController;
use App\Domains\Wilayah\BukuKeuangan\Controllers\DesaBukuKeuanganController;
use App\Domains\Wilayah\BukuKeuangan\Controllers\KecamatanBukuKeuanganController;
use App\Domains\Wilayah\BukuKeuangan\Controllers\BukuKeuanganReportPrintController;
use App\Domains\Wilayah\AnggotaPokja\Controllers\DesaAnggotaPokjaController;
use App\Domains\Wilayah\AnggotaPokja\Controllers\KecamatanAnggotaPokjaController;
use App\Domains\Wilayah\AnggotaPokja\Controllers\AnggotaPokjaReportPrintController;
use App\Domains\Wilayah\AnggotaTimPenggerak\Controllers\DesaAnggotaTimPenggerakController;
use App\Domains\Wilayah\AnggotaTimPenggerak\Controllers\KecamatanAnggotaTimPenggerakController;
use App\Domains\Wilayah\AnggotaTimPenggerak\Controllers\AnggotaTimPenggerakReportPrintController;
use App\Domains\Wilayah\KaderKhusus\Controllers\DesaKaderKhususController;
use App\Domains\Wilayah\KaderKhusus\Controllers\KecamatanKaderKhususController;
use App\Domains\Wilayah\KaderKhusus\Controllers\KaderKhususReportPrintController;
use App\Domains\Wilayah\PrestasiLomba\Controllers\DesaPrestasiLombaController;
use App\Domains\Wilayah\PrestasiLomba\Controllers\KecamatanPrestasiLombaController;
use App\Domains\Wilayah\PrestasiLomba\Controllers\PrestasiLombaPrintController;
use App\Domains\Wilayah\Bkl\Controllers\DesaBklController;
use App\Domains\Wilayah\Bkl\Controllers\KecamatanBklController;
use App\Domains\Wilayah\Bkl\Controllers\BklPrintController;
use App\Domains\Wilayah\Bkr\Controllers\DesaBkrController;
use App\Domains\Wilayah\Bkr\Controllers\KecamatanBkrController;
use App\Domains\Wilayah\Bkr\Controllers\BkrPrintController;
use App\Domains\Wilayah\Paar\Controllers\DesaPaarController;
use App\Domains\Wilayah\Paar\Controllers\KecamatanPaarController;
use App\Domains\Wilayah\Paar\Controllers\PaarPrintController;
use App\Domains\Wilayah\Koperasi\Controllers\DesaKoperasiController;
use App\Domains\Wilayah\Koperasi\Controllers\KecamatanKoperasiController;
use App\Domains\Wilayah\Koperasi\Controllers\KoperasiPrintController;
use App\Domains\Wilayah\WarungPkk\Controllers\DesaWarungPkkController;
use App\Domains\Wilayah\WarungPkk\Controllers\KecamatanWarungPkkController;
use App\Domains\Wilayah\WarungPkk\Controllers\WarungPkkPrintController;
use App\Domains\Wilayah\DataWarga\Controllers\DesaDataWargaController;
use App\Domains\Wilayah\DataWarga\Controllers\KecamatanDataWargaController;
use App\Domains\Wilayah\DataWarga\Controllers\DataWargaPrintController;
use App\Domains\Wilayah\DataKegiatanWarga\Controllers\DesaDataKegiatanWargaController;
use App\Domains\Wilayah\DataKegiatanWarga\Controllers\KecamatanDataKegiatanWargaController;
use App\Domains\Wilayah\DataKegiatanWarga\Controllers\DataKegiatanWargaPrintController;
use App\Domains\Wilayah\DataKeluarga\Controllers\DesaDataKeluargaController;
use App\Domains\Wilayah\DataKeluarga\Controllers\KecamatanDataKeluargaController;
use App\Domains\Wilayah\DataKeluarga\Controllers\DataKeluargaPrintController;
use App\Domains\Wilayah\DataIndustriRumahTangga\Controllers\DesaDataIndustriRumahTanggaController;
use App\Domains\Wilayah\DataIndustriRumahTangga\Controllers\KecamatanDataIndustriRumahTanggaController;
use App\Domains\Wilayah\DataIndustriRumahTangga\Controllers\DataIndustriRumahTanggaPrintController;
use App\Domains\Wilayah\DataPelatihanKader\Controllers\DesaDataPelatihanKaderController;
use App\Domains\Wilayah\DataPelatihanKader\Controllers\KecamatanDataPelatihanKaderController;
use App\Domains\Wilayah\DataPelatihanKader\Controllers\DataPelatihanKaderPrintController;
use App\Domains\Wilayah\CatatanKeluarga\Controllers\DesaCatatanKeluargaController;
use App\Domains\Wilayah\CatatanKeluarga\Controllers\KecamatanCatatanKeluargaController;
use App\Domains\Wilayah\CatatanKeluarga\Controllers\CatatanKeluargaPrintController;
use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Controllers\DesaDataPemanfaatanTanahPekaranganHatinyaPkkController;
use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Controllers\KecamatanDataPemanfaatanTanahPekaranganHatinyaPkkController;
use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Controllers\DataPemanfaatanTanahPekaranganHatinyaPkkPrintController;
use App\Domains\Wilayah\TamanBacaan\Controllers\DesaTamanBacaanController;
use App\Domains\Wilayah\TamanBacaan\Controllers\KecamatanTamanBacaanController;
use App\Domains\Wilayah\TamanBacaan\Controllers\TamanBacaanPrintController;
use App\Domains\Wilayah\KejarPaket\Controllers\DesaKejarPaketController;
use App\Domains\Wilayah\KejarPaket\Controllers\KecamatanKejarPaketController;
use App\Domains\Wilayah\KejarPaket\Controllers\KejarPaketPrintController;
use App\Domains\Wilayah\Posyandu\Controllers\DesaPosyanduController;
use App\Domains\Wilayah\Posyandu\Controllers\KecamatanPosyanduController;
use App\Domains\Wilayah\Posyandu\Controllers\PosyanduPrintController;
use App\Domains\Wilayah\SimulasiPenyuluhan\Controllers\DesaSimulasiPenyuluhanController;
use App\Domains\Wilayah\SimulasiPenyuluhan\Controllers\KecamatanSimulasiPenyuluhanController;
use App\Domains\Wilayah\SimulasiPenyuluhan\Controllers\SimulasiPenyuluhanPrintController;
use App\Domains\Wilayah\ProgramPrioritas\Controllers\DesaProgramPrioritasController;
use App\Domains\Wilayah\ProgramPrioritas\Controllers\KecamatanProgramPrioritasController;
use App\Domains\Wilayah\ProgramPrioritas\Controllers\ProgramPrioritasPrintController;
use App\Domains\Wilayah\PilotProjectKeluargaSehat\Controllers\DesaPilotProjectKeluargaSehatController;
use App\Domains\Wilayah\PilotProjectKeluargaSehat\Controllers\KecamatanPilotProjectKeluargaSehatController;
use App\Domains\Wilayah\PilotProjectKeluargaSehat\Controllers\PilotProjectKeluargaSehatPrintController;
use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Controllers\DesaPilotProjectNaskahPelaporanController;
use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Controllers\KecamatanPilotProjectNaskahPelaporanController;
use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Controllers\PilotProjectNaskahPelaporanPrintController;
use App\Domains\Wilayah\LaporanTahunanPkk\Controllers\DesaLaporanTahunanPkkController;
use App\Domains\Wilayah\LaporanTahunanPkk\Controllers\KecamatanLaporanTahunanPkkController;
use App\Domains\Wilayah\LaporanTahunanPkk\Controllers\LaporanTahunanPkkPrintController;


Route::get('/', function () {
    if (auth()->user()?->hasRole('super-admin')) {
        return redirect()->route('super-admin.users.index');
    }

    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
});

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
Route::get('/dashboard/charts/report/pdf', [DashboardController::class, 'printChartPdf'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.charts.report');
Route::get('/arsip', ArsipController::class)
    ->middleware(['auth', 'verified'])
    ->name('arsip.index');
Route::post('/arsip', [ArsipController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('arsip.store');
Route::put('/arsip/{arsipDocument}', [ArsipController::class, 'update'])
    ->middleware(['auth', 'verified'])
    ->name('arsip.update');
Route::delete('/arsip/{arsipDocument}', [ArsipController::class, 'destroy'])
    ->middleware(['auth', 'verified'])
    ->name('arsip.destroy');
Route::get('/arsip/download/{arsipDocument}', [ArsipController::class, 'download'])
    ->middleware(['auth', 'verified'])
    ->name('arsip.download');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/ui/runtime-errors', UiRuntimeErrorLogController::class)
        ->middleware('throttle:20,1')
        ->name('ui.runtime-errors.store');
});

Route::middleware(['auth', 'role:super-admin'])
    ->prefix('super-admin')
    ->name('super-admin.')
    ->group(function () {
        Route::resource('users', UserManagementController::class);
        Route::get('access-control', AccessControlManagementController::class)
            ->name('access-control.index');
        Route::resource('arsip', ArsipManagementController::class)
            ->parameters(['arsip' => 'arsipDocument']);
    });

Route::prefix('desa')
    ->name('desa.')
    ->middleware(['auth', 'scope.role:desa', 'module.visibility'])
    ->group(function () {

        Route::resource('activities', DesaActivityController::class);
        Route::get('activities/{id}/attachments/{type}', [DesaActivityController::class, 'attachment'])
            ->whereIn('type', ['image', 'document'])
            ->name('activities.attachments.show');
        Route::resource('agenda-surat', DesaAgendaSuratController::class);
        Route::get('agenda-surat/{id}/attachment/data-dukung', [DesaAgendaSuratController::class, 'attachment'])
            ->name('agenda-surat.attachments.show');
        Route::resource('buku-daftar-hadir', DesaBukuDaftarHadirController::class);
        Route::resource('buku-tamu', DesaBukuTamuController::class);
        Route::resource('buku-notulen-rapat', DesaBukuNotulenRapatController::class);
        Route::resource('inventaris', DesaInventarisController::class);
        Route::resource('bantuans', DesaBantuanController::class);
        Route::resource('buku-keuangan', DesaBukuKeuanganController::class);
        Route::resource('anggota-pokja', DesaAnggotaPokjaController::class);
        Route::resource('anggota-tim-penggerak', DesaAnggotaTimPenggerakController::class);
        Route::resource('kader-khusus', DesaKaderKhususController::class);
        Route::resource('prestasi-lomba', DesaPrestasiLombaController::class);
        Route::resource('bkl', DesaBklController::class);
        Route::resource('bkr', DesaBkrController::class);
        Route::resource('paar', DesaPaarController::class);
        Route::resource('koperasi', DesaKoperasiController::class);
        Route::resource('data-warga', DesaDataWargaController::class);
        Route::resource('data-kegiatan-warga', DesaDataKegiatanWargaController::class);
        Route::resource('data-keluarga', DesaDataKeluargaController::class);
        Route::resource('data-industri-rumah-tangga', DesaDataIndustriRumahTanggaController::class);
        Route::resource('data-pelatihan-kader', DesaDataPelatihanKaderController::class);
        Route::resource('data-pemanfaatan-tanah-pekarangan-hatinya-pkk', DesaDataPemanfaatanTanahPekaranganHatinyaPkkController::class)
            ->parameters(['data-pemanfaatan-tanah-pekarangan-hatinya-pkk' => 'dataPemanfaatan']);
        Route::get('catatan-keluarga', [DesaCatatanKeluargaController::class, 'index'])->name('catatan-keluarga.index');
        Route::resource('warung-pkk', DesaWarungPkkController::class);
        Route::resource('taman-bacaan', DesaTamanBacaanController::class);
        Route::resource('kejar-paket', DesaKejarPaketController::class);
        Route::resource('posyandu', DesaPosyanduController::class);
        Route::resource('simulasi-penyuluhan', DesaSimulasiPenyuluhanController::class);
        Route::resource('program-prioritas', DesaProgramPrioritasController::class);
        Route::resource('pilot-project-keluarga-sehat', DesaPilotProjectKeluargaSehatController::class);
        Route::resource('pilot-project-naskah-pelaporan', DesaPilotProjectNaskahPelaporanController::class);
        Route::resource('laporan-tahunan-pkk', DesaLaporanTahunanPkkController::class);
        Route::get('activities/{id}/print', [ActivityPrintController::class, 'printDesa'])->name('activities.print');
        Route::get('activities/report/pdf', [ActivityPrintController::class, 'printDesaReport'])->name('activities.report');
        Route::get('buku-notulen-rapat/report/pdf', [BukuNotulenRapatPrintController::class, 'printDesaReport'])->name('buku-notulen-rapat.report');
        Route::get('buku-daftar-hadir/report/pdf', [BukuDaftarHadirPrintController::class, 'printDesaReport'])->name('buku-daftar-hadir.report');
        Route::get('buku-tamu/report/pdf', [BukuTamuPrintController::class, 'printDesaReport'])->name('buku-tamu.report');
        Route::get('agenda-surat/report/pdf', [AgendaSuratReportPrintController::class, 'printDesaReport'])->name('agenda-surat.report');
        Route::get('agenda-surat/ekspedisi/report/pdf', [AgendaSuratReportPrintController::class, 'printDesaEkspedisiReport'])->name('agenda-surat.ekspedisi.report');
        Route::get('inventaris/report/pdf', [InventarisReportPrintController::class, 'printDesaReport'])->name('inventaris.report');
        Route::get('bantuans/report/pdf', [BantuanReportPrintController::class, 'printDesaReport'])->name('bantuans.report');
        Route::get('buku-keuangan/report/pdf', [BukuKeuanganReportPrintController::class, 'printDesaReport'])->name('buku-keuangan.report');
        Route::get('bantuans/keuangan/report/pdf', [BukuKeuanganReportPrintController::class, 'printDesaReport'])->name('bantuans.keuangan.report');
        Route::get('anggota-pokja/report/pdf', [AnggotaPokjaReportPrintController::class, 'printDesaReport'])->name('anggota-pokja.report');
        Route::get('anggota-tim-penggerak/report/pdf', [AnggotaTimPenggerakReportPrintController::class, 'printDesaReport'])->name('anggota-tim-penggerak.report');
        Route::get('anggota-tim-penggerak-kader/report/pdf', [AnggotaTimPenggerakReportPrintController::class, 'printDesaAnggotaDanKaderReport'])->name('anggota-tim-penggerak-kader.report');
        Route::get('kader-khusus/report/pdf', [KaderKhususReportPrintController::class, 'printDesaReport'])->name('kader-khusus.report');
        Route::get('prestasi-lomba/report/pdf', [PrestasiLombaPrintController::class, 'printDesaReport'])->name('prestasi-lomba.report');
        Route::get('bkl/report/pdf', [BklPrintController::class, 'printDesaReport'])->name('bkl.report');
        Route::get('bkr/report/pdf', [BkrPrintController::class, 'printDesaReport'])->name('bkr.report');
        Route::get('paar/report/pdf', [PaarPrintController::class, 'printDesaReport'])->name('paar.report');
        Route::get('koperasi/report/pdf', [KoperasiPrintController::class, 'printDesaReport'])->name('koperasi.report');
        Route::get('data-warga/report/pdf', [DataWargaPrintController::class, 'printDesaReport'])->name('data-warga.report');
        Route::get('data-kegiatan-warga/report/pdf', [DataKegiatanWargaPrintController::class, 'printDesaReport'])->name('data-kegiatan-warga.report');
        Route::get('data-keluarga/report/pdf', [DataKeluargaPrintController::class, 'printDesaReport'])->name('data-keluarga.report');
        Route::get('data-industri-rumah-tangga/report/pdf', [DataIndustriRumahTanggaPrintController::class, 'printDesaReport'])->name('data-industri-rumah-tangga.report');
        Route::get('data-pelatihan-kader/report/pdf', [DataPelatihanKaderPrintController::class, 'printDesaReport'])->name('data-pelatihan-kader.report');
        Route::get('data-pemanfaatan-tanah-pekarangan-hatinya-pkk/report/pdf', [DataPemanfaatanTanahPekaranganHatinyaPkkPrintController::class, 'printDesaReport'])->name('data-pemanfaatan-tanah-pekarangan-hatinya-pkk.report');
        Route::get('catatan-keluarga/report/pdf', [CatatanKeluargaPrintController::class, 'printDesaReport'])->name('catatan-keluarga.report');
        Route::get('catatan-keluarga/rekap-dasa-wisma/report/pdf', [CatatanKeluargaPrintController::class, 'printDesaRekapDasaWismaReport'])->name('catatan-keluarga.rekap-dasa-wisma.report');
        Route::get('catatan-keluarga/rekap-ibu-hamil-dasawisma/report/pdf', [CatatanKeluargaPrintController::class, 'printDesaRekapIbuHamilDasaWismaReport'])->name('catatan-keluarga.rekap-ibu-hamil-dasawisma.report');
        Route::get('catatan-keluarga/rekap-ibu-hamil-pkk-rt/report/pdf', [CatatanKeluargaPrintController::class, 'printDesaRekapIbuHamilPkkRtReport'])->name('catatan-keluarga.rekap-ibu-hamil-pkk-rt.report');
        Route::get('catatan-keluarga/rekap-ibu-hamil-pkk-rw/report/pdf', [CatatanKeluargaPrintController::class, 'printDesaRekapIbuHamilPkkRwReport'])->name('catatan-keluarga.rekap-ibu-hamil-pkk-rw.report');
        Route::get('catatan-keluarga/rekap-ibu-hamil-pkk-dusun-lingkungan/report/pdf', [CatatanKeluargaPrintController::class, 'printDesaRekapIbuHamilPkkDusunLingkunganReport'])->name('catatan-keluarga.rekap-ibu-hamil-pkk-dusun-lingkungan.report');
        Route::get('catatan-keluarga/rekap-ibu-hamil-tp-pkk-kecamatan/report/pdf', [CatatanKeluargaPrintController::class, 'printDesaRekapIbuHamilTpPkkKecamatanReport'])->name('catatan-keluarga.rekap-ibu-hamil-tp-pkk-kecamatan.report');
        Route::get('catatan-keluarga/data-umum-pkk/report/pdf', [CatatanKeluargaPrintController::class, 'printDesaDataUmumPkkReport'])->name('catatan-keluarga.data-umum-pkk.report');
        Route::get('catatan-keluarga/data-umum-pkk-kecamatan/report/pdf', [CatatanKeluargaPrintController::class, 'printDesaDataUmumPkkKecamatanReport'])->name('catatan-keluarga.data-umum-pkk-kecamatan.report');
        Route::get('catatan-keluarga/data-kegiatan-pkk-pokja-iii/report/pdf', [CatatanKeluargaPrintController::class, 'printDesaDataKegiatanPkkPokjaIiiReport'])->name('catatan-keluarga.data-kegiatan-pkk-pokja-iii.report');
        Route::get('catatan-keluarga/data-kegiatan-pkk-pokja-iv/report/pdf', [CatatanKeluargaPrintController::class, 'printDesaDataKegiatanPkkPokjaIvReport'])->name('catatan-keluarga.data-kegiatan-pkk-pokja-iv.report');
        Route::get('catatan-keluarga/rekap-pkk-rt/report/pdf', [CatatanKeluargaPrintController::class, 'printDesaRekapPkkRtReport'])->name('catatan-keluarga.rekap-pkk-rt.report');
        Route::get('catatan-keluarga/catatan-pkk-rw/report/pdf', [CatatanKeluargaPrintController::class, 'printDesaCatatanPkkRwReport'])->name('catatan-keluarga.catatan-pkk-rw.report');
        Route::get('catatan-keluarga/rekap-rw/report/pdf', [CatatanKeluargaPrintController::class, 'printDesaRekapRwReport'])->name('catatan-keluarga.rekap-rw.report');
        Route::get('catatan-keluarga/tp-pkk-desa-kelurahan/report/pdf', [CatatanKeluargaPrintController::class, 'printDesaCatatanTpPkkDesaKelurahanReport'])->name('catatan-keluarga.tp-pkk-desa-kelurahan.report');
        Route::get('catatan-keluarga/tp-pkk-kecamatan/report/pdf', [CatatanKeluargaPrintController::class, 'printDesaCatatanTpPkkKecamatanReport'])->name('catatan-keluarga.tp-pkk-kecamatan.report');
        Route::get('catatan-keluarga/tp-pkk-kabupaten-kota/report/pdf', [CatatanKeluargaPrintController::class, 'printDesaCatatanTpPkkKabupatenKotaReport'])->name('catatan-keluarga.tp-pkk-kabupaten-kota.report');
        Route::get('catatan-keluarga/tp-pkk-provinsi/report/pdf', [CatatanKeluargaPrintController::class, 'printDesaCatatanTpPkkProvinsiReport'])->name('catatan-keluarga.tp-pkk-provinsi.report');
        Route::get('warung-pkk/report/pdf', [WarungPkkPrintController::class, 'printDesaReport'])->name('warung-pkk.report');
        Route::get('taman-bacaan/report/pdf', [TamanBacaanPrintController::class, 'printDesaReport'])->name('taman-bacaan.report');
        Route::get('kejar-paket/report/pdf', [KejarPaketPrintController::class, 'printDesaReport'])->name('kejar-paket.report');
        Route::get('posyandu/report/pdf', [PosyanduPrintController::class, 'printDesaReport'])->name('posyandu.report');
        Route::get('simulasi-penyuluhan/report/pdf', [SimulasiPenyuluhanPrintController::class, 'printDesaReport'])->name('simulasi-penyuluhan.report');
        Route::get('program-prioritas/report/pdf', [ProgramPrioritasPrintController::class, 'printDesaReport'])->name('program-prioritas.report');
        Route::get('pilot-project-keluarga-sehat/report/pdf', [PilotProjectKeluargaSehatPrintController::class, 'printDesaReport'])->name('pilot-project-keluarga-sehat.report');
        Route::get('pilot-project-naskah-pelaporan/report/pdf', [PilotProjectNaskahPelaporanPrintController::class, 'printDesaReport'])->name('pilot-project-naskah-pelaporan.report');
        Route::get('laporan-tahunan-pkk/{id}/print/docx', [LaporanTahunanPkkPrintController::class, 'printDesaReport'])->name('laporan-tahunan-pkk.print');
    });

Route::prefix('kecamatan')
    ->name('kecamatan.')
    ->middleware(['auth', 'scope.role:kecamatan', 'module.visibility'])
    ->group(function () {

        Route::resource('activities', KecamatanActivityController::class);
        Route::get('activities/{id}/attachments/{type}', [KecamatanActivityController::class, 'attachment'])
            ->whereIn('type', ['image', 'document'])
            ->name('activities.attachments.show');
        Route::resource('agenda-surat', KecamatanAgendaSuratController::class);
        Route::get('agenda-surat/{id}/attachment/data-dukung', [KecamatanAgendaSuratController::class, 'attachment'])
            ->name('agenda-surat.attachments.show');
        Route::resource('buku-daftar-hadir', KecamatanBukuDaftarHadirController::class);
        Route::resource('buku-tamu', KecamatanBukuTamuController::class);
        Route::resource('buku-notulen-rapat', KecamatanBukuNotulenRapatController::class);
        Route::resource('inventaris', KecamatanInventarisController::class);
        Route::resource('bantuans', KecamatanBantuanController::class);
        Route::resource('buku-keuangan', KecamatanBukuKeuanganController::class);
        Route::resource('anggota-pokja', KecamatanAnggotaPokjaController::class);
        Route::resource('anggota-tim-penggerak', KecamatanAnggotaTimPenggerakController::class);
        Route::resource('kader-khusus', KecamatanKaderKhususController::class);
        Route::resource('prestasi-lomba', KecamatanPrestasiLombaController::class);
        Route::resource('bkl', KecamatanBklController::class);
        Route::resource('bkr', KecamatanBkrController::class);
        Route::resource('paar', KecamatanPaarController::class);
        Route::resource('koperasi', KecamatanKoperasiController::class);
        Route::resource('data-warga', KecamatanDataWargaController::class);
        Route::resource('data-kegiatan-warga', KecamatanDataKegiatanWargaController::class);
        Route::resource('data-keluarga', KecamatanDataKeluargaController::class);
        Route::resource('data-industri-rumah-tangga', KecamatanDataIndustriRumahTanggaController::class);
        Route::resource('data-pelatihan-kader', KecamatanDataPelatihanKaderController::class);
        Route::resource('data-pemanfaatan-tanah-pekarangan-hatinya-pkk', KecamatanDataPemanfaatanTanahPekaranganHatinyaPkkController::class)
            ->parameters(['data-pemanfaatan-tanah-pekarangan-hatinya-pkk' => 'dataPemanfaatan']);
        Route::get('catatan-keluarga', [KecamatanCatatanKeluargaController::class, 'index'])->name('catatan-keluarga.index');
        Route::resource('warung-pkk', KecamatanWarungPkkController::class);
        Route::resource('taman-bacaan', KecamatanTamanBacaanController::class);
        Route::resource('kejar-paket', KecamatanKejarPaketController::class);
        Route::resource('posyandu', KecamatanPosyanduController::class);
        Route::resource('simulasi-penyuluhan', KecamatanSimulasiPenyuluhanController::class);
        Route::resource('program-prioritas', KecamatanProgramPrioritasController::class);
        Route::resource('pilot-project-keluarga-sehat', KecamatanPilotProjectKeluargaSehatController::class);
        Route::resource('pilot-project-naskah-pelaporan', KecamatanPilotProjectNaskahPelaporanController::class);
        Route::resource('laporan-tahunan-pkk', KecamatanLaporanTahunanPkkController::class);
        Route::get('activities/{id}/print', [ActivityPrintController::class, 'printKecamatan'])->name('activities.print');
        Route::get('activities/report/pdf', [ActivityPrintController::class, 'printKecamatanReport'])->name('activities.report');
        Route::get('buku-notulen-rapat/report/pdf', [BukuNotulenRapatPrintController::class, 'printKecamatanReport'])->name('buku-notulen-rapat.report');
        Route::get('buku-daftar-hadir/report/pdf', [BukuDaftarHadirPrintController::class, 'printKecamatanReport'])->name('buku-daftar-hadir.report');
        Route::get('buku-tamu/report/pdf', [BukuTamuPrintController::class, 'printKecamatanReport'])->name('buku-tamu.report');
        Route::get('agenda-surat/report/pdf', [AgendaSuratReportPrintController::class, 'printKecamatanReport'])->name('agenda-surat.report');
        Route::get('agenda-surat/ekspedisi/report/pdf', [AgendaSuratReportPrintController::class, 'printKecamatanEkspedisiReport'])->name('agenda-surat.ekspedisi.report');
        Route::get('inventaris/report/pdf', [InventarisReportPrintController::class, 'printKecamatanReport'])->name('inventaris.report');
        Route::get('bantuans/report/pdf', [BantuanReportPrintController::class, 'printKecamatanReport'])->name('bantuans.report');
        Route::get('buku-keuangan/report/pdf', [BukuKeuanganReportPrintController::class, 'printKecamatanReport'])->name('buku-keuangan.report');
        Route::get('bantuans/keuangan/report/pdf', [BukuKeuanganReportPrintController::class, 'printKecamatanReport'])->name('bantuans.keuangan.report');
        Route::get('anggota-pokja/report/pdf', [AnggotaPokjaReportPrintController::class, 'printKecamatanReport'])->name('anggota-pokja.report');
        Route::get('anggota-tim-penggerak/report/pdf', [AnggotaTimPenggerakReportPrintController::class, 'printKecamatanReport'])->name('anggota-tim-penggerak.report');
        Route::get('anggota-tim-penggerak-kader/report/pdf', [AnggotaTimPenggerakReportPrintController::class, 'printKecamatanAnggotaDanKaderReport'])->name('anggota-tim-penggerak-kader.report');
        Route::get('kader-khusus/report/pdf', [KaderKhususReportPrintController::class, 'printKecamatanReport'])->name('kader-khusus.report');
        Route::get('prestasi-lomba/report/pdf', [PrestasiLombaPrintController::class, 'printKecamatanReport'])->name('prestasi-lomba.report');
        Route::get('bkl/report/pdf', [BklPrintController::class, 'printKecamatanReport'])->name('bkl.report');
        Route::get('bkr/report/pdf', [BkrPrintController::class, 'printKecamatanReport'])->name('bkr.report');
        Route::get('paar/report/pdf', [PaarPrintController::class, 'printKecamatanReport'])->name('paar.report');
        Route::get('koperasi/report/pdf', [KoperasiPrintController::class, 'printKecamatanReport'])->name('koperasi.report');
        Route::get('data-warga/report/pdf', [DataWargaPrintController::class, 'printKecamatanReport'])->name('data-warga.report');
        Route::get('data-kegiatan-warga/report/pdf', [DataKegiatanWargaPrintController::class, 'printKecamatanReport'])->name('data-kegiatan-warga.report');
        Route::get('data-keluarga/report/pdf', [DataKeluargaPrintController::class, 'printKecamatanReport'])->name('data-keluarga.report');
        Route::get('data-industri-rumah-tangga/report/pdf', [DataIndustriRumahTanggaPrintController::class, 'printKecamatanReport'])->name('data-industri-rumah-tangga.report');
        Route::get('data-pelatihan-kader/report/pdf', [DataPelatihanKaderPrintController::class, 'printKecamatanReport'])->name('data-pelatihan-kader.report');
        Route::get('data-pemanfaatan-tanah-pekarangan-hatinya-pkk/report/pdf', [DataPemanfaatanTanahPekaranganHatinyaPkkPrintController::class, 'printKecamatanReport'])->name('data-pemanfaatan-tanah-pekarangan-hatinya-pkk.report');
        Route::get('catatan-keluarga/report/pdf', [CatatanKeluargaPrintController::class, 'printKecamatanReport'])->name('catatan-keluarga.report');
        Route::get('catatan-keluarga/rekap-dasa-wisma/report/pdf', [CatatanKeluargaPrintController::class, 'printKecamatanRekapDasaWismaReport'])->name('catatan-keluarga.rekap-dasa-wisma.report');
        Route::get('catatan-keluarga/rekap-ibu-hamil-dasawisma/report/pdf', [CatatanKeluargaPrintController::class, 'printKecamatanRekapIbuHamilDasaWismaReport'])->name('catatan-keluarga.rekap-ibu-hamil-dasawisma.report');
        Route::get('catatan-keluarga/rekap-ibu-hamil-pkk-rt/report/pdf', [CatatanKeluargaPrintController::class, 'printKecamatanRekapIbuHamilPkkRtReport'])->name('catatan-keluarga.rekap-ibu-hamil-pkk-rt.report');
        Route::get('catatan-keluarga/rekap-ibu-hamil-pkk-rw/report/pdf', [CatatanKeluargaPrintController::class, 'printKecamatanRekapIbuHamilPkkRwReport'])->name('catatan-keluarga.rekap-ibu-hamil-pkk-rw.report');
        Route::get('catatan-keluarga/rekap-ibu-hamil-pkk-dusun-lingkungan/report/pdf', [CatatanKeluargaPrintController::class, 'printKecamatanRekapIbuHamilPkkDusunLingkunganReport'])->name('catatan-keluarga.rekap-ibu-hamil-pkk-dusun-lingkungan.report');
        Route::get('catatan-keluarga/rekap-ibu-hamil-tp-pkk-kecamatan/report/pdf', [CatatanKeluargaPrintController::class, 'printKecamatanRekapIbuHamilTpPkkKecamatanReport'])->name('catatan-keluarga.rekap-ibu-hamil-tp-pkk-kecamatan.report');
        Route::get('catatan-keluarga/data-umum-pkk/report/pdf', [CatatanKeluargaPrintController::class, 'printKecamatanDataUmumPkkReport'])->name('catatan-keluarga.data-umum-pkk.report');
        Route::get('catatan-keluarga/data-umum-pkk-kecamatan/report/pdf', [CatatanKeluargaPrintController::class, 'printKecamatanDataUmumPkkKecamatanReport'])->name('catatan-keluarga.data-umum-pkk-kecamatan.report');
        Route::get('catatan-keluarga/data-kegiatan-pkk-pokja-iii/report/pdf', [CatatanKeluargaPrintController::class, 'printKecamatanDataKegiatanPkkPokjaIiiReport'])->name('catatan-keluarga.data-kegiatan-pkk-pokja-iii.report');
        Route::get('catatan-keluarga/data-kegiatan-pkk-pokja-iv/report/pdf', [CatatanKeluargaPrintController::class, 'printKecamatanDataKegiatanPkkPokjaIvReport'])->name('catatan-keluarga.data-kegiatan-pkk-pokja-iv.report');
        Route::get('catatan-keluarga/rekap-pkk-rt/report/pdf', [CatatanKeluargaPrintController::class, 'printKecamatanRekapPkkRtReport'])->name('catatan-keluarga.rekap-pkk-rt.report');
        Route::get('catatan-keluarga/catatan-pkk-rw/report/pdf', [CatatanKeluargaPrintController::class, 'printKecamatanCatatanPkkRwReport'])->name('catatan-keluarga.catatan-pkk-rw.report');
        Route::get('catatan-keluarga/rekap-rw/report/pdf', [CatatanKeluargaPrintController::class, 'printKecamatanRekapRwReport'])->name('catatan-keluarga.rekap-rw.report');
        Route::get('catatan-keluarga/tp-pkk-desa-kelurahan/report/pdf', [CatatanKeluargaPrintController::class, 'printKecamatanCatatanTpPkkDesaKelurahanReport'])->name('catatan-keluarga.tp-pkk-desa-kelurahan.report');
        Route::get('catatan-keluarga/tp-pkk-kecamatan/report/pdf', [CatatanKeluargaPrintController::class, 'printKecamatanCatatanTpPkkKecamatanReport'])->name('catatan-keluarga.tp-pkk-kecamatan.report');
        Route::get('catatan-keluarga/tp-pkk-kabupaten-kota/report/pdf', [CatatanKeluargaPrintController::class, 'printKecamatanCatatanTpPkkKabupatenKotaReport'])->name('catatan-keluarga.tp-pkk-kabupaten-kota.report');
        Route::get('catatan-keluarga/tp-pkk-provinsi/report/pdf', [CatatanKeluargaPrintController::class, 'printKecamatanCatatanTpPkkProvinsiReport'])->name('catatan-keluarga.tp-pkk-provinsi.report');
        Route::get('warung-pkk/report/pdf', [WarungPkkPrintController::class, 'printKecamatanReport'])->name('warung-pkk.report');
        Route::get('taman-bacaan/report/pdf', [TamanBacaanPrintController::class, 'printKecamatanReport'])->name('taman-bacaan.report');
        Route::get('kejar-paket/report/pdf', [KejarPaketPrintController::class, 'printKecamatanReport'])->name('kejar-paket.report');
        Route::get('posyandu/report/pdf', [PosyanduPrintController::class, 'printKecamatanReport'])->name('posyandu.report');
        Route::get('simulasi-penyuluhan/report/pdf', [SimulasiPenyuluhanPrintController::class, 'printKecamatanReport'])->name('simulasi-penyuluhan.report');
        Route::get('desa-activities', [KecamatanDesaActivityController::class, 'index'])->name('desa-activities.index');
        Route::get('desa-activities/{id}', [KecamatanDesaActivityController::class, 'show'])->name('desa-activities.show');
        Route::get('desa-activities/{id}/attachments/{type}', [KecamatanDesaActivityController::class, 'attachment'])
            ->whereIn('type', ['image', 'document'])
            ->name('desa-activities.attachments.show');
        Route::get('desa-activities/{id}/print', [ActivityPrintController::class, 'printKecamatanDesa'])->name('desa-activities.print');
        Route::get('desa-arsip', [KecamatanDesaArsipController::class, 'index'])->name('desa-arsip.index');
        Route::get('program-prioritas/report/pdf', [ProgramPrioritasPrintController::class, 'printKecamatanReport'])->name('program-prioritas.report');
        Route::get('pilot-project-keluarga-sehat/report/pdf', [PilotProjectKeluargaSehatPrintController::class, 'printKecamatanReport'])->name('pilot-project-keluarga-sehat.report');
        Route::get('pilot-project-naskah-pelaporan/report/pdf', [PilotProjectNaskahPelaporanPrintController::class, 'printKecamatanReport'])->name('pilot-project-naskah-pelaporan.report');
        Route::get('laporan-tahunan-pkk/{id}/print/docx', [LaporanTahunanPkkPrintController::class, 'printKecamatanReport'])->name('laporan-tahunan-pkk.print');
    });

require __DIR__.'/auth.php';
