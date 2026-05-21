<?php

namespace App\Support;

use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Models\User;

/**
 * RoleScopeMatrix defines the hierarchical permissions and scopes
 * based on the RAKERNAS X PKK organizational structure.
 */
class RoleScopeMatrix
{
    // Administrative Roles
    public const ROLE_SUPER_ADMIN = 'super-admin';
    public const ROLE_ADMIN_PUSAT = 'admin_pusat';
    public const ROLE_ADMIN_PROVINSI = 'admin_provinsi';
    public const ROLE_ADMIN_KABUPATEN = 'admin_kabupaten';
    public const ROLE_ADMIN_KECAMATAN = 'admin_kecamatan';
    public const ROLE_ADMIN_DESA = 'admin_desa';
    public const ROLE_ADMIN_DUSUN = 'admin_dusun';
    public const ROLE_ADMIN_RW = 'admin_rw';
    public const ROLE_ADMIN_RT = 'admin_rt';
    public const ROLE_ADMIN_DASAWISMA = 'admin_dasawisma';

    // Functional Roles (Implementation)
    public const ROLE_SEKRETARIS_KECAMATAN = 'kecamatan-sekretaris';
    public const ROLE_BENDAHARA_KECAMATAN = 'kecamatan-bendahara';
    public const ROLE_POKJA_1_KECAMATAN = 'kecamatan-pokja-i';
    public const ROLE_POKJA_2_KECAMATAN = 'kecamatan-pokja-ii';
    public const ROLE_POKJA_3_KECAMATAN = 'kecamatan-pokja-iii';
    public const ROLE_POKJA_4_KECAMATAN = 'kecamatan-pokja-iv';

    public const ROLE_SEKRETARIS_DESA = 'desa-sekretaris';
    public const ROLE_BENDAHARA_DESA = 'desa-bendahara';
    public const ROLE_POKJA_1_DESA = 'desa-pokja-i';
    public const ROLE_POKJA_2_DESA = 'desa-pokja-ii';
    public const ROLE_POKJA_3_DESA = 'desa-pokja-iii';
    public const ROLE_POKJA_4_DESA = 'desa-pokja-iv';

