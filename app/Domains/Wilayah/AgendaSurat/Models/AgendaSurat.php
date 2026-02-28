<?php

namespace App\Domains\Wilayah\AgendaSurat\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AgendaSurat extends Model
{
    protected $table = 'agenda_surats';

    protected $fillable = [
        'jenis_surat',
        'tanggal_terima',
        'tanggal_surat',
        'nomor_surat',
        'asal_surat',
        'dari',
        'kepada',
        'perihal',
        'lampiran',
        'diteruskan_kepada',
        'tembusan',
        'keterangan',
        'data_dukung_path',
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
