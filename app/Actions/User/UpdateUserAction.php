<?php
namespace App\Actions\User;

use App\Domains\Wilayah\Repositories\AreaRepositoryInterface;
use App\Models\User;
use App\Support\RoleScopeMatrix;
use DomainException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UpdateUserAction
{
    public function __construct(
        private readonly AreaRepositoryInterface $areaRepository
    ) {
    }

    public function execute(User $user, array $data): User
    {
        if ($user->hasRole('super-admin')) {
            throw new DomainException('Super Admin tidak boleh diubah');
        }

        $resolvedScopeAndArea = $this->resolveScopeAndArea($user, $data);

        $updatePayload = [
            'name' => $data['name'],
            'email' => $data['email'],
            'scope' => $resolvedScopeAndArea['scope'],
            'area_id' => $resolvedScopeAndArea['area_id'],
        ];

        $user->update($updatePayload);

        if (! empty($data['password'])) {
            $user->update([
                'password' => Hash::make($data['password']),
            ]);
        }

        $user->syncRoles([$data['role']]);

        return $user;
    }

    /**
     * @return array{scope: string, area_id: int}
     */
    private function resolveScopeAndArea(User $user, array $data): array
    {
        $role = (string) ($data['role'] ?? '');
        $areaIdSource = $data['area_id'] ?? $user->area_id;
        $areaId = is_numeric($areaIdSource) ? (int) $areaIdSource : 0;

        $scopeFromArea = $this->areaRepository->getLevelById($areaId);
        if (! is_string($scopeFromArea)) {
            throw ValidationException::withMessages([
                'area_id' => 'Area tidak sesuai dengan scope yang dipilih.',
            ]);
        }

        $requestedScope = (string) ($data['scope'] ?? '');
        if ($requestedScope !== '' && $requestedScope !== $scopeFromArea) {
            throw ValidationException::withMessages([
                'scope' => 'Scope tidak sesuai dengan area yang dipilih.',
            ]);
        }

        if (! RoleScopeMatrix::isRoleCompatibleWithScope($role, $scopeFromArea)) {
            throw ValidationException::withMessages([
                'role' => 'Role tidak sesuai dengan scope yang dipilih.',
            ]);
        }

        return [
            'scope' => $scopeFromArea,
            'area_id' => $areaId,
        ];
    }
}
