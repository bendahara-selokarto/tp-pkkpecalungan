<?php

namespace App\Domains\Wilayah\ProgramPrioritas\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ProgramPrioritas extends Model
{
    protected $table = 'program_prioritas';

    protected $fillable = [
        'program',
        'prioritas_program',
        'kegiatan',
        'sasaran_target',
        'jadwal_i',
        'jadwal_ii',
        'jadwal_iii',
        'jadwal_iv',
        'sumber_dana_pusat',
        'sumber_dana_apbd',
        'sumber_dana_swd',
        'sumber_dana_bant',
        'keterangan',
        'level',
        'area_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'jadwal_i' => 'boolean',
            'jadwal_ii' => 'boolean',
            'jadwal_iii' => 'boolean',
            'jadwal_iv' => 'boolean',
            'sumber_dana_pusat' => 'boolean',
            'sumber_dana_apbd' => 'boolean',
            'sumber_dana_swd' => 'boolean',
            'sumber_dana_bant' => 'boolean',
        ];
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
