<?php

namespace App\Domains\Wilayah\DataKegiatanWarga\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class DataKegiatanWarga extends Model
{
    public const KEGIATAN_OPTIONS = [
        'Penghayatan dan Pengamalan Pancasila',
        'Kerja Bakti',
        'Rukun Kematian',
        'Kegiatan Keagamaan',
        'Jimpitan',
        'Arisan',
        'Lain-Lain',
    ];

    protected $table = 'data_kegiatan_wargas';

    protected $fillable = [
        'kegiatan',
        'aktivitas',
        'keterangan',
        'level',
        'area_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'aktivitas' => 'boolean',
        ];
    }

    public static function kegiatanOptions(): array
    {
        return self::KEGIATAN_OPTIONS;
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
