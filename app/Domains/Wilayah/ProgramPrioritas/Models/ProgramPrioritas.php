<?php

namespace App\Domains\Wilayah\ProgramPrioritas\Models;

use App\Domains\Wilayah\Models\Area;
use App\Domains\Wilayah\ProgramPrioritas\Models\ProgramPrioritasJadwalBulan;
use App\Domains\Wilayah\ProgramPrioritas\Models\ProgramPrioritasSumberDana;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

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
        'tahun_anggaran',
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
            'tahun_anggaran' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $programPrioritas): void {
            if (is_numeric($programPrioritas->tahun_anggaran)) {
                return;
            }

            $programPrioritas->tahun_anggaran = (int) Carbon::now()->format('Y');
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

    public function jadwalBulans()
    {
        return $this->hasMany(ProgramPrioritasJadwalBulan::class);
    }

    public function sumberDanas()
    {
        return $this->hasMany(ProgramPrioritasSumberDana::class);
    }

    public function getJadwalBulan1Attribute($value): bool
    {
        return $this->resolveMonthlyFlag(1, $value);
    }

    public function getJadwalBulan2Attribute($value): bool
    {
        return $this->resolveMonthlyFlag(2, $value);
    }

    public function getJadwalBulan3Attribute($value): bool
    {
        return $this->resolveMonthlyFlag(3, $value);
    }

    public function getJadwalBulan4Attribute($value): bool
    {
        return $this->resolveMonthlyFlag(4, $value);
    }

    public function getJadwalBulan5Attribute($value): bool
    {
        return $this->resolveMonthlyFlag(5, $value);
    }

    public function getJadwalBulan6Attribute($value): bool
    {
        return $this->resolveMonthlyFlag(6, $value);
    }

    public function getJadwalBulan7Attribute($value): bool
    {
        return $this->resolveMonthlyFlag(7, $value);
    }

    public function getJadwalBulan8Attribute($value): bool
    {
        return $this->resolveMonthlyFlag(8, $value);
    }

    public function getJadwalBulan9Attribute($value): bool
    {
        return $this->resolveMonthlyFlag(9, $value);
    }

    public function getJadwalBulan10Attribute($value): bool
    {
        return $this->resolveMonthlyFlag(10, $value);
    }

    public function getJadwalBulan11Attribute($value): bool
    {
        return $this->resolveMonthlyFlag(11, $value);
    }

    public function getJadwalBulan12Attribute($value): bool
    {
        return $this->resolveMonthlyFlag(12, $value);
    }

    public function getJadwalIAttribute($value): bool
    {
        return $this->resolveQuarterFlag(1);
    }

    public function getJadwalIiAttribute($value): bool
    {
        return $this->resolveQuarterFlag(4);
    }

    public function getJadwalIiiAttribute($value): bool
    {
        return $this->resolveQuarterFlag(7);
    }

    public function getJadwalIvAttribute($value): bool
    {
        return $this->resolveQuarterFlag(10);
    }

    public function getSumberDanaPusatAttribute($value): bool
    {
        return $this->resolveFundingFlag('pusat', $value);
    }

    public function getSumberDanaApbdAttribute($value): bool
    {
        return $this->resolveFundingFlag('apbd', $value);
    }

    public function getSumberDanaSwdAttribute($value): bool
    {
        return $this->resolveFundingFlag('swd', $value);
    }

    public function getSumberDanaBantAttribute($value): bool
    {
        return $this->resolveFundingFlag('bant', $value);
    }

    private function resolveMonthlyFlag(int $month, $fallback): bool
    {
        if ($this->shouldUseNormalizedJadwal()) {
            return $this->jadwalBulans->contains('month', $month);
        }

        return (bool) $fallback;
    }

    private function resolveQuarterFlag(int $startMonth): bool
    {
        return $this->resolveMonthlyFlag($startMonth, $this->attributes["jadwal_bulan_{$startMonth}"] ?? false)
            || $this->resolveMonthlyFlag($startMonth + 1, $this->attributes['jadwal_bulan_'.($startMonth + 1)] ?? false)
            || $this->resolveMonthlyFlag($startMonth + 2, $this->attributes['jadwal_bulan_'.($startMonth + 2)] ?? false);
    }

    private function resolveFundingFlag(string $source, $fallback): bool
    {
        if ($this->shouldUseNormalizedFunding()) {
            return $this->sumberDanas->contains('source', $source);
        }

        return (bool) $fallback;
    }

    private function shouldUseNormalizedJadwal(): bool
    {
        return $this->relationLoaded('jadwalBulans') && $this->jadwalBulans->isNotEmpty();
    }

    private function shouldUseNormalizedFunding(): bool
    {
        return $this->relationLoaded('sumberDanas') && $this->sumberDanas->isNotEmpty();
    }
}
