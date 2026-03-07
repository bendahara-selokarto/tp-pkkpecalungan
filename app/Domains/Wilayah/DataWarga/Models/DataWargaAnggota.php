<?php

namespace App\Domains\Wilayah\DataWarga\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class DataWargaAnggota extends Model
{
    protected $table = 'data_warga_anggotas';

    protected $fillable = [
        'data_warga_id',
        'nomor_urut',
        'nomor_registrasi',
        'nomor_ktp_kk',
        'nama',
        'jabatan',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'umur_tahun',
        'status_perkawinan',
        'status_dalam_keluarga',
        'agama',
        'alamat',
        'desa_kel_sejenis',
        'pendidikan',
        'pekerjaan',
        'akseptor_kb',
        'aktif_posyandu',
        'ikut_bkb',
        'memiliki_tabungan',
        'ikut_kelompok_belajar',
        'jenis_kelompok_belajar',
        'ikut_paud',
        'ikut_koperasi',
        'keterangan',
        'tahun_anggaran',
        'level',
        'area_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'nomor_urut' => 'integer',
            'tanggal_lahir' => 'date',
            'umur_tahun' => 'integer',
            'tahun_anggaran' => 'integer',
            'akseptor_kb' => 'boolean',
            'aktif_posyandu' => 'boolean',
            'ikut_bkb' => 'boolean',
            'memiliki_tabungan' => 'boolean',
            'ikut_kelompok_belajar' => 'boolean',
            'ikut_paud' => 'boolean',
            'ikut_koperasi' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $dataWargaAnggota): void {
            if (! is_int($dataWargaAnggota->tahun_anggaran) || $dataWargaAnggota->tahun_anggaran <= 0) {
                $dataWargaAnggota->tahun_anggaran = (int) now()->format('Y');
            }
        });
    }

    public function dataWarga()
    {
        return $this->belongsTo(DataWarga::class, 'data_warga_id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
