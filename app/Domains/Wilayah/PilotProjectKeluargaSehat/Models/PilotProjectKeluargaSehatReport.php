<?php

namespace App\Domains\Wilayah\PilotProjectKeluargaSehat\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    ];

    protected function casts(): array
    {
        return [
            'tahun_awal' => 'integer',
            'tahun_akhir' => 'integer',
        ];
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

