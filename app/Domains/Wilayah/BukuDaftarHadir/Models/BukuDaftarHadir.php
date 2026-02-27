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
    ];

    protected $casts = [
        'attendance_date' => 'date:Y-m-d',
    ];

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
