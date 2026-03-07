<?php

namespace App\Domains\Wilayah\BukuNotulenRapat\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class BukuNotulenRapat extends Model
{
    protected $table = 'buku_notulen_rapats';

    protected $fillable = [
        'entry_date',
        'title',
        'person_name',
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
            'entry_date' => 'date:Y-m-d',
            'tahun_anggaran' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $bukuNotulenRapat): void {
            if (is_numeric($bukuNotulenRapat->tahun_anggaran)) {
                return;
            }

            $fallbackYear = $bukuNotulenRapat->entry_date
                ? (int) date('Y', strtotime((string) $bukuNotulenRapat->entry_date))
                : (int) now()->format('Y');

            $bukuNotulenRapat->tahun_anggaran = $fallbackYear;
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
