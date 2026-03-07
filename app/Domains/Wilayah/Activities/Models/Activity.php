<?php

namespace App\Domains\Wilayah\Activities\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Activity extends Model
{
    protected $table = 'activities';

    protected $fillable = [
        'title',
        'nama_petugas',
        'jabatan_petugas',
        'description',
        'uraian',
        'level',
        'area_id',
        'created_by',
        'tahun_anggaran',
        'activity_date',
        'tempat_kegiatan',
        'status',
        'tanda_tangan',
        'image_path',
        'document_path',
    ];

    protected function casts(): array
    {
        return [
            'tahun_anggaran' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $activity): void {
            if (is_numeric($activity->tahun_anggaran)) {
                return;
            }

            $activity->tahun_anggaran = is_string($activity->activity_date) && $activity->activity_date !== ''
                ? (int) date('Y', strtotime($activity->activity_date))
                : (int) Carbon::now()->format('Y');
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
