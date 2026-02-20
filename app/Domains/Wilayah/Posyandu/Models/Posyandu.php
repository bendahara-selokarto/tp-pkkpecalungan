<?php

namespace App\Domains\Wilayah\Posyandu\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Posyandu extends Model
{
    protected $table = 'posyandus';

    protected $fillable = [
        'nama_posyandu',
        'nama_pengelola',
        'nama_sekretaris',
        'jenis_posyandu',
        'jumlah_kader',
        'jenis_kegiatan',
        'frekuensi_layanan',
        'jumlah_pengunjung_l',
        'jumlah_pengunjung_p',
        'jumlah_petugas_l',
        'jumlah_petugas_p',
        'level',
        'area_id',
        'created_by',
    ];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}





