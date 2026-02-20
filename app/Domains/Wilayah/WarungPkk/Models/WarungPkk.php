<?php

namespace App\Domains\Wilayah\WarungPkk\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class WarungPkk extends Model
{
    protected $table = 'warung_pkks';

    protected $fillable = [
        'nama_warung_pkk',
        'nama_pengelola',
        'komoditi',
        'kategori',
        'volume',
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
