<?php

namespace App\Domains\Wilayah\KejarPaket\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class KejarPaket extends Model
{
    protected $table = 'kejar_pakets';

    protected $fillable = [
        'nama_kejar_paket',
        'jenis_kejar_paket',
        'jumlah_warga_belajar_l',
        'jumlah_warga_belajar_p',
        'jumlah_pengajar_l',
        'jumlah_pengajar_p',
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





