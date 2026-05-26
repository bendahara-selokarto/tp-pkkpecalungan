<?php

namespace App\Domains\Wilayah\Simulasi\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class BukuTamuSimulasi extends Model
{
    protected $table = 'buku_tamu_simulasis';

    protected $fillable = [
        'visit_date',
        'description',
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

    protected function casts(): array
    {
        return [
            'visit_date' => 'date:Y-m-d',
            'tahun_anggaran' => 'integer',
            'size_bytes' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            if (is_numeric($model->tahun_anggaran)) {
                return;
            }

            $fallbackYear = $model->visit_date
                ? (int) date('Y', strtotime((string) $model->visit_date))
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

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? Storage::disk('public')->url($this->file_path) : null;
    }
}
