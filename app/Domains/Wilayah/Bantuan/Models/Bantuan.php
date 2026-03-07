<?php

namespace App\Domains\Wilayah\Bantuan\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Bantuan extends Model
{
    protected $table = 'bantuans';

    protected $fillable = [
        'name',
        'category',
        'description',
        'source',
        'amount',
        'received_date',
        'tahun_anggaran',
        'level',
        'area_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tahun_anggaran' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Bantuan $bantuan): void {
            if (is_numeric($bantuan->tahun_anggaran)) {
                return;
            }

            $bantuan->tahun_anggaran = (int) now()->format('Y');
        });
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
