<?php

namespace App\Domains\Wilayah\Activities\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

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
        'activity_date',
        'tempat_kegiatan',
        'status',
        'tanda_tangan',
        'image_path',
        'document_path',
    ];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
