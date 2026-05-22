<?php

namespace App\Domains\Wilayah\BukuTamu\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class BukuTamu extends Model
{
    protected $table = 'buku_tamus';

    protected $fillable = [
        'title',
        'visit_date',
        'guest_name',
        'purpose',
        'institution',
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
        static::creating(function (self $bukuTamu): void {
            if (is_numeric($bukuTamu->tahun_anggaran)) {
                return;
            }

            $fallbackYear = $bukuTamu->visit_date
                ? (int) date('Y', strtotime((string) $bukuTamu->visit_date))
                : (int) now()->format('Y');

            $bukuTamu->tahun_anggaran = $fallbackYear;
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
