<?php

namespace App\Domains\Wilayah\Inventaris\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Inventaris extends Model
{
    protected $table = 'inventaris';

    protected $fillable = [
        'name',
        'asal_barang',
        'description',
        'keterangan',
        'quantity',
        'unit',
        'tanggal_penerimaan',
        'tempat_penyimpanan',
        'condition',
        'level',
        'area_id',
        'created_by',
        'tahun_anggaran',
    ];

    protected function casts(): array
    {
        return [
            'tahun_anggaran' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $inventaris): void {
            if (is_numeric($inventaris->tahun_anggaran)) {
                return;
            }

            $fallbackYear = $inventaris->tanggal_penerimaan
                ? (int) date('Y', strtotime((string) $inventaris->tanggal_penerimaan))
                : (int) now()->format('Y');

            $inventaris->tahun_anggaran = $fallbackYear;
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
