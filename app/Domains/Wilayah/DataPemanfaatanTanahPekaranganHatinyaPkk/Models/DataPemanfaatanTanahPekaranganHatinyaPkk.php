<?php

namespace App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class DataPemanfaatanTanahPekaranganHatinyaPkk extends Model
{
    public const KATEGORI_PEMANFAATAN_LAHAN_OPTIONS = [
        'Peternakan',
        'Perikanan',
        'Warung Hidup',
        'TOGA',
        'Tanaman Keras',
        'Lainnya',
    ];

    protected $table = 'data_pemanfaatan_tanah_pekarangan_hatinya_pkks';

    protected $fillable = [
        'kategori_pemanfaatan_lahan',
        'komoditi',
        'jumlah_komoditi',
        'level',
        'area_id',
        'created_by',
    ];

    public static function kategoriPemanfaatanLahanOptions(): array
    {
        return self::KATEGORI_PEMANFAATAN_LAHAN_OPTIONS;
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



