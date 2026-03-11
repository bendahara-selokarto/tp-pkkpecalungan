<?php

namespace App\Domains\Wilayah\PelatihanKaderPokjaIi\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class PelatihanKaderPokjaIi extends Model
{
    protected $table = 'pelatihan_kader_pokja_ii';

    protected $fillable = [
        'kategori_pelatihan',
        'jumlah_kader',
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
        static::creating(function (PelatihanKaderPokjaIi $pelatihanKaderPokjaIi): void {
            if (is_numeric($pelatihanKaderPokjaIi->tahun_anggaran)) {
                return;
            }

            $pelatihanKaderPokjaIi->tahun_anggaran = (int) now()->format('Y');
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
