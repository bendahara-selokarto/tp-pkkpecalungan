<?php

namespace App\Domains\Wilayah\DataKeluarga\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class DataKeluarga extends Model
{
    public const KATEGORI_OPTIONS = [
        'Pra Sejahtera',
        'Sejahtera I',
        'Sejahtera II',
        'Sejahtera III',
        'Sejahtera III Plus',
    ];

    protected $table = 'data_keluargas';

    protected $fillable = [
        'kategori_keluarga',
        'jumlah_keluarga',
        'keterangan',
        'tahun_anggaran',
        'level',
        'area_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_keluarga' => 'integer',
            'tahun_anggaran' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $dataKeluarga): void {
            if (! is_int($dataKeluarga->tahun_anggaran) || $dataKeluarga->tahun_anggaran <= 0) {
                $dataKeluarga->tahun_anggaran = (int) now()->format('Y');
            }
        });
    }

    public static function kategoriOptions(): array
    {
        return self::KATEGORI_OPTIONS;
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
