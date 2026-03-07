<?php

namespace App\Domains\Wilayah\DataIndustriRumahTangga\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class DataIndustriRumahTangga extends Model
{
    public const KATEGORI_JENIS_INDUSTRI_OPTIONS = [
        'Pangan',
        'Sandang',
        'Konveksi',
        'Jasa',
        'Lain-lain',
    ];

    protected $table = 'data_industri_rumah_tanggas';

    protected $fillable = [
        'kategori_jenis_industri',
        'komoditi',
        'jumlah_komoditi',
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
        static::creating(function (DataIndustriRumahTangga $dataIndustriRumahTangga): void {
            if (is_numeric($dataIndustriRumahTangga->tahun_anggaran)) {
                return;
            }

            $dataIndustriRumahTangga->tahun_anggaran = (int) now()->format('Y');
        });
    }

    public static function kategoriJenisIndustriOptions(): array
    {
        return self::KATEGORI_JENIS_INDUSTRI_OPTIONS;
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