    /**
     * Permission Matrix mapping roles to their capabilities.
     * Permissions are structured as: [domain].[action]
     */
    private const PERMISSIONS = [
        self::ROLE_SUPER_ADMIN => ['*'],

        self::ROLE_ADMIN_PUSAT => [
            'arsip_document.view', 'arsip_document.create', 'arsip_document.update', 'arsip_document.delete', 'arsip_document.export',
            'activities.view', 'activities.print',
        ],

        self::ROLE_ADMIN_KECAMATAN => [
            'arsip_document.view', 'arsip_document.create', 'arsip_document.update', 'arsip_document.delete', 'arsip_document.export',
            'activities.view', 'activities.create', 'activities.update', 'activities.delete', 'activities.print',
            'agenda_surat.view', 'agenda_surat.create', 'agenda_surat.update', 'agenda_surat.delete', 'agenda_surat.print',
            'anggota_pokja.view', 'anggota_pokja.create', 'anggota_pokja.update', 'anggota_pokja.delete', 'anggota_pokja.print',
            'inventaris.view', 'inventaris.create', 'inventaris.update', 'inventaris.delete', 'inventaris.print',
            'bantuan.view', 'bantuan.create', 'bantuan.update', 'bantuan.delete', 'bantuan.print',
            'kader_khusus.view', 'kader_khusus.create', 'kader_khusus.update', 'kader_khusus.delete', 'kader_khusus.print',
            'prestasi_lomba.view', 'prestasi_lomba.create', 'prestasi_lomba.update', 'prestasi_lomba.delete', 'prestasi_lomba.print',
            'program_prioritas.view', 'program_prioritas.create', 'program_prioritas.update', 'program_prioritas.delete', 'program_prioritas.print',
            'anggota_tim_penggerak.view', 'anggota_tim_penggerak.create', 'anggota_tim_penggerak.update', 'anggota_tim_penggerak.delete', 'anggota_tim_penggerak.print',
            'buku_daftar_hadir.view', 'buku_daftar_hadir.create', 'buku_daftar_hadir.update', 'buku_daftar_hadir.delete', 'buku_daftar_hadir.print',
            'buku_notulen_rapat.view', 'buku_notulen_rapat.create', 'buku_notulen_rapat.update', 'buku_notulen_rapat.delete', 'buku_notulen_rapat.print',
            'buku_ekspedisi.view', 'buku_ekspedisi.create', 'buku_ekspedisi.update', 'buku_ekspedisi.delete', 'buku_ekspedisi.print',
            'buku_tamu.view', 'buku_tamu.create', 'buku_tamu.update', 'buku_tamu.delete', 'buku_tamu.print',
            'laporan_tahunan_pkk.view', 'laporan_tahunan_pkk.create', 'laporan_tahunan_pkk.update', 'laporan_tahunan_pkk.delete', 'laporan_tahunan_pkk.print',
            'data_warga.view', 'data_warga.create', 'data_warga.update', 'data_warga.delete', 'data_warga.print',
            'data_kegiatan_warga.view', 'data_kegiatan_warga.create', 'data_kegiatan_warga.update', 'data_kegiatan_warga.delete', 'data_kegiatan_warga.print',
            'data_keluarga.view', 'data_keluarga.create', 'data_keluarga.update', 'data_keluarga.delete', 'data_keluarga.print',
            'catatan_keluarga.view', 'catatan_keluarga.create', 'catatan_keluarga.update', 'catatan_keluarga.delete', 'catatan_keluarga.print',
            'buku_keuangan.view', 'buku_keuangan.create', 'buku_keuangan.update', 'buku_keuangan.delete', 'buku_keuangan.print',
            'simulasi_penyuluhan.view', 'simulasi_penyuluhan.create', 'simulasi_penyuluhan.update', 'simulasi_penyuluhan.delete', 'simulasi_penyuluhan.print',
            'bkr.view', 'bkr.create', 'bkr.update', 'bkr.delete', 'bkr.print',
            'paar.view', 'paar.create', 'paar.update', 'paar.delete', 'paar.print',
            'bkl.view', 'bkl.create', 'bkl.update', 'bkl.delete', 'bkl.print',
            'literasi_warga.view', 'literasi_warga.create', 'literasi_warga.update', 'literasi_warga.delete', 'literasi_warga.print',
            'pra_koperasi_up2k.view', 'pra_koperasi_up2k.create', 'pra_koperasi_up2k.update', 'pra_koperasi_up2k.delete', 'pra_koperasi_up2k.print',
            'koperasi.view', 'koperasi.create', 'koperasi.update', 'koperasi.delete', 'koperasi.print',
            'kejar_paket.view', 'kejar_paket.create', 'kejar_paket.update', 'kejar_paket.delete', 'kejar_paket.print',
            'taman_bacaan.view', 'taman_bacaan.create', 'taman_bacaan.update', 'taman_bacaan.delete', 'taman_bacaan.print',
            'pelatihan_kader_pokja_ii.view', 'pelatihan_kader_pokja_ii.create', 'pelatihan_kader_pokja_ii.update', 'pelatihan_kader_pokja_ii.delete', 'pelatihan_kader_pokja_ii.print',
            'warung_pkk.view', 'warung_pkk.create', 'warung_pkk.update', 'warung_pkk.delete', 'warung_pkk.print',
            'tutor_khusus.view', 'tutor_khusus.create', 'tutor_khusus.update', 'tutor_khusus.delete', 'tutor_khusus.print',
            'data_pelatihan_kader.view', 'data_pelatihan_kader.create', 'data_pelatihan_kader.update', 'data_pelatihan_kader.delete', 'data_pelatihan_kader.print',
            'data_pemanfaatan_tanah_pekarangan_hatinya_pkk.view', 'data_pemanfaatan_tanah_pekarangan_hatinya_pkk.create', 'data_pemanfaatan_tanah_pekarangan_hatinya_pkk.update', 'data_pemanfaatan_tanah_pekarangan_hatinya_pkk.delete', 'data_pemanfaatan_tanah_pekarangan_hatinya_pkk.print',
            'data_industri_rumah_tangga.view', 'data_industri_rumah_tangga.create', 'data_industri_rumah_tangga.update', 'data_industri_rumah_tangga.delete', 'data_industri_rumah_tangga.print',
            'posyandu.view', 'posyandu.create', 'posyandu.update', 'posyandu.delete', 'posyandu.print',
            'bkb_kegiatan.view', 'bkb_kegiatan.create', 'bkb_kegiatan.update', 'bkb_kegiatan.delete', 'bkb_kegiatan.print',
            'pilot_project_naskah_pelaporan.view', 'pilot_project_naskah_pelaporan.create', 'pilot_project_naskah_pelaporan.update', 'pilot_project_naskah_pelaporan.delete', 'pilot_project_naskah_pelaporan.print',
            'pilot_project_keluarga_sehat.view', 'pilot_project_keluarga_sehat.create', 'pilot_project_keluarga_sehat.update', 'pilot_project_keluarga_sehat.delete', 'pilot_project_keluarga_sehat.print',
        ],

        self::ROLE_SEKRETARIS_KECAMATAN => [
            'arsip_document.view', 'activities.view', 'agenda_surat.view', 'anggota_pokja.view', 'inventaris.view', 'bantuan.view', 'kader_khusus.view', 'prestasi_lomba.view', 'program_prioritas.view', 'anggota_tim_penggerak.view', 'buku_daftar_hadir.view', 'buku_notulen_rapat.view', 'buku_tamu.view', 'laporan_tahunan_pkk.view', 'data_warga.view', 'data_kegiatan_warga.view', 'data_keluarga.view', 'catatan_keluarga.view', 'buku_keuangan.view', 'simulasi_penyuluhan.view', 'bkr.view', 'paar.view', 'bkl.view', 'literasi_warga.view', 'pra_koperasi_up2k.view', 'koperasi.view', 'kejar_paket.view', 'taman_bacaan.view', 'pelatihan_kader_pokja_ii.view', 'warung_pkk.view', 'tutor_khusus.view', 'data_pelatihan_kader.view', 'data_pemanfaatan_tanah_pekarangan_hatinya_pkk.view', 'data_industri_rumah_tangga.view', 'posyandu.view', 'bkb_kegiatan.view', 'pilot_project_naskah_pelaporan.view', 'pilot_project_keluarga_sehat.view',
            'arsip_document.view', 'arsip_document.create', 'arsip_document.update', 'arsip_document.delete', 'arsip_document.export',
            'activities.view', 'activities.create', 'activities.update', 'activities.delete', 'activities.print',
            'anggota_pokja.view', 'anggota_pokja.create', 'anggota_pokja.update', 'anggota_pokja.delete', 'anggota_pokja.print',
            'agenda_surat.view', 'agenda_surat.create', 'agenda_surat.update', 'agenda_surat.delete', 'agenda_surat.print',
            'inventaris.view', 'inventaris.create', 'inventaris.update', 'inventaris.delete', 'inventaris.print',
            'bantuan.view', 'bantuan.create', 'bantuan.update', 'bantuan.delete', 'bantuan.print',
            'kader_khusus.view', 'kader_khusus.create', 'kader_khusus.update', 'kader_khusus.delete', 'kader_khusus.print',
            'prestasi_lomba.view', 'prestasi_lomba.create', 'prestasi_lomba.update', 'prestasi_lomba.delete', 'prestasi_lomba.print',
            'program_prioritas.view', 'program_prioritas.create', 'program_prioritas.update', 'program_prioritas.delete', 'program_prioritas.print',
            'anggota_tim_penggerak.view', 'anggota_tim_penggerak.create', 'anggota_tim_penggerak.update', 'anggota_tim_penggerak.delete', 'anggota_tim_penggerak.print',
            'buku_daftar_hadir.view', 'buku_daftar_hadir.create', 'buku_daftar_hadir.update', 'buku_daftar_hadir.delete', 'buku_daftar_hadir.print',
            'buku_notulen_rapat.view', 'buku_notulen_rapat.create', 'buku_notulen_rapat.update', 'buku_notulen_rapat.delete', 'buku_notulen_rapat.print',
            'buku_ekspedisi.view', 'buku_ekspedisi.create', 'buku_ekspedisi.update', 'buku_ekspedisi.delete', 'buku_ekspedisi.print',
            'buku_tamu.view', 'buku_tamu.create', 'buku_tamu.update', 'buku_tamu.delete', 'buku_tamu.print',
            'laporan_tahunan_pkk.view', 'laporan_tahunan_pkk.create', 'laporan_tahunan_pkk.update', 'laporan_tahunan_pkk.delete', 'laporan_tahunan_pkk.print',
            'data_warga.view', 'data_warga.create', 'data_warga.update', 'data_warga.delete', 'data_warga.print',
            'data_kegiatan_warga.view', 'data_kegiatan_warga.create', 'data_kegiatan_warga.update', 'data_kegiatan_warga.delete', 'data_kegiatan_warga.print',
            'data_keluarga.view', 'data_keluarga.create', 'data_keluarga.update', 'data_keluarga.delete', 'data_keluarga.print',
            'catatan_keluarga.view', 'catatan_keluarga.create', 'catatan_keluarga.update', 'catatan_keluarga.delete', 'catatan_keluarga.print',
        ],

        self::ROLE_BENDAHARA_KECAMATAN => [
            'arsip_document.view', 'activities.view', 'agenda_surat.view', 'anggota_pokja.view', 'inventaris.view', 'bantuan.view', 'kader_khusus.view', 'prestasi_lomba.view', 'program_prioritas.view', 'anggota_tim_penggerak.view', 'buku_daftar_hadir.view', 'buku_notulen_rapat.view', 'buku_tamu.view', 'laporan_tahunan_pkk.view', 'data_warga.view', 'data_kegiatan_warga.view', 'data_keluarga.view', 'catatan_keluarga.view', 'buku_keuangan.view', 'simulasi_penyuluhan.view', 'bkr.view', 'paar.view', 'bkl.view', 'literasi_warga.view', 'pra_koperasi_up2k.view', 'koperasi.view', 'kejar_paket.view', 'taman_bacaan.view', 'pelatihan_kader_pokja_ii.view', 'warung_pkk.view', 'tutor_khusus.view', 'data_pelatihan_kader.view', 'data_pemanfaatan_tanah_pekarangan_hatinya_pkk.view', 'data_industri_rumah_tangga.view', 'posyandu.view', 'bkb_kegiatan.view', 'pilot_project_naskah_pelaporan.view', 'pilot_project_keluarga_sehat.view',
            'activities.view', 'activities.create', 'activities.update', 'activities.delete', 'activities.print',
            'buku_keuangan.view', 'buku_keuangan.create', 'buku_keuangan.update', 'buku_keuangan.delete', 'buku_keuangan.print',
            'data_warga.view', 'data_keluarga.view', 'data_kegiatan_warga.view', 'catatan_keluarga.view',
        ],

        self::ROLE_POKJA_1_KECAMATAN => [
            'arsip_document.view', 'activities.view', 'agenda_surat.view', 'anggota_pokja.view', 'inventaris.view', 'bantuan.view', 'kader_khusus.view', 'prestasi_lomba.view', 'program_prioritas.view', 'anggota_tim_penggerak.view', 'buku_daftar_hadir.view', 'buku_notulen_rapat.view', 'buku_tamu.view', 'laporan_tahunan_pkk.view', 'data_warga.view', 'data_kegiatan_warga.view', 'data_keluarga.view', 'catatan_keluarga.view', 'buku_keuangan.view', 'simulasi_penyuluhan.view', 'bkr.view', 'paar.view', 'bkl.view', 'literasi_warga.view', 'pra_koperasi_up2k.view', 'koperasi.view', 'kejar_paket.view', 'taman_bacaan.view', 'pelatihan_kader_pokja_ii.view', 'warung_pkk.view', 'tutor_khusus.view', 'data_pelatihan_kader.view', 'data_pemanfaatan_tanah_pekarangan_hatinya_pkk.view', 'data_industri_rumah_tangga.view', 'posyandu.view', 'bkb_kegiatan.view', 'pilot_project_naskah_pelaporan.view', 'pilot_project_keluarga_sehat.view',
            'activities.view', 'activities.create', 'activities.update', 'activities.delete', 'activities.print',
            'anggota_pokja.view', 'anggota_pokja.create', 'anggota_pokja.update', 'anggota_pokja.delete', 'anggota_pokja.print',
            'program_prioritas.view', 'program_prioritas.create', 'program_prioritas.update', 'program_prioritas.delete', 'program_prioritas.print',
            'inventaris.view', 'inventaris.create', 'inventaris.update', 'inventaris.delete', 'inventaris.print',
            'bantuan.view', 'bantuan.create', 'bantuan.update', 'bantuan.delete', 'bantuan.print',
            'kader_khusus.view', 'kader_khusus.create', 'kader_khusus.update', 'kader_khusus.delete', 'kader_khusus.print',
            'prestasi_lomba.view', 'prestasi_lomba.create', 'prestasi_lomba.update', 'prestasi_lomba.delete', 'prestasi_lomba.print',
            'simulasi_penyuluhan.view', 'simulasi_penyuluhan.create', 'simulasi_penyuluhan.update', 'simulasi_penyuluhan.delete', 'simulasi_penyuluhan.print',
            'bkr.view', 'bkr.create', 'bkr.update', 'bkr.delete', 'bkr.print',
            'paar.view', 'paar.create', 'paar.update', 'paar.delete', 'paar.print',
            'bkl.view', 'bkl.create', 'bkl.update', 'bkl.delete', 'bkl.print',
            'literasi_warga.view', 'literasi_warga.create', 'literasi_warga.update', 'literasi_warga.delete', 'literasi_warga.print',
            'data_warga.view', 'data_keluarga.view', 'data_kegiatan_warga.view', 'catatan_keluarga.view',
        ],

        self::ROLE_POKJA_2_KECAMATAN => [
            'arsip_document.view', 'activities.view', 'agenda_surat.view', 'anggota_pokja.view', 'inventaris.view', 'bantuan.view', 'kader_khusus.view', 'prestasi_lomba.view', 'program_prioritas.view', 'anggota_tim_penggerak.view', 'buku_daftar_hadir.view', 'buku_notulen_rapat.view', 'buku_tamu.view', 'laporan_tahunan_pkk.view', 'data_warga.view', 'data_kegiatan_warga.view', 'data_keluarga.view', 'catatan_keluarga.view', 'buku_keuangan.view', 'simulasi_penyuluhan.view', 'bkr.view', 'paar.view', 'bkl.view', 'literasi_warga.view', 'pra_koperasi_up2k.view', 'koperasi.view', 'kejar_paket.view', 'taman_bacaan.view', 'pelatihan_kader_pokja_ii.view', 'warung_pkk.view', 'tutor_khusus.view', 'data_pelatihan_kader.view', 'data_pemanfaatan_tanah_pekarangan_hatinya_pkk.view', 'data_industri_rumah_tangga.view', 'posyandu.view', 'bkb_kegiatan.view', 'pilot_project_naskah_pelaporan.view', 'pilot_project_keluarga_sehat.view',
            'activities.view', 'activities.create', 'activities.update', 'activities.delete', 'activities.print',
            'program_prioritas.view', 'program_prioritas.create', 'program_prioritas.update', 'program_prioritas.delete', 'program_prioritas.print',
            'inventaris.view', 'inventaris.create', 'inventaris.update', 'inventaris.delete', 'inventaris.print',
            'bantuan.view', 'bantuan.create', 'bantuan.update', 'bantuan.delete', 'bantuan.print',
            'kader_khusus.view', 'kader_khusus.create', 'kader_khusus.update', 'kader_khusus.delete', 'kader_khusus.print',
            'prestasi_lomba.view', 'prestasi_lomba.create', 'prestasi_lomba.update', 'prestasi_lomba.delete', 'prestasi_lomba.print',
            'pra_koperasi_up2k.view', 'pra_koperasi_up2k.create', 'pra_koperasi_up2k.update', 'pra_koperasi_up2k.delete', 'pra_koperasi_up2k.print',
            'koperasi.view', 'koperasi.create', 'koperasi.update', 'koperasi.delete', 'koperasi.print',
            'kejar_paket.view', 'kejar_paket.create', 'kejar_paket.update', 'kejar_paket.delete', 'kejar_paket.print',
            'taman_bacaan.view', 'taman_bacaan.create', 'taman_bacaan.update', 'taman_bacaan.delete', 'taman_bacaan.print',
            'pelatihan_kader_pokja_ii.view', 'pelatihan_kader_pokja_ii.create', 'pelatihan_kader_pokja_ii.update', 'pelatihan_kader_pokja_ii.delete', 'pelatihan_kader_pokja_ii.print',
            'warung_pkk.view', 'warung_pkk.create', 'warung_pkk.update', 'warung_pkk.delete', 'warung_pkk.print',
            'tutor_khusus.view', 'tutor_khusus.create', 'tutor_khusus.update', 'tutor_khusus.delete', 'tutor_khusus.print',
            'data_pelatihan_kader.view', 'data_pelatihan_kader.create', 'data_pelatihan_kader.update', 'data_pelatihan_kader.delete', 'data_pelatihan_kader.print',
            'data_warga.view', 'data_keluarga.view', 'data_kegiatan_warga.view', 'catatan_keluarga.view',
        ],

        self::ROLE_POKJA_3_KECAMATAN => [
            'arsip_document.view', 'activities.view', 'agenda_surat.view', 'anggota_pokja.view', 'inventaris.view', 'bantuan.view', 'kader_khusus.view', 'prestasi_lomba.view', 'program_prioritas.view', 'anggota_tim_penggerak.view', 'buku_daftar_hadir.view', 'buku_notulen_rapat.view', 'buku_tamu.view', 'laporan_tahunan_pkk.view', 'data_warga.view', 'data_kegiatan_warga.view', 'data_keluarga.view', 'catatan_keluarga.view', 'buku_keuangan.view', 'simulasi_penyuluhan.view', 'bkr.view', 'paar.view', 'bkl.view', 'literasi_warga.view', 'pra_koperasi_up2k.view', 'koperasi.view', 'kejar_paket.view', 'taman_bacaan.view', 'pelatihan_kader_pokja_ii.view', 'warung_pkk.view', 'tutor_khusus.view', 'data_pelatihan_kader.view', 'data_pemanfaatan_tanah_pekarangan_hatinya_pkk.view', 'data_industri_rumah_tangga.view', 'posyandu.view', 'bkb_kegiatan.view', 'pilot_project_naskah_pelaporan.view', 'pilot_project_keluarga_sehat.view',
            'activities.view', 'activities.create', 'activities.update', 'activities.delete', 'activities.print',
            'program_prioritas.view', 'program_prioritas.create', 'program_prioritas.update', 'program_prioritas.delete', 'program_prioritas.print',
            'inventaris.view', 'inventaris.create', 'inventaris.update', 'inventaris.delete', 'inventaris.print',
            'bantuan.view', 'bantuan.create', 'bantuan.update', 'bantuan.delete', 'bantuan.print',
            'kader_khusus.view', 'kader_khusus.create', 'kader_khusus.update', 'kader_khusus.delete', 'kader_khusus.print',
            'prestasi_lomba.view', 'prestasi_lomba.create', 'prestasi_lomba.update', 'prestasi_lomba.delete', 'prestasi_lomba.print',
            'data_keluarga.view', 'data_keluarga.create', 'data_keluarga.update', 'data_keluarga.delete', 'data_keluarga.print',
            'data_pemanfaatan_tanah_pekarangan_hatinya_pkk.view', 'data_pemanfaatan_tanah_pekarangan_hatinya_pkk.create', 'data_pemanfaatan_tanah_pekarangan_hatinya_pkk.update', 'data_pemanfaatan_tanah_pekarangan_hatinya_pkk.delete', 'data_pemanfaatan_tanah_pekarangan_hatinya_pkk.print',
            'data_industri_rumah_tangga.view', 'data_industri_rumah_tangga.create', 'data_industri_rumah_tangga.update', 'data_industri_rumah_tangga.delete', 'data_industri_rumah_tangga.print',
            'data_warga.view', 'data_keluarga.view', 'data_kegiatan_warga.view', 'catatan_keluarga.view',
        ],

        self::ROLE_POKJA_4_KECAMATAN => [
            'arsip_document.view', 'activities.view', 'agenda_surat.view', 'anggota_pokja.view', 'inventaris.view', 'bantuan.view', 'kader_khusus.view', 'prestasi_lomba.view', 'program_prioritas.view', 'anggota_tim_penggerak.view', 'buku_daftar_hadir.view', 'buku_notulen_rapat.view', 'buku_tamu.view', 'laporan_tahunan_pkk.view', 'data_warga.view', 'data_kegiatan_warga.view', 'data_keluarga.view', 'catatan_keluarga.view', 'buku_keuangan.view', 'simulasi_penyuluhan.view', 'bkr.view', 'paar.view', 'bkl.view', 'literasi_warga.view', 'pra_koperasi_up2k.view', 'koperasi.view', 'kejar_paket.view', 'taman_bacaan.view', 'pelatihan_kader_pokja_ii.view', 'warung_pkk.view', 'tutor_khusus.view', 'data_pelatihan_kader.view', 'data_pemanfaatan_tanah_pekarangan_hatinya_pkk.view', 'data_industri_rumah_tangga.view', 'posyandu.view', 'bkb_kegiatan.view', 'pilot_project_naskah_pelaporan.view', 'pilot_project_keluarga_sehat.view',
            'activities.view', 'activities.create', 'activities.update', 'activities.delete', 'activities.print',
            'program_prioritas.view', 'program_prioritas.create', 'program_prioritas.update', 'program_prioritas.delete', 'program_prioritas.print',
            'inventaris.view', 'inventaris.create', 'inventaris.update', 'inventaris.delete', 'inventaris.print',
            'bantuan.view', 'bantuan.create', 'bantuan.update', 'bantuan.delete', 'bantuan.print',
            'kader_khusus.view', 'kader_khusus.create', 'kader_khusus.update', 'kader_khusus.delete', 'kader_khusus.print',
            'prestasi_lomba.view', 'prestasi_lomba.create', 'prestasi_lomba.update', 'prestasi_lomba.delete', 'prestasi_lomba.print',
            'posyandu.view', 'posyandu.create', 'posyandu.update', 'posyandu.delete', 'posyandu.print',
            'bkb_kegiatan.view', 'bkb_kegiatan.create', 'bkb_kegiatan.update', 'bkb_kegiatan.delete', 'bkb_kegiatan.print',
            'pilot_project_naskah_pelaporan.view', 'pilot_project_naskah_pelaporan.create', 'pilot_project_naskah_pelaporan.update', 'pilot_project_naskah_pelaporan.delete', 'pilot_project_naskah_pelaporan.print',
            'pilot_project_keluarga_sehat.view', 'pilot_project_keluarga_sehat.create', 'pilot_project_keluarga_sehat.update', 'pilot_project_keluarga_sehat.delete', 'pilot_project_keluarga_sehat.print',
            'data_warga.view', 'data_keluarga.view', 'data_kegiatan_warga.view', 'catatan_keluarga.view',
        ],

        self::ROLE_ADMIN_DESA => [
            'arsip_document.view', 'arsip_document.create', 'arsip_document.update', 'arsip_document.delete', 'arsip_document.export',
            'activities.view', 'activities.create', 'activities.update', 'activities.delete', 'activities.print',
            'agenda_surat.view', 'agenda_surat.create', 'agenda_surat.update', 'agenda_surat.delete', 'agenda_surat.print',
            'anggota_pokja.view', 'anggota_pokja.create', 'anggota_pokja.update', 'anggota_pokja.delete', 'anggota_pokja.print',
            'inventaris.view', 'inventaris.create', 'inventaris.update', 'inventaris.delete', 'inventaris.print',
            'bantuan.view', 'bantuan.create', 'bantuan.update', 'bantuan.delete', 'bantuan.print',
            'kader_khusus.view', 'kader_khusus.create', 'kader_khusus.update', 'kader_khusus.delete', 'kader_khusus.print',
            'prestasi_lomba.view', 'prestasi_lomba.create', 'prestasi_lomba.update', 'prestasi_lomba.delete', 'prestasi_lomba.print',
            'program_prioritas.view', 'program_prioritas.create', 'program_prioritas.update', 'program_prioritas.delete', 'program_prioritas.print',
            'anggota_tim_penggerak.view', 'anggota_tim_penggerak.create', 'anggota_tim_penggerak.update', 'anggota_tim_penggerak.delete', 'anggota_tim_penggerak.print',
            'buku_daftar_hadir.view', 'buku_daftar_hadir.create', 'buku_daftar_hadir.update', 'buku_daftar_hadir.delete', 'buku_daftar_hadir.print',
            'buku_notulen_rapat.view', 'buku_notulen_rapat.create', 'buku_notulen_rapat.update', 'buku_notulen_rapat.delete', 'buku_notulen_rapat.print',
            'buku_ekspedisi.view', 'buku_ekspedisi.create', 'buku_ekspedisi.update', 'buku_ekspedisi.delete', 'buku_ekspedisi.print',
            'buku_tamu.view', 'buku_tamu.create', 'buku_tamu.update', 'buku_tamu.delete', 'buku_tamu.print',
            'laporan_tahunan_pkk.view', 'laporan_tahunan_pkk.create', 'laporan_tahunan_pkk.update', 'laporan_tahunan_pkk.delete', 'laporan_tahunan_pkk.print',
            'data_warga.view', 'data_warga.create', 'data_warga.update', 'data_warga.delete', 'data_warga.print',
            'data_kegiatan_warga.view', 'data_kegiatan_warga.create', 'data_kegiatan_warga.update', 'data_kegiatan_warga.delete', 'data_kegiatan_warga.print',
            'data_keluarga.view', 'data_keluarga.create', 'data_keluarga.update', 'data_keluarga.delete', 'data_keluarga.print',
            'catatan_keluarga.view', 'catatan_keluarga.create', 'catatan_keluarga.update', 'catatan_keluarga.delete', 'catatan_keluarga.print',
            'buku_keuangan.view', 'buku_keuangan.create', 'buku_keuangan.update', 'buku_keuangan.delete', 'buku_keuangan.print',
            'simulasi_penyuluhan.view', 'simulasi_penyuluhan.create', 'simulasi_penyuluhan.update', 'simulasi_penyuluhan.delete', 'simulasi_penyuluhan.print',
            'bkr.view', 'bkr.create', 'bkr.update', 'bkr.delete', 'bkr.print',
            'paar.view', 'paar.create', 'paar.update', 'paar.delete', 'paar.print',
            'bkl.view', 'bkl.create', 'bkl.update', 'bkl.delete', 'bkl.print',
            'literasi_warga.view', 'literasi_warga.create', 'literasi_warga.update', 'literasi_warga.delete', 'literasi_warga.print',
            'pra_koperasi_up2k.view', 'pra_koperasi_up2k.create', 'pra_koperasi_up2k.update', 'pra_koperasi_up2k.delete', 'pra_koperasi_up2k.print',
            'koperasi.view', 'koperasi.create', 'koperasi.update', 'koperasi.delete', 'koperasi.print',
            'kejar_paket.view', 'kejar_paket.create', 'kejar_paket.update', 'kejar_paket.delete', 'kejar_paket.print',
            'taman_bacaan.view', 'taman_bacaan.create', 'taman_bacaan.update', 'taman_bacaan.delete', 'taman_bacaan.print',
            'pelatihan_kader_pokja_ii.view', 'pelatihan_kader_pokja_ii.create', 'pelatihan_kader_pokja_ii.update', 'pelatihan_kader_pokja_ii.delete', 'pelatihan_kader_pokja_ii.print',
            'warung_pkk.view', 'warung_pkk.create', 'warung_pkk.update', 'warung_pkk.delete', 'warung_pkk.print',
            'tutor_khusus.view', 'tutor_khusus.create', 'tutor_khusus.update', 'tutor_khusus.delete', 'tutor_khusus.print',
            'data_pelatihan_kader.view', 'data_pelatihan_kader.create', 'data_pelatihan_kader.update', 'data_pelatihan_kader.delete', 'data_pelatihan_kader.print',
            'data_pemanfaatan_tanah_pekarangan_hatinya_pkk.view', 'data_pemanfaatan_tanah_pekarangan_hatinya_pkk.create', 'data_pemanfaatan_tanah_pekarangan_hatinya_pkk.update', 'data_pemanfaatan_tanah_pekarangan_hatinya_pkk.delete', 'data_pemanfaatan_tanah_pekarangan_hatinya_pkk.print',
            'data_industri_rumah_tangga.view', 'data_industri_rumah_tangga.create', 'data_industri_rumah_tangga.update', 'data_industri_rumah_tangga.delete', 'data_industri_rumah_tangga.print',
            'posyandu.view', 'posyandu.create', 'posyandu.update', 'posyandu.delete', 'posyandu.print',
            'bkb_kegiatan.view', 'bkb_kegiatan.create', 'bkb_kegiatan.update', 'bkb_kegiatan.delete', 'bkb_kegiatan.print',
            'pilot_project_naskah_pelaporan.view', 'pilot_project_naskah_pelaporan.create', 'pilot_project_naskah_pelaporan.update', 'pilot_project_naskah_pelaporan.delete', 'pilot_project_naskah_pelaporan.print',
            'pilot_project_keluarga_sehat.view', 'pilot_project_keluarga_sehat.create', 'pilot_project_keluarga_sehat.update', 'pilot_project_keluarga_sehat.delete', 'pilot_project_keluarga_sehat.print',
        ],

        self::ROLE_SEKRETARIS_DESA => [
            'arsip_document.view', 'activities.view', 'agenda_surat.view', 'anggota_pokja.view', 'inventaris.view', 'bantuan.view', 'kader_khusus.view', 'prestasi_lomba.view', 'program_prioritas.view', 'anggota_tim_penggerak.view', 'buku_daftar_hadir.view', 'buku_notulen_rapat.view', 'buku_tamu.view', 'laporan_tahunan_pkk.view', 'data_warga.view', 'data_kegiatan_warga.view', 'data_keluarga.view', 'catatan_keluarga.view', 'buku_keuangan.view', 'simulasi_penyuluhan.view', 'bkr.view', 'paar.view', 'bkl.view', 'literasi_warga.view', 'pra_koperasi_up2k.view', 'koperasi.view', 'kejar_paket.view', 'taman_bacaan.view', 'pelatihan_kader_pokja_ii.view', 'warung_pkk.view', 'tutor_khusus.view', 'data_pelatihan_kader.view', 'data_pemanfaatan_tanah_pekarangan_hatinya_pkk.view', 'data_industri_rumah_tangga.view', 'posyandu.view', 'bkb_kegiatan.view', 'pilot_project_naskah_pelaporan.view', 'pilot_project_keluarga_sehat.view',
            'arsip_document.view', 'arsip_document.create', 'arsip_document.update', 'arsip_document.delete', 'arsip_document.export',
            'activities.view', 'activities.create', 'activities.update', 'activities.delete', 'activities.print',
            'anggota_pokja.view', 'anggota_pokja.create', 'anggota_pokja.update', 'anggota_pokja.delete', 'anggota_pokja.print',
            'agenda_surat.view', 'agenda_surat.create', 'agenda_surat.update', 'agenda_surat.delete', 'agenda_surat.print',
            'inventaris.view', 'inventaris.create', 'inventaris.update', 'inventaris.delete', 'inventaris.print',
            'bantuan.view', 'bantuan.create', 'bantuan.update', 'bantuan.delete', 'bantuan.print',
            'kader_khusus.view', 'kader_khusus.create', 'kader_khusus.update', 'kader_khusus.delete', 'kader_khusus.print',
            'prestasi_lomba.view', 'prestasi_lomba.create', 'prestasi_lomba.update', 'prestasi_lomba.delete', 'prestasi_lomba.print',
            'program_prioritas.view', 'program_prioritas.create', 'program_prioritas.update', 'program_prioritas.delete', 'program_prioritas.print',
            'anggota_tim_penggerak.view', 'anggota_tim_penggerak.create', 'anggota_tim_penggerak.update', 'anggota_tim_penggerak.delete', 'anggota_tim_penggerak.print',
            'buku_daftar_hadir.view', 'buku_daftar_hadir.create', 'buku_daftar_hadir.update', 'buku_daftar_hadir.delete', 'buku_daftar_hadir.print',
            'buku_notulen_rapat.view', 'buku_notulen_rapat.create', 'buku_notulen_rapat.update', 'buku_notulen_rapat.delete', 'buku_notulen_rapat.print',
            'buku_ekspedisi.view', 'buku_ekspedisi.create', 'buku_ekspedisi.update', 'buku_ekspedisi.delete', 'buku_ekspedisi.print',
            'buku_tamu.view', 'buku_tamu.create', 'buku_tamu.update', 'buku_tamu.delete', 'buku_tamu.print',
            'laporan_tahunan_pkk.view', 'laporan_tahunan_pkk.create', 'laporan_tahunan_pkk.update', 'laporan_tahunan_pkk.delete', 'laporan_tahunan_pkk.print',
            'data_warga.view', 'data_warga.create', 'data_warga.update', 'data_warga.delete', 'data_warga.print',
            'data_kegiatan_warga.view', 'data_kegiatan_warga.create', 'data_kegiatan_warga.update', 'data_kegiatan_warga.delete', 'data_kegiatan_warga.print',
            'data_keluarga.view', 'data_keluarga.create', 'data_keluarga.update', 'data_keluarga.delete', 'data_keluarga.print',
            'catatan_keluarga.view', 'catatan_keluarga.create', 'catatan_keluarga.update', 'catatan_keluarga.delete', 'catatan_keluarga.print',
        ],

        self::ROLE_BENDAHARA_DESA => [
            'arsip_document.view', 'activities.view', 'agenda_surat.view', 'anggota_pokja.view', 'inventaris.view', 'bantuan.view', 'kader_khusus.view', 'prestasi_lomba.view', 'program_prioritas.view', 'anggota_tim_penggerak.view', 'buku_daftar_hadir.view', 'buku_notulen_rapat.view', 'buku_tamu.view', 'laporan_tahunan_pkk.view', 'data_warga.view', 'data_kegiatan_warga.view', 'data_keluarga.view', 'catatan_keluarga.view', 'buku_keuangan.view', 'simulasi_penyuluhan.view', 'bkr.view', 'paar.view', 'bkl.view', 'literasi_warga.view', 'pra_koperasi_up2k.view', 'koperasi.view', 'kejar_paket.view', 'taman_bacaan.view', 'pelatihan_kader_pokja_ii.view', 'warung_pkk.view', 'tutor_khusus.view', 'data_pelatihan_kader.view', 'data_pemanfaatan_tanah_pekarangan_hatinya_pkk.view', 'data_industri_rumah_tangga.view', 'posyandu.view', 'bkb_kegiatan.view', 'pilot_project_naskah_pelaporan.view', 'pilot_project_keluarga_sehat.view',
            'activities.view', 'activities.create', 'activities.update', 'activities.delete', 'activities.print',
            'buku_keuangan.view', 'buku_keuangan.create', 'buku_keuangan.update', 'buku_keuangan.delete', 'buku_keuangan.print',
            'data_warga.view', 'data_keluarga.view', 'data_kegiatan_warga.view', 'catatan_keluarga.view',
        ],

        self::ROLE_POKJA_1_DESA => [
            'arsip_document.view', 'activities.view', 'agenda_surat.view', 'anggota_pokja.view', 'inventaris.view', 'bantuan.view', 'kader_khusus.view', 'prestasi_lomba.view', 'program_prioritas.view', 'anggota_tim_penggerak.view', 'buku_daftar_hadir.view', 'buku_notulen_rapat.view', 'buku_tamu.view', 'laporan_tahunan_pkk.view', 'data_warga.view', 'data_kegiatan_warga.view', 'data_keluarga.view', 'catatan_keluarga.view', 'buku_keuangan.view', 'simulasi_penyuluhan.view', 'bkr.view', 'paar.view', 'bkl.view', 'literasi_warga.view', 'pra_koperasi_up2k.view', 'koperasi.view', 'kejar_paket.view', 'taman_bacaan.view', 'pelatihan_kader_pokja_ii.view', 'warung_pkk.view', 'tutor_khusus.view', 'data_pelatihan_kader.view', 'data_pemanfaatan_tanah_pekarangan_hatinya_pkk.view', 'data_industri_rumah_tangga.view', 'posyandu.view', 'bkb_kegiatan.view', 'pilot_project_naskah_pelaporan.view', 'pilot_project_keluarga_sehat.view',
            'activities.view', 'activities.create', 'activities.update', 'activities.delete', 'activities.print',
            'anggota_pokja.view', 'anggota_pokja.create', 'anggota_pokja.update', 'anggota_pokja.delete', 'anggota_pokja.print',
            'program_prioritas.view', 'program_prioritas.create', 'program_prioritas.update', 'program_prioritas.delete', 'program_prioritas.print',
            'inventaris.view', 'inventaris.create', 'inventaris.update', 'inventaris.delete', 'inventaris.print',
            'bantuan.view', 'bantuan.create', 'bantuan.update', 'bantuan.delete', 'bantuan.print',
            'kader_khusus.view', 'kader_khusus.create', 'kader_khusus.update', 'kader_khusus.delete', 'kader_khusus.print',
            'prestasi_lomba.view', 'prestasi_lomba.create', 'prestasi_lomba.update', 'prestasi_lomba.delete', 'prestasi_lomba.print',
            'simulasi_penyuluhan.view', 'simulasi_penyuluhan.create', 'simulasi_penyuluhan.update', 'simulasi_penyuluhan.delete', 'simulasi_penyuluhan.print',
            'bkr.view', 'bkr.create', 'bkr.update', 'bkr.delete', 'bkr.print',
            'paar.view', 'paar.create', 'paar.update', 'paar.delete', 'paar.print',
            'bkl.view', 'bkl.create', 'bkl.update', 'bkl.delete', 'bkl.print',
            'literasi_warga.view', 'literasi_warga.create', 'literasi_warga.update', 'literasi_warga.delete', 'literasi_warga.print',
            'data_warga.view', 'data_keluarga.view', 'data_kegiatan_warga.view', 'catatan_keluarga.view',
        ],

        self::ROLE_POKJA_2_DESA => [
            'arsip_document.view', 'activities.view', 'agenda_surat.view', 'anggota_pokja.view', 'inventaris.view', 'bantuan.view', 'kader_khusus.view', 'prestasi_lomba.view', 'program_prioritas.view', 'anggota_tim_penggerak.view', 'buku_daftar_hadir.view', 'buku_notulen_rapat.view', 'buku_tamu.view', 'laporan_tahunan_pkk.view', 'data_warga.view', 'data_kegiatan_warga.view', 'data_keluarga.view', 'catatan_keluarga.view', 'buku_keuangan.view', 'simulasi_penyuluhan.view', 'bkr.view', 'paar.view', 'bkl.view', 'literasi_warga.view', 'pra_koperasi_up2k.view', 'koperasi.view', 'kejar_paket.view', 'taman_bacaan.view', 'pelatihan_kader_pokja_ii.view', 'warung_pkk.view', 'tutor_khusus.view', 'data_pelatihan_kader.view', 'data_pemanfaatan_tanah_pekarangan_hatinya_pkk.view', 'data_industri_rumah_tangga.view', 'posyandu.view', 'bkb_kegiatan.view', 'pilot_project_naskah_pelaporan.view', 'pilot_project_keluarga_sehat.view',
            'activities.view', 'activities.create', 'activities.update', 'activities.delete', 'activities.print',
            'program_prioritas.view', 'program_prioritas.create', 'program_prioritas.update', 'program_prioritas.delete', 'program_prioritas.print',
            'inventaris.view', 'inventaris.create', 'inventaris.update', 'inventaris.delete', 'inventaris.print',
            'bantuan.view', 'bantuan.create', 'bantuan.update', 'bantuan.delete', 'bantuan.print',
            'kader_khusus.view', 'kader_khusus.create', 'kader_khusus.update', 'kader_khusus.delete', 'kader_khusus.print',
            'prestasi_lomba.view', 'prestasi_lomba.create', 'prestasi_lomba.update', 'prestasi_lomba.delete', 'prestasi_lomba.print',
            'pra_koperasi_up2k.view', 'pra_koperasi_up2k.create', 'pra_koperasi_up2k.update', 'pra_koperasi_up2k.delete', 'pra_koperasi_up2k.print',
            'koperasi.view', 'koperasi.create', 'koperasi.update', 'koperasi.delete', 'koperasi.print',
            'kejar_paket.view', 'kejar_paket.create', 'kejar_paket.update', 'kejar_paket.delete', 'kejar_paket.print',
            'taman_bacaan.view', 'taman_bacaan.create', 'taman_bacaan.update', 'taman_bacaan.delete', 'taman_bacaan.print',
            'pelatihan_kader_pokja_ii.view', 'pelatihan_kader_pokja_ii.create', 'pelatihan_kader_pokja_ii.update', 'pelatihan_kader_pokja_ii.delete', 'pelatihan_kader_pokja_ii.print',
            'warung_pkk.view', 'warung_pkk.create', 'warung_pkk.update', 'warung_pkk.delete', 'warung_pkk.print',
            'tutor_khusus.view', 'tutor_khusus.create', 'tutor_khusus.update', 'tutor_khusus.delete', 'tutor_khusus.print',
            'data_pelatihan_kader.view', 'data_pelatihan_kader.create', 'data_pelatihan_kader.update', 'data_pelatihan_kader.delete', 'data_pelatihan_kader.print',
            'data_warga.view', 'data_keluarga.view', 'data_kegiatan_warga.view', 'catatan_keluarga.view',
            'bkb_kegiatan.view', 
        ],

        self::ROLE_POKJA_3_DESA => [
            'arsip_document.view', 'activities.view', 'agenda_surat.view', 'anggota_pokja.view', 'inventaris.view', 'bantuan.view', 'kader_khusus.view', 'prestasi_lomba.view', 'program_prioritas.view', 'anggota_tim_penggerak.view', 'buku_daftar_hadir.view', 'buku_notulen_rapat.view', 'buku_tamu.view', 'laporan_tahunan_pkk.view', 'data_warga.view', 'data_kegiatan_warga.view', 'data_keluarga.view', 'catatan_keluarga.view', 'buku_keuangan.view', 'simulasi_penyuluhan.view', 'bkr.view', 'paar.view', 'bkl.view', 'literasi_warga.view', 'pra_koperasi_up2k.view', 'koperasi.view', 'kejar_paket.view', 'taman_bacaan.view', 'pelatihan_kader_pokja_ii.view', 'warung_pkk.view', 'tutor_khusus.view', 'data_pelatihan_kader.view', 'data_pemanfaatan_tanah_pekarangan_hatinya_pkk.view', 'data_industri_rumah_tangga.view', 'posyandu.view', 'bkb_kegiatan.view', 'pilot_project_naskah_pelaporan.view', 'pilot_project_keluarga_sehat.view',
            'activities.view', 'activities.create', 'activities.update', 'activities.delete', 'activities.print',
            'program_prioritas.view', 'program_prioritas.create', 'program_prioritas.update', 'program_prioritas.delete', 'program_prioritas.print',
            'inventaris.view', 'inventaris.create', 'inventaris.update', 'inventaris.delete', 'inventaris.print',
            'bantuan.view', 'bantuan.create', 'bantuan.update', 'bantuan.delete', 'bantuan.print',
            'kader_khusus.view', 'kader_khusus.create', 'kader_khusus.update', 'kader_khusus.delete', 'kader_khusus.print',
            'prestasi_lomba.view', 'prestasi_lomba.create', 'prestasi_lomba.update', 'prestasi_lomba.delete', 'prestasi_lomba.print',
            'data_keluarga.view', 'data_keluarga.create', 'data_keluarga.update', 'data_keluarga.delete', 'data_keluarga.print',
            'data_pemanfaatan_tanah_pekarangan_hatinya_pkk.view', 'data_pemanfaatan_tanah_pekarangan_hatinya_pkk.create', 'data_pemanfaatan_tanah_pekarangan_hatinya_pkk.update', 'data_pemanfaatan_tanah_pekarangan_hatinya_pkk.delete', 'data_pemanfaatan_tanah_pekarangan_hatinya_pkk.print',
            'data_industri_rumah_tangga.view', 'data_industri_rumah_tangga.create', 'data_industri_rumah_tangga.update', 'data_industri_rumah_tangga.delete', 'data_industri_rumah_tangga.print',
            'data_warga.view', 'data_keluarga.view', 'data_kegiatan_warga.view', 'catatan_keluarga.view',
        ],

        self::ROLE_POKJA_4_DESA => [
            'arsip_document.view', 'activities.view', 'agenda_surat.view', 'anggota_pokja.view', 'inventaris.view', 'bantuan.view', 'kader_khusus.view', 'prestasi_lomba.view', 'program_prioritas.view', 'anggota_tim_penggerak.view', 'buku_daftar_hadir.view', 'buku_notulen_rapat.view', 'buku_tamu.view', 'laporan_tahunan_pkk.view', 'data_warga.view', 'data_kegiatan_warga.view', 'data_keluarga.view', 'catatan_keluarga.view', 'buku_keuangan.view', 'simulasi_penyuluhan.view', 'bkr.view', 'paar.view', 'bkl.view', 'literasi_warga.view', 'pra_koperasi_up2k.view', 'koperasi.view', 'kejar_paket.view', 'taman_bacaan.view', 'pelatihan_kader_pokja_ii.view', 'warung_pkk.view', 'tutor_khusus.view', 'data_pelatihan_kader.view', 'data_pemanfaatan_tanah_pekarangan_hatinya_pkk.view', 'data_industri_rumah_tangga.view', 'posyandu.view', 'bkb_kegiatan.view', 'pilot_project_naskah_pelaporan.view', 'pilot_project_keluarga_sehat.view',
            'activities.view', 'activities.create', 'activities.update', 'activities.delete', 'activities.print',
            'program_prioritas.view', 'program_prioritas.create', 'program_prioritas.update', 'program_prioritas.delete', 'program_prioritas.print',
            'inventaris.view', 'inventaris.create', 'inventaris.update', 'inventaris.delete', 'inventaris.print',
            'bantuan.view', 'bantuan.create', 'bantuan.update', 'bantuan.delete', 'bantuan.print',
            'kader_khusus.view', 'kader_khusus.create', 'kader_khusus.update', 'kader_khusus.delete', 'kader_khusus.print',
            'prestasi_lomba.view', 'prestasi_lomba.create', 'prestasi_lomba.update', 'prestasi_lomba.delete', 'prestasi_lomba.print',
            'posyandu.view', 'posyandu.create', 'posyandu.update', 'posyandu.delete', 'posyandu.print',
            'bkb_kegiatan.view', 'bkb_kegiatan.create', 'bkb_kegiatan.update', 'bkb_kegiatan.delete', 'bkb_kegiatan.print',
            'pilot_project_naskah_pelaporan.view', 'pilot_project_naskah_pelaporan.create', 'pilot_project_naskah_pelaporan.update', 'pilot_project_naskah_pelaporan.delete', 'pilot_project_naskah_pelaporan.print',
            'pilot_project_keluarga_sehat.view', 'pilot_project_keluarga_sehat.create', 'pilot_project_keluarga_sehat.update', 'pilot_project_keluarga_sehat.delete', 'pilot_project_keluarga_sehat.print',
            'data_warga.view', 'data_keluarga.view', 'data_kegiatan_warga.view', 'catatan_keluarga.view',
        ],

        self::ROLE_ADMIN_DUSUN => [
            'arsip_document.view', 'arsip_document.create', 'arsip_document.update', 'arsip_document.delete',
            'activities.view', 'activities.create', 'activities.update', 'activities.delete', 'activities.print',
        ],
    ];

