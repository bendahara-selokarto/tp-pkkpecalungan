<?php

namespace App\Domains\Wilayah\Paar\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Paar extends Model
{
    protected $table = 'paars';

    /**
     * @var array<string, string>
     */
    public const INDICATORS = [
        'akte_kelahiran' => 'Jumlah Penduduk yang mempunyai Akte Kelahiran',
        'kia' => 'Jumlah Anak yang mempunyai Kartu Identitas Anak (KIA)',
        'kekerasan_seksual_anak' => 'Kasus Kekerasan Seksual pada Anak',
        'kdrt' => 'Kasus Kekerasan Dalam Rumah Tangga',
        'perdagangan_anak' => 'Kasus Perdagangan Anak (Trafficking)',
        'narkoba' => 'Kasus Narkoba',
    ];

    protected $fillable = [
        'indikator',
        'jumlah',
        'keterangan',
        'level',
        'area_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
        ];
    }

    public static function indicatorLabel(string $key): string
    {
        return self::INDICATORS[$key] ?? $key;
    }

    /**
     * @return list<string>
     */
    public static function indicatorKeys(): array
    {
        return array_keys(self::INDICATORS);
    }

    /**
     * @return list<array{value:string,label:string}>
     */
    public static function indicatorOptions(): array
    {
        return collect(self::INDICATORS)
            ->map(static fn (string $label, string $value): array => [
                'value' => $value,
                'label' => $label,
            ])
            ->values()
            ->all();
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