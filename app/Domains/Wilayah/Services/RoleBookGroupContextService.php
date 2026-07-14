<?php

namespace App\Domains\Wilayah\Services;

use App\Models\User;
use Illuminate\Http\Request;

class RoleBookGroupContextService
{
    public const QUERY_KEY = 'book_group';

    /**
     * @var list<string>
     */
    private const VALID_GROUPS = [
        'sekretaris-tpk',
        'bendahara-tpk',
        'pokja-i',
        'pokja-ii',
        'pokja-iii',
        'pokja-iv',
    ];

    public function __construct(
        private readonly Request $request
    ) {
    }

    /**
     * @param array<string, string> $roleToGroupMap
     * @return list<string>
     */
    public function resolveRoleGroups(User $user, array $roleToGroupMap): array
    {
        $groups = [];

        foreach ($user->getRoleNames()->all() as $roleName) {
            if (isset($roleToGroupMap[$roleName])) {
                $groups[] = $roleToGroupMap[$roleName];
            }
        }

        return array_values(array_unique($groups));
    }

    /**
     * @param list<string> $roleGroups
     * @return list<string>
     */
    public function resolveContextualGroups(User $user, string $moduleSlug, array $roleGroups): array
    {
        $requestedGroup = $this->requestedGroup();
        if (is_string($requestedGroup)) {
            if ($this->isGroupAllowed($requestedGroup, $user, $roleGroups)) {
                $this->storeSelectedGroup($moduleSlug, $requestedGroup);

                return [$requestedGroup];
            }

            return [];
        }

        $selectedGroup = $this->selectedGroup($moduleSlug);
        if (is_string($selectedGroup)) {
            if ($this->isGroupAllowed($selectedGroup, $user, $roleGroups)) {
                return [$selectedGroup];
            }

            return [];
        }

        return $roleGroups;
    }

    private function requestedGroup(): ?string
    {
        $value = $this->request->query(self::QUERY_KEY);

        return is_string($value) && in_array($value, self::VALID_GROUPS, true) ? $value : null;
    }

    /**
     * @param list<string> $roleGroups
     */
    private function isGroupAllowed(string $group, User $user, array $roleGroups): bool
    {
        if (\App\Support\RoleScopeMatrix::userIsSuperAdmin($user)) {
            return true;
        }

        return in_array($group, $roleGroups, true);
    }

    private function selectedGroup(string $moduleSlug): ?string
    {
        if (! $this->request->hasSession()) {
            return null;
        }

        $value = $this->request->session()->get($this->sessionKey($moduleSlug));

        return is_string($value) && in_array($value, self::VALID_GROUPS, true) ? $value : null;
    }

    private function storeSelectedGroup(string $moduleSlug, string $group): void
    {
        if (! $this->request->hasSession()) {
            return;
        }

        $this->request->session()->put($this->sessionKey($moduleSlug), $group);
    }

    private function sessionKey(string $moduleSlug): string
    {
        $scope = $this->request->segment(1);
        $normalizedScope = is_string($scope) && $scope !== '' ? $scope : 'global';

        return "role_book_group.{$normalizedScope}.{$moduleSlug}";
    }
}
