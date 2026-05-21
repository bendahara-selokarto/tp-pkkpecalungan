<?php

namespace App\Domains\Wilayah\BukuEkspedisi\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class BukuEkspedisi extends Model
{
    protected $table = 'buku_ekspedisis';

    protected $fillable = [
        'title',
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

    protected $casts = [
        'size_bytes' => 'integer',
        'tahun_anggaran' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $bukuEkspedisi): void {
            if (is_numeric($bukuEkspedisi->tahun_anggaran)) {
                return;
            }

            $bukuEkspedisi->tahun_anggaran = (int) now()->format('Y');
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
