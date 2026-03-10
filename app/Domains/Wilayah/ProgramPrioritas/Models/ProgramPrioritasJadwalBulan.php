<?php

namespace App\Domains\Wilayah\ProgramPrioritas\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramPrioritasJadwalBulan extends Model
{
    protected $table = 'program_prioritas_jadwal_months';

    protected $fillable = [
        'program_prioritas_id',
        'month',
        'level',
        'area_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'month' => 'integer',
        ];
    }

    public function programPrioritas()
    {
        return $this->belongsTo(ProgramPrioritas::class);
    }
}