    /**
     * Check if a role has a specific permission.
     */
    public static function hasPermission(?string $role, string $permission): bool
    {
        if (!$role || !isset(self::PERMISSIONS[$role])) {
            return false;
        }

        $rolePermissions = self::PERMISSIONS[$role];

        if (in_array('*', $rolePermissions, true)) {
            return true;
        }

        return in_array($permission, $rolePermissions, true);
    }

    /**
     * @return list<string>
     */
    public static function roleNamesForUser(User $user): array
    {
        $attributes = $user->getAttributes();
        $attributeRole = $attributes['role'] ?? null;
        if (is_string($attributeRole) && $attributeRole !== '') {
            return [$attributeRole];
        }

        $roleNames = $user->getRoleNames()->values()->all();

        if ($roleNames !== []) {
            return $roleNames;
        }

        return [];
    }

    public static function primaryRoleForUser(User $user): ?string
    {
        return self::roleNamesForUser($user)[0] ?? null;
    }

    public static function userHasPermission(User $user, string $permission): bool
    {
        foreach (self::roleNamesForUser($user) as $roleName) {
            if (self::hasPermission($roleName, $permission)) {
                return true;
            }
        }

        return false;
    }

    public static function userIsSuperAdmin(User $user): bool
    {
        return in_array(self::ROLE_SUPER_ADMIN, self::roleNamesForUser($user), true);
    }

