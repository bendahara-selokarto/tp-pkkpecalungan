<?php

namespace App\Domains\Wilayah\Bkr\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Bkr extends Model
{
    protected $table = 'bkrs';

    protected $fillable = [
        'desa',
        'nama_bkr',
        'no_tgl_sk',
        'nama_ketua_kelompok',
        'jumlah_anggota',
        'kegiatan',
        'tahun_anggaran',
        'level',
        'area_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_anggota' => 'integer',
            'tahun_anggaran' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $bkr): void {
            if (! is_numeric($bkr->tahun_anggaran)) {
                $bkr->tahun_anggaran = (int) Carbon::now()->format('Y');
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
