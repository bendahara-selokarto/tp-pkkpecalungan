<?php

namespace App\Domains\Wilayah\ProgramPrioritas\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramPrioritasSumberDana extends Model
{
    protected $table = 'program_prioritas_funding_sources';

    protected $fillable = [
        'program_prioritas_id',
        'source',
        'level',
        'area_id',
        'created_by',
    ];

    public function programPrioritas()
    {
        return $this->belongsTo(ProgramPrioritas::class);
    }
}
