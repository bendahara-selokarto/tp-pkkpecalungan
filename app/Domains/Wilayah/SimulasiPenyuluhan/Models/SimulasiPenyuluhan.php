<?php

namespace App\Domains\Wilayah\SimulasiPenyuluhan\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class SimulasiPenyuluhan extends Model
{
    protected $table = 'simulasi_penyuluhans';

    protected $fillable = [
        'nama_kegiatan',
        'jenis_simulasi_penyuluhan',
        'jumlah_kelompok',
        'jumlah_sosialisasi',
        'jumlah_kader_l',
        'jumlah_kader_p',
        'keterangan',
        'tahun_anggaran',
        'level',
        'area_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_kelompok' => 'integer',
            'jumlah_sosialisasi' => 'integer',
            'jumlah_kader_l' => 'integer',
            'jumlah_kader_p' => 'integer',
            'tahun_anggaran' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (SimulasiPenyuluhan $simulasiPenyuluhan): void {
            if (is_numeric($simulasiPenyuluhan->tahun_anggaran)) {
                return;
            }

            $simulasiPenyuluhan->tahun_anggaran = (int) now()->format('Y');
        });
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
