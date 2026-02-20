<?php

namespace App\Domains\Wilayah\PrestasiLomba\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class PrestasiLomba extends Model
{
    protected $table = 'prestasi_lombas';

    protected $fillable = [
        'tahun',
        'jenis_lomba',
        'lokasi',
        'prestasi_kecamatan',
        'prestasi_kabupaten',
        'prestasi_provinsi',
        'prestasi_nasional',
        'keterangan',
        'level',
        'area_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tahun' => 'integer',
            'prestasi_kecamatan' => 'boolean',
            'prestasi_kabupaten' => 'boolean',
            'prestasi_provinsi' => 'boolean',
            'prestasi_nasional' => 'boolean',
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
