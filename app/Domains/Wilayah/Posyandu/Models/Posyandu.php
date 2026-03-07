<?php

namespace App\Domains\Wilayah\Posyandu\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Posyandu extends Model
{
    protected $table = 'posyandus';

    protected $fillable = [
        'nama_posyandu',
        'nama_pengelola',
        'nama_sekretaris',
        'jenis_posyandu',
        'jumlah_kader',
        'jenis_kegiatan',
        'frekuensi_layanan',
        'jumlah_pengunjung_l',
        'jumlah_pengunjung_p',
        'jumlah_petugas_l',
        'jumlah_petugas_p',
        'keterangan',
        'tahun_anggaran',
        'level',
        'area_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_kader' => 'integer',
            'frekuensi_layanan' => 'integer',
            'jumlah_pengunjung_l' => 'integer',
            'jumlah_pengunjung_p' => 'integer',
            'jumlah_petugas_l' => 'integer',
            'jumlah_petugas_p' => 'integer',
            'tahun_anggaran' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $posyandu): void {
            if (! is_numeric($posyandu->tahun_anggaran)) {
                $posyandu->tahun_anggaran = (int) Carbon::now()->format('Y');
            }
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




