<?php

namespace App\Domains\Wilayah\PilotProjectNaskahPelaporan\Models;

use App\Domains\Wilayah\Models\Area;
use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Models\PilotProjectNaskahPelaporanPelaksanaanItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

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
        'tahun_anggaran',
    ];

    protected function casts(): array
    {
        return [
            'surat_tanggal' => 'date',
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

    public function attachments(): HasMany
    {
        return $this->hasMany(PilotProjectNaskahPelaporanAttachment::class, 'report_id');
    }

    public function pelaksanaanItems(): HasMany
    {
        return $this->hasMany(PilotProjectNaskahPelaporanPelaksanaanItem::class, 'report_id')
            ->orderBy('sequence');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getPelaksanaan1Attribute($value): string
    {
        return $this->resolvePelaksanaan(1, $value);
    }

    public function getPelaksanaan2Attribute($value): string
    {
        return $this->resolvePelaksanaan(2, $value);
    }

    public function getPelaksanaan3Attribute($value): string
    {
        return $this->resolvePelaksanaan(3, $value);
    }

    public function getPelaksanaan4Attribute($value): string
    {
        return $this->resolvePelaksanaan(4, $value);
    }

    public function getPelaksanaan5Attribute($value): string
    {
        return $this->resolvePelaksanaan(5, $value);
    }

    private function resolvePelaksanaan(int $sequence, $fallback): string
    {
        if ($this->relationLoaded('pelaksanaanItems') && $this->pelaksanaanItems->isNotEmpty()) {
            $item = $this->pelaksanaanItems->firstWhere('sequence', $sequence);
            if ($item) {
                return (string) $item->description;
            }
        }

        return (string) ($fallback ?? '');
    }
}
