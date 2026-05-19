<?php

namespace App\Support;

class RoleLabelFormatter
{
    public static function label(string $role): string
    {
        if ($role === 'super_admin' || $role === 'super-admin') {
            return 'Super Admin';
        }

        [$scope, $suffix] = array_pad(explode('-', $role, 2), 2, '');

        if (in_array($scope, ['desa', 'kecamatan'], true) && $suffix !== '') {
            return sprintf('%s (%s)', self::suffixLabel($suffix), ucfirst($scope));
        }

        $parts = explode('_', $role);
        $scope = end($parts);

        if (in_array($scope, ['desa', 'kecamatan'], true)) {
            array_pop($parts);
            $suffix = implode('_', $parts);

            return sprintf('%s (%s)', self::suffixLabel($suffix), ucfirst($scope));
        }

        return ucwords(str_replace(['-', '_'], ' ', $role));
    }

    public static function pdfTitleSuffix(string $role): string
    {
        if ($role === 'super_admin' || $role === 'super-admin') {
            return 'SUPER ADMIN';
        }

        [$scope, $suffix] = array_pad(explode('-', $role, 2), 2, '');

        if (in_array($scope, ['desa', 'kecamatan'], true) && $suffix !== '') {
            return sprintf(
                '%s %s',
                strtoupper(self::suffixLabel($suffix)),
                $scope === 'desa' ? 'PKK DESA' : 'PKK KECAMATAN'
            );
        }

        $parts = explode('_', $role);
        $scope = end($parts);

        if (in_array($scope, ['desa', 'kecamatan'], true)) {
            array_pop($parts);
            $suffix = implode('_', $parts);

            return sprintf(
                '%s %s',
                strtoupper(self::suffixLabel($suffix)),
                $scope === 'desa' ? 'PKK DESA' : 'PKK KECAMATAN'
            );
        }

        return strtoupper(str_replace(['-', '_'], ' ', $role));
    }

    private static function suffixLabel(string $suffix): string
    {
        return match ($suffix) {
            'sekretaris' => 'Sekretaris',
            'bendahara' => 'Bendahara',
            'pokja_1', 'pokja-i' => 'Pokja I',
            'pokja_2', 'pokja-ii' => 'Pokja II',
            'pokja_3', 'pokja-iii' => 'Pokja III',
            'pokja_4', 'pokja-iv' => 'Pokja IV',
            default => ucwords(str_replace(['-', '_'], ' ', $suffix)),
        };
    }
}
