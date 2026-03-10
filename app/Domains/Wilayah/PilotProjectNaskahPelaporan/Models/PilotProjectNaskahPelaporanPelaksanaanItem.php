<?php

namespace App\Domains\Wilayah\PilotProjectNaskahPelaporan\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PilotProjectNaskahPelaporanPelaksanaanItem extends Model
{
    protected $table = 'pilot_project_naskah_pelaporan_pelaksanaan_items';

    protected $fillable = [
        'report_id',
        'sequence',
        'description',
        'level',
        'area_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'sequence' => 'integer',
        ];
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(PilotProjectNaskahPelaporanReport::class, 'report_id');
    }
}
