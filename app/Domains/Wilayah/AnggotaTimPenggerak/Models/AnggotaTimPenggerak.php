<?php

namespace App\Domains\Wilayah\AnggotaTimPenggerak\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class AnggotaTimPenggerak extends Model
{
    protected $table = 'anggota_tim_penggeraks';

    protected $fillable = [
        'nama',
        'jabatan',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'status_perkawinan',
        'alamat',
        'pendidikan',
        'pekerjaan',
        'keterangan',
        'level',
        'area_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
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

