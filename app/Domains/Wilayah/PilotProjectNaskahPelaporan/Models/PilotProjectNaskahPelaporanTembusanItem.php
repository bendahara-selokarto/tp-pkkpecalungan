<?php

namespace App\Domains\Wilayah\PilotProjectNaskahPelaporan\Models;

use Illuminate\Database\Eloquent\Model;

class PilotProjectNaskahPelaporanTembusanItem extends Model
{
    protected $table = 'pilot_project_naskah_pelaporan_tembusan_items';

    protected $fillable = [
        'report_id',
        'sequence',
        'value',
        'level',
        'area_id',
        'created_by',
    ];

    public function report()
    {
        return $this->belongsTo(PilotProjectNaskahPelaporanReport::class, 'report_id');
    }
}
