<?php

namespace App\Domains\Wilayah\TamanBacaan\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class TamanBacaan extends Model
{
    protected $table = 'taman_bacaans';

    protected $fillable = [
        'nama_taman_bacaan',
        'nama_pengelola',
        'jumlah_buku_bacaan',
        'jenis_buku',
        'kategori',
        'jumlah',
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
        static::creating(function (TamanBacaan $tamanBacaan): void {
            if (is_numeric($tamanBacaan->tahun_anggaran)) {
                return;
            }

            $tamanBacaan->tahun_anggaran = (int) now()->format('Y');
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

