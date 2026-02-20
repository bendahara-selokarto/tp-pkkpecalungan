<?php

namespace App\Domains\Wilayah\TamanBacaan\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class TamanBacaan extends Model
{
    protected $table = 'taman_bacaans';

    protected $fillable = [
        'nama_taman_bacaan',
        'nama_pengelola',
        'jumlah_buku_bacaan',
        'jenis_buku',
        'kategori',
        'jumlah',
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


