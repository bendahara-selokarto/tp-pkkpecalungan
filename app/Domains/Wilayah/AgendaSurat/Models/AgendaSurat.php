<?php

namespace App\Domains\Wilayah\AgendaSurat\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class AgendaSurat extends Model
{
    protected $table = 'agenda_surats';

    protected $fillable = [
        'jenis_surat',
        'tanggal_terima',
        'tanggal_surat',
        'nomor_surat',
        'asal_surat',
        'dari',
        'kepada',
        'perihal',
        'lampiran',
        'diteruskan_kepada',
        'tembusan',
        'keterangan',
        'data_dukung_path',
        'level',
        'area_id',
        'created_by',
        'tahun_anggaran',
    ];

    protected function casts(): array
    {
        return [
            'tahun_anggaran' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $agendaSurat): void {
            if (is_numeric($agendaSurat->tahun_anggaran)) {
                return;
            }

            $fallbackYear = $agendaSurat->tanggal_surat
                ? (int) date('Y', strtotime((string) $agendaSurat->tanggal_surat))
                : (int) now()->format('Y');

            $agendaSurat->tahun_anggaran = $fallbackYear;
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

    public function lampiranItems()
    {
        return $this->hasMany(AgendaSuratLampiranItem::class)
            ->orderBy('sequence');
    }

    public function tembusanItems()
    {
        return $this->hasMany(AgendaSuratTembusanItem::class)
            ->orderBy('sequence');
    }

    public function getLampiranAttribute($value): ?string
    {
        return $this->resolveMultiValue('lampiranItems', $value);
    }

    public function getTembusanAttribute($value): ?string
    {
        return $this->resolveMultiValue('tembusanItems', $value);
    }

    private function resolveMultiValue(string $relation, $fallback): ?string
    {
        if ($this->relationLoaded($relation)) {
            /** @var Collection $items */
            $items = $this->getRelation($relation);
            if ($items->isNotEmpty()) {
                $values = $items
                    ->pluck('value')
                    ->map(static fn ($value) => trim((string) $value))
                    ->filter(static fn (string $value): bool => $value !== '')
                    ->values()
                    ->all();

                if ($values !== []) {
                    return implode("\n", $values);
                }
            }
        }

        $text = trim((string) ($fallback ?? ''));

        return $text !== '' ? $text : null;
    }
}
