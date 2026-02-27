<?php

namespace App\Domains\Wilayah\BukuTamu\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class BukuTamu extends Model
{
    protected $table = 'buku_tamus';

    protected $fillable = [
        'visit_date',
        'guest_name',
        'purpose',
        'institution',
        'description',
        'level',
        'area_id',
        'created_by',
    ];

    protected $casts = [
        'visit_date' => 'date:Y-m-d',
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
