<?php

namespace App\Support;

class RoleLabelFormatter
{
    public static function label(string $role): string
    {
        if ($role === 'super-admin') {
            return 'Super Admin';
        }

        [$scope, $suffix] = array_pad(explode('-', $role, 2), 2, '');

        if (! in_array($scope, ['desa', 'kecamatan'], true) || $suffix === '') {
            return ucwords(str_replace('-', ' ', $role));
        }

        $scopeLabel = ucfirst($scope);
        $baseLabel = self::suffixLabel($suffix);

        return sprintf('%s (%s)', $baseLabel, $scopeLabel);
    }

    private static function suffixLabel(string $suffix): string
    {
        return match ($suffix) {
            'sekretaris' => 'Sekretaris',
            'bendahara' => 'Bendahara',
            'pokja-i' => 'Pokja I',
            'pokja-ii' => 'Pokja II',
            'pokja-iii' => 'Pokja III',
            'pokja-iv' => 'Pokja IV',
            default => ucwords(str_replace('-', ' ', $suffix)),
        };
    }
}

