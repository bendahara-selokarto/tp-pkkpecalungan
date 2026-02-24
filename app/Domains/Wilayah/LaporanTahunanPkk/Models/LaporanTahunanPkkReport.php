<?php

namespace App\Domains\Wilayah\LaporanTahunanPkk\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LaporanTahunanPkkReport extends Model
{
    protected $table = 'laporan_tahunan_pkk_reports';

    protected $fillable = [
        'judul_laporan',
        'tahun_laporan',
        'pendahuluan',
        'keberhasilan',
        'hambatan',
        'kesimpulan',
        'penutup',
        'disusun_oleh',
        'jabatan_penanda_tangan',
        'nama_penanda_tangan',
        'level',
        'area_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tahun_laporan' => 'integer',
        ];
    }

    public function entries(): HasMany
    {
        return $this->hasMany(LaporanTahunanPkkEntry::class, 'report_id');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

