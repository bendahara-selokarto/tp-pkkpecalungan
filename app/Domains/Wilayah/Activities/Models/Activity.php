<?php

namespace App\Domains\Wilayah\Activities\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $table = 'activities';

    protected $fillable = [
    'title',
    'description',
    'level',
    'area_id',
    'created_by',
    'activity_date',
    'status',
];

}
