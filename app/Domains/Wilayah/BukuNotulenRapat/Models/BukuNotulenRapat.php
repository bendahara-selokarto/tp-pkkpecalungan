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
    ];

    protected $casts = [
        'entry_date' => 'date:Y-m-d',
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
