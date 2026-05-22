<?php

namespace App\Domains\Wilayah\BukuAgendaSk\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class BukuAgendaSk extends Model
{
    protected $table = 'buku_agenda_sks';

    protected $fillable = [
        'nomor_sk',
        'tanggal_sk',
        'kepada',
        'perihal',
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

    protected function casts(): array
    {
        return [
            'tanggal_sk' => 'date:Y-m-d',
            'tahun_anggaran' => 'integer',
            'size_bytes' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $bukuAgendaSk): void {
            if (is_numeric($bukuAgendaSk->tahun_anggaran)) {
                return;
            }

            $fallbackYear = $bukuAgendaSk->tanggal_sk
                ? (int) date('Y', strtotime((string) $bukuAgendaSk->tanggal_sk))
                : (int) now()->format('Y');

            $bukuAgendaSk->tahun_anggaran = $fallbackYear;
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
