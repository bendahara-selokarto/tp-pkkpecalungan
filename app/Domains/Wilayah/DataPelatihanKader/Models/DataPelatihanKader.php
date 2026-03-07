<?php

namespace App\Domains\Wilayah\DataPelatihanKader\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class DataPelatihanKader extends Model
{
    public const STATUS_SERTIFIKAT_OPTIONS = [
        'Bersertifikat',
        'Tidak',
    ];

    protected $table = 'data_pelatihan_kaders';

    protected $fillable = [
        'nomor_registrasi',
        'nama_lengkap_kader',
        'tanggal_masuk_tp_pkk',
        'jabatan_fungsi',
        'nomor_urut_pelatihan',
        'judul_pelatihan',
        'jenis_kriteria_kaderisasi',
        'tahun_penyelenggaraan',
        'institusi_penyelenggara',
        'status_sertifikat',
        'tahun_anggaran',
        'level',
        'area_id',
        'created_by',
    ];

    protected $casts = [
        'nomor_urut_pelatihan' => 'integer',
        'tahun_penyelenggaraan' => 'integer',
        'tahun_anggaran' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $dataPelatihanKader): void {
            if (! is_numeric($dataPelatihanKader->tahun_anggaran)) {
                if (is_numeric($dataPelatihanKader->tahun_penyelenggaraan)) {
                    $dataPelatihanKader->tahun_anggaran = (int) $dataPelatihanKader->tahun_penyelenggaraan;

                    return;
                }

                $dataPelatihanKader->tahun_anggaran = (int) Carbon::now()->format('Y');
            }
        });
    }

    public static function statusSertifikatOptions(): array
    {
        return self::STATUS_SERTIFIKAT_OPTIONS;
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
