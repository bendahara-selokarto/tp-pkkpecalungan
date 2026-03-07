<?php

namespace App\Domains\Wilayah\KaderKhusus\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class KaderKhusus extends Model
{
    protected $table = 'kader_khusus';

    protected $fillable = [
        'nama',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'status_perkawinan',
        'alamat',
        'pendidikan',
        'jenis_kader_khusus',
        'keterangan',
        'level',
        'area_id',
        'created_by',
        'tahun_anggaran',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'tahun_anggaran' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $kaderKhusus): void {
            if (is_numeric($kaderKhusus->tahun_anggaran)) {
                return;
            }

            $kaderKhusus->tahun_anggaran = (int) now()->format('Y');
        });
    }

    protected $appends = [
        'umur',
    ];

    public function getUmurAttribute(): ?int
    {
        if (! $this->tanggal_lahir) {
            return null;
        }

        return Carbon::parse($this->tanggal_lahir)->age;
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
