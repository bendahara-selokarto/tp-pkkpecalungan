<?php

namespace App\Domains\Wilayah\BkbKegiatan\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class BkbKegiatan extends Model
{
    protected $table = 'bkb_kegiatans';

    protected $fillable = [
        'jumlah_kelompok',
        'jumlah_ibu_peserta',
        'jumlah_ape_set',
        'jumlah_kelompok_simulasi',
        'keterangan',
        'tahun_anggaran',
        'level',
        'area_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tahun_anggaran' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (BkbKegiatan $bkbKegiatan): void {
            if (is_numeric($bkbKegiatan->tahun_anggaran)) {
                return;
            }

            $bkbKegiatan->tahun_anggaran = (int) now()->format('Y');
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
