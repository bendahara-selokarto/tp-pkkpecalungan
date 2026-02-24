<?php

namespace App\Domains\Wilayah\LaporanTahunanPkk\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanTahunanPkkEntry extends Model
{
    protected $table = 'laporan_tahunan_pkk_entries';

    protected $fillable = [
        'report_id',
        'bidang',
        'activity_date',
        'description',
        'entry_source',
        'level',
        'area_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'activity_date' => 'date',
        ];
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(LaporanTahunanPkkReport::class, 'report_id');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

