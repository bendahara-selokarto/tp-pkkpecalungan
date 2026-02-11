<?php

namespace App\Domains\Wilayah\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $fillable = [
        'name',
        'level',
        'parent_id'
    ];

    public function parent()
    {
        return $this->belongsTo(Area::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Area::class, 'parent_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
