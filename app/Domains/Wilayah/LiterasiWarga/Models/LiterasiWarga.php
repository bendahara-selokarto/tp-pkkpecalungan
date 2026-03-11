<?php

namespace App\Domains\Wilayah\LiterasiWarga\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class LiterasiWarga extends Model
{
    protected $table = 'literasi_wargas';

    protected $fillable = [
        'jumlah_tiga_buta',
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
        static::creating(function (LiterasiWarga $literasiWarga): void {
            if (is_numeric($literasiWarga->tahun_anggaran)) {
                return;
            }

            $literasiWarga->tahun_anggaran = (int) now()->format('Y');
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
