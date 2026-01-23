<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAssignment extends Model
{
    protected $fillable = [
        'user_id',
        'role',
        'kecamatan_id',
        'desa_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }
}
