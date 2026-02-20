<?php

namespace App\Domains\Wilayah\DataWarga\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class DataWarga extends Model
{
    protected $table = 'data_wargas';

    protected $fillable = [
        'dasawisma',
        'nama_kepala_keluarga',
        'alamat',
        'jumlah_warga_laki_laki',
        'jumlah_warga_perempuan',
        'keterangan',
        'level',
        'area_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_warga_laki_laki' => 'integer',
            'jumlah_warga_perempuan' => 'integer',
        ];
    }

    protected $appends = [
        'total_warga',
    ];

    public function getTotalWargaAttribute(): int
    {
        return (int) $this->jumlah_warga_laki_laki + (int) $this->jumlah_warga_perempuan;
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
