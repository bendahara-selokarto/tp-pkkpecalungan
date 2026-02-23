<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class MigrateLegacyRoleAssignmentsSeeder extends Seeder
{
    /**
     * @var array<string, string>
     */
    private const LEGACY_ROLE_TARGETS = [
        'admin-desa' => 'desa-sekretaris',
        'admin-kecamatan' => 'kecamatan-sekretaris',
        'desa-bendahara' => 'desa-sekretaris',
        'kecamatan-bendahara' => 'kecamatan-sekretaris',
    ];

    public function run(): void
    {
        User::with('roles')->chunkById(100, function ($users) {
            foreach ($users as $user) {
                $currentRoles = $user->getRoleNames()->map(static fn ($role): string => (string) $role)->all();
                $legacyRoles = array_values(array_intersect(array_keys(self::LEGACY_ROLE_TARGETS), $currentRoles));

                if ($legacyRoles === []) {
                    continue;
                }

                $targetRole = $this->resolveTargetRole($user->scope, $legacyRoles);
                if ($targetRole === null) {
                    continue;
                }

                Role::firstOrCreate(['name' => $targetRole]);

                $nextRoles = array_values(array_diff($currentRoles, array_keys(self::LEGACY_ROLE_TARGETS)));
                if (! in_array($targetRole, $nextRoles, true)) {
                    $nextRoles[] = $targetRole;
                }

                $user->syncRoles($nextRoles);
            }
        });
    }

    /**
     * @param list<string> $legacyRoles
     */
    private function resolveTargetRole(?string $scope, array $legacyRoles): ?string
    {
        if ($scope === 'desa') {
            return 'desa-sekretaris';
        }

        if ($scope === 'kecamatan') {
            return 'kecamatan-sekretaris';
        }

        foreach ($legacyRoles as $legacyRole) {
            $target = self::LEGACY_ROLE_TARGETS[$legacyRole] ?? null;
            if ($target !== null) {
                return $target;
            }
        }

        return null;
    }
}