    public static function userHasRole(User $user, string $role): bool
    {
        return in_array($role, self::roleNamesForUser($user), true);
    }

    /**
     * Map role to functional job group (e.g., pokja-i, sekretaris-tpk).
     */
    public static function resolveJobGroup(string $role): ?string
    {
        return match ($role) {
            self::ROLE_SEKRETARIS_DESA, self::ROLE_SEKRETARIS_KECAMATAN => 'sekretaris-tpk',
            self::ROLE_BENDAHARA_DESA, self::ROLE_BENDAHARA_KECAMATAN => 'bendahara-tpk',
            self::ROLE_POKJA_1_DESA, self::ROLE_POKJA_1_KECAMATAN => 'pokja-i',
            self::ROLE_POKJA_2_DESA, self::ROLE_POKJA_2_KECAMATAN => 'pokja-ii',
            self::ROLE_POKJA_3_DESA, self::ROLE_POKJA_3_KECAMATAN => 'pokja-iii',
            self::ROLE_POKJA_4_DESA, self::ROLE_POKJA_4_KECAMATAN => 'pokja-iv',
            default => null,
        };
    }

    /**
     * @return array<string, list<string>>
     */
    public static function scopedRoles(): array
    {
        return [
            ScopeLevel::DESA->value => [
                self::ROLE_ADMIN_DESA,
                self::ROLE_SEKRETARIS_DESA,
                self::ROLE_BENDAHARA_DESA,
                self::ROLE_POKJA_1_DESA,
                self::ROLE_POKJA_2_DESA,
                self::ROLE_POKJA_3_DESA,
                self::ROLE_POKJA_4_DESA,
                self::ROLE_ADMIN_DUSUN,
                self::ROLE_ADMIN_RW,
                self::ROLE_ADMIN_RT,
                self::ROLE_ADMIN_DASAWISMA,
            ],
            ScopeLevel::KECAMATAN->value => [
                self::ROLE_ADMIN_KECAMATAN,
                self::ROLE_SEKRETARIS_KECAMATAN,
                self::ROLE_BENDAHARA_KECAMATAN,
                self::ROLE_POKJA_1_KECAMATAN,
                self::ROLE_POKJA_2_KECAMATAN,
                self::ROLE_POKJA_3_KECAMATAN,
                self::ROLE_POKJA_4_KECAMATAN,
                self::ROLE_SUPER_ADMIN,
            ],
        ];
    }

    public static function isRoleCompatibleWithScope(string $role, string $scope): bool
    {
        return in_array($role, self::scopedRoles()[$scope] ?? [], true);
    }

    public static function userHasRoleForScope(User $user, string $scope): bool
    {
        $scopedRoles = self::scopedRoles()[$scope] ?? [];

        foreach (self::roleNamesForUser($user) as $roleName) {
            if (in_array($roleName, $scopedRoles, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return list<string>
     */
    public static function assignableRolesForScope(string $scope): array
    {
        $roles = self::scopedRoles()[$scope] ?? [];

        return array_values(array_filter(
            $roles,
            static fn (string $role) => $role !== self::ROLE_SUPER_ADMIN
        ));
    }

    public static function isRestrictedForManagedAssignment(string $role): bool
    {
        return $role === self::ROLE_SUPER_ADMIN;
    }
}
