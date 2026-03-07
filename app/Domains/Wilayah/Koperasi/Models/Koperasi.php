<?php

namespace App\Domains\Wilayah\Koperasi\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Koperasi extends Model
{
    protected $table = 'koperasis';

    protected $fillable = [
        'nama_koperasi',
        'jenis_usaha',
        'berbadan_hukum',
        'belum_berbadan_hukum',
        'jumlah_anggota_l',
        'jumlah_anggota_p',
        'tahun_anggaran',
        'level',
        'area_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'berbadan_hukum' => 'boolean',
            'belum_berbadan_hukum' => 'boolean',
            'jumlah_anggota_l' => 'integer',
            'jumlah_anggota_p' => 'integer',
            'tahun_anggaran' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Koperasi $koperasi): void {
            if (is_numeric($koperasi->tahun_anggaran)) {
                return;
            }

            $koperasi->tahun_anggaran = (int) now()->format('Y');
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

