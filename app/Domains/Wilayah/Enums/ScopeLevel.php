<?php

namespace App\Domains\Wilayah\Enums;

enum ScopeLevel: string
{
    case DESA = 'desa';
    case KECAMATAN = 'kecamatan';

    public function reportLevelLabel(): string
    {
        return match ($this) {
            self::DESA => 'DESA/KELURAHAN',
            self::KECAMATAN => 'KECAMATAN',
        };
    }

    public function reportAreaLabel(): string
    {
        return match ($this) {
            self::DESA => 'Desa/Kelurahan',
            self::KECAMATAN => 'Kecamatan',
        };
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(
            static fn (self $level): string => $level->value,
            self::cases()
        );
    }
}
