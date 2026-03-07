<?php

namespace App\Domains\Wilayah\KejarPaket\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class KejarPaket extends Model
{
    protected $table = 'kejar_pakets';

    protected $fillable = [
        'nama_kejar_paket',
        'jenis_kejar_paket',
        'jumlah_warga_belajar_l',
        'jumlah_warga_belajar_p',
        'jumlah_pengajar_l',
        'jumlah_pengajar_p',
        'tahun_anggaran',
        'level',
        'area_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_warga_belajar_l' => 'integer',
            'jumlah_warga_belajar_p' => 'integer',
            'jumlah_pengajar_l' => 'integer',
            'jumlah_pengajar_p' => 'integer',
            'tahun_anggaran' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (KejarPaket $kejarPaket): void {
            if (is_numeric($kejarPaket->tahun_anggaran)) {
                return;
            }

            $kejarPaket->tahun_anggaran = (int) now()->format('Y');
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




