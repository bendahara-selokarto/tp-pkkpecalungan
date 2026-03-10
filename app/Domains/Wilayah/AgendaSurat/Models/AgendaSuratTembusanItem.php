<?php

namespace App\Domains\Wilayah\AgendaSurat\Models;

use Illuminate\Database\Eloquent\Model;

class AgendaSuratTembusanItem extends Model
{
    protected $table = 'agenda_surat_tembusan_items';

    protected $fillable = [
        'agenda_surat_id',
        'sequence',
        'value',
        'level',
        'area_id',
        'created_by',
    ];

    public function agendaSurat()
    {
        return $this->belongsTo(AgendaSurat::class);
    }
}
