<?php

namespace App\Domains\Wilayah\PilotProjectNaskahPelaporan\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PilotProjectNaskahPelaporanReport extends Model
{
    protected $table = 'pilot_project_naskah_pelaporan_reports';

    protected $fillable = [
        'judul_laporan',
        'surat_kepada',
        'surat_dari',
        'surat_tembusan',
        'surat_tanggal',
        'surat_nomor',
        'surat_sifat',
        'surat_lampiran',
        'surat_hal',
        'dasar_pelaksanaan',
        'pendahuluan',
        'pelaksanaan_1',
        'pelaksanaan_2',
        'pelaksanaan_3',
        'pelaksanaan_4',
        'pelaksanaan_5',
        'penutup',
        'level',
        'area_id',
        'created_by',
    ];

    protected $casts = [
        'surat_tanggal' => 'date',
    ];

    public function attachments(): HasMany
    {
        return $this->hasMany(PilotProjectNaskahPelaporanAttachment::class, 'report_id');
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
