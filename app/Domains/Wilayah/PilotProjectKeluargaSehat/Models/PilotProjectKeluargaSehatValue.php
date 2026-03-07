<?php

namespace App\Domains\Wilayah\PilotProjectKeluargaSehat\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class PilotProjectKeluargaSehatValue extends Model
{
    protected $table = 'pilot_project_keluarga_sehat_values';

    protected $fillable = [
        'report_id',
        'section',
        'cluster_code',
        'indicator_code',
        'indicator_label',
        'year',
        'semester',
        'value',
        'evaluation_note',
        'keterangan_note',
        'sort_order',
        'level',
        'area_id',
        'created_by',
        'tahun_anggaran',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'semester' => 'integer',
            'value' => 'integer',
            'sort_order' => 'integer',
            'tahun_anggaran' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $value): void {
            if (is_numeric($value->tahun_anggaran)) {
                return;
            }

            $value->tahun_anggaran = (int) Carbon::now()->format('Y');
        });
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(PilotProjectKeluargaSehatReport::class, 'report_id');
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
