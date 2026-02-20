<?php

namespace App\Domains\Wilayah\Bkl\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Bkl extends Model
{
    protected $table = 'bkls';

    protected $fillable = [
        'desa',
        'nama_bkl',
        'no_tgl_sk',
        'nama_ketua_kelompok',
        'jumlah_anggota',
        'kegiatan',
        'level',
        'area_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_anggota' => 'integer',
        ];
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
