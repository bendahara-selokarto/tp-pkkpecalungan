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
        'jadwal_bulan_1',
        'jadwal_bulan_2',
        'jadwal_bulan_3',
        'jadwal_bulan_4',
        'jadwal_bulan_5',
        'jadwal_bulan_6',
        'jadwal_bulan_7',
        'jadwal_bulan_8',
        'jadwal_bulan_9',
        'jadwal_bulan_10',
        'jadwal_bulan_11',
        'jadwal_bulan_12',
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
            'jadwal_bulan_1' => 'boolean',
            'jadwal_bulan_2' => 'boolean',
            'jadwal_bulan_3' => 'boolean',
            'jadwal_bulan_4' => 'boolean',
            'jadwal_bulan_5' => 'boolean',
            'jadwal_bulan_6' => 'boolean',
            'jadwal_bulan_7' => 'boolean',
            'jadwal_bulan_8' => 'boolean',
            'jadwal_bulan_9' => 'boolean',
            'jadwal_bulan_10' => 'boolean',
            'jadwal_bulan_11' => 'boolean',
            'jadwal_bulan_12' => 'boolean',
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
