<?php

namespace App\Domains\Wilayah\Inventaris\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Inventaris extends Model
{
    protected $table = 'inventaris';

    protected $fillable = [
        'name',
        'asal_barang',
        'description',
        'keterangan',
        'quantity',
        'unit',
        'tanggal_penerimaan',
        'tempat_penyimpanan',
        'condition',
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
