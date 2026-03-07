<?php

namespace App\Domains\Wilayah\LaporanTahunanPkk\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

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
        'tahun_anggaran',
    ];

    protected function casts(): array
    {
        return [
            'activity_date' => 'date',
            'tahun_anggaran' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $entry): void {
            if (is_numeric($entry->tahun_anggaran)) {
                return;
            }

            $entry->tahun_anggaran = $entry->report instanceof LaporanTahunanPkkReport
                ? (int) $entry->report->tahun_anggaran
                : (int) Carbon::now()->format('Y');
        });
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
