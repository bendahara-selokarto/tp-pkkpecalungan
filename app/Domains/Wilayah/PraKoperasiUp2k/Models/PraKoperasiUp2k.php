<?php

namespace App\Domains\Wilayah\PraKoperasiUp2k\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class PraKoperasiUp2k extends Model
{
    protected $table = 'pra_koperasi_up2k';

    protected $fillable = [
        'tingkat',
        'jumlah_kelompok',
        'jumlah_peserta',
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
        static::creating(function (PraKoperasiUp2k $praKoperasiUp2k): void {
            if (is_numeric($praKoperasiUp2k->tahun_anggaran)) {
                return;
            }

            $praKoperasiUp2k->tahun_anggaran = (int) now()->format('Y');
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
