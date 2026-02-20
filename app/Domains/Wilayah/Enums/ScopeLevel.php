<?php

namespace App\Domains\Wilayah\Enums;

enum ScopeLevel: string
{
    case DESA = 'desa';
    case KECAMATAN = 'kecamatan';

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

