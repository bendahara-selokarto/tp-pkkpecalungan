<?php

namespace App\Domains\Wilayah\PrestasiLomba\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class PrestasiLomba extends Model
{
    protected $table = 'prestasi_lombas';

    protected $fillable = [
        'tahun',
        'jenis_lomba',
        'lokasi',
        'prestasi_kecamatan',
        'prestasi_kabupaten',
        'prestasi_provinsi',
        'prestasi_nasional',
        'keterangan',
        'tahun_anggaran',
        'level',
        'area_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tahun' => 'integer',
            'prestasi_kecamatan' => 'boolean',
            'prestasi_kabupaten' => 'boolean',
            'prestasi_provinsi' => 'boolean',
            'prestasi_nasional' => 'boolean',
            'tahun_anggaran' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (PrestasiLomba $prestasiLomba): void {
            if (is_numeric($prestasiLomba->tahun_anggaran)) {
                return;
            }

            $prestasiLomba->tahun_anggaran = is_numeric($prestasiLomba->tahun)
                ? (int) $prestasiLomba->tahun
                : (int) now()->format('Y');
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
