<?php

namespace App\Domains\Wilayah\PilotProjectKeluargaSehat\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class PilotProjectKeluargaSehatReport extends Model
{
    protected $table = 'pilot_project_keluarga_sehat_reports';

    protected $fillable = [
        'judul_laporan',
        'dasar_hukum',
        'pendahuluan',
        'maksud_tujuan',
        'pelaksanaan',
        'dokumentasi',
        'penutup',
        'tahun_awal',
        'tahun_akhir',
        'level',
        'area_id',
        'created_by',
        'tahun_anggaran',
    ];

    protected function casts(): array
    {
        return [
            'tahun_awal' => 'integer',
            'tahun_akhir' => 'integer',
            'tahun_anggaran' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $report): void {
            if (is_numeric($report->tahun_anggaran)) {
                return;
            }

            $report->tahun_anggaran = (int) Carbon::now()->format('Y');
        });
    }

    public function values(): HasMany
    {
        return $this->hasMany(PilotProjectKeluargaSehatValue::class, 'report_id');
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
