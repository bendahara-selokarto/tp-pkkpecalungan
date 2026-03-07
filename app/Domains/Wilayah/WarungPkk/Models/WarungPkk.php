<?php

namespace App\Domains\Wilayah\WarungPkk\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class WarungPkk extends Model
{
    protected $table = 'warung_pkks';

    protected $fillable = [
        'nama_warung_pkk',
        'nama_pengelola',
        'komoditi',
        'kategori',
        'volume',
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
        static::creating(function (WarungPkk $warungPkk): void {
            if (is_numeric($warungPkk->tahun_anggaran)) {
                return;
            }

            $warungPkk->tahun_anggaran = (int) now()->format('Y');
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
