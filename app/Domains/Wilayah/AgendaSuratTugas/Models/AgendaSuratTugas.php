<?php

namespace App\Domains\Wilayah\AgendaSuratTugas\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AgendaSuratTugas extends Model
{
    protected $table = 'agenda_surat_tugas';

    protected $fillable = [
        'nomor_surat',
        'tanggal_surat',
        'kepada',
        'perihal',
        'lampiran',
        'tembusan',
        'file_path',
        'original_name',
        'mime_type',
        'extension',
        'size_bytes',
        'level',
        'area_id',
        'created_by',
        'tahun_anggaran',
    ];

    protected $appends = ['file_url'];

    protected function casts(): array
    {
        return [
            'tanggal_surat' => 'date:Y-m-d',
            'tahun_anggaran' => 'integer',
            'size_bytes' => 'integer',
        ];
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path 
            ? \Illuminate\Support\Facades\Storage::disk('public')->url($this->file_path)
            : null;
    }

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            if (is_numeric($model->tahun_anggaran)) {
                return;
            }

            $fallbackYear = $model->tanggal_surat
                ? (int) date('Y', strtotime((string) $model->tanggal_surat))
                : (int) now()->format('Y');

            $model->tahun_anggaran = $fallbackYear;
        });
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
