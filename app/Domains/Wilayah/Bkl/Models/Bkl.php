<?php

namespace App\Domains\Wilayah\Bkl\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Bkl extends Model
{
    protected $table = 'bkls';

    protected $fillable = [
        'desa',
        'nama_bkl',
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
        static::creating(function (self $bkl): void {
            if (! is_numeric($bkl->tahun_anggaran)) {
                $bkl->tahun_anggaran = (int) Carbon::now()->format('Y');
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
