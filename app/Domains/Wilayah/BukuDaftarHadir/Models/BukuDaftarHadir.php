<?php

namespace App\Domains\Wilayah\BukuDaftarHadir\Models;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class BukuDaftarHadir extends Model
{
    protected $table = 'buku_daftar_hadirs';

    protected $fillable = [
        'attendance_date',
        'activity_id',
        'attendee_name',
        'institution',
        'description',
        'level',
        'area_id',
        'created_by',
        'tahun_anggaran',
    ];

    protected function casts(): array
    {
        return [
            'attendance_date' => 'date:Y-m-d',
            'tahun_anggaran' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $bukuDaftarHadir): void {
            if (is_numeric($bukuDaftarHadir->tahun_anggaran)) {
                return;
            }

            $fallbackYear = $bukuDaftarHadir->attendance_date
                ? (int) date('Y', strtotime((string) $bukuDaftarHadir->attendance_date))
                : (int) now()->format('Y');

            $bukuDaftarHadir->tahun_anggaran = $fallbackYear;
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

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
}
