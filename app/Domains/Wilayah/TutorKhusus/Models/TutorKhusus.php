<?php

namespace App\Domains\Wilayah\TutorKhusus\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class TutorKhusus extends Model
{
    protected $table = 'tutor_khusus';

    protected $fillable = [
        'jenis_tutor',
        'jumlah_tutor',
        'keterangan',
        'tahun_anggaran',
        'level',
        'area_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tahun_anggaran' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (TutorKhusus $tutorKhusus): void {
            if (is_numeric($tutorKhusus->tahun_anggaran)) {
                return;
            }

            $tutorKhusus->tahun_anggaran = (int) now()->format('Y');
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
