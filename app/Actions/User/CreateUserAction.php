<?php
namespace App\Actions\User;

use App\Domains\Wilayah\Repositories\AreaRepositoryInterface;
use App\Models\User;
use App\Support\RoleScopeMatrix;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateUserAction
{
    public function __construct(
        private readonly AreaRepositoryInterface $areaRepository
    ) {
    }

    public function execute(array $data): User
    {
        $resolvedScopeAndArea = $this->resolveScopeAndArea($data);

        return DB::transaction(function () use ($data, $resolvedScopeAndArea) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'scope' => $resolvedScopeAndArea['scope'],
                'area_id' => $resolvedScopeAndArea['area_id'],
            ]);

            $user->syncRoles([$data['role']]);

            return $user;
        });
    }

    /**
     * @return array{scope: string, area_id: int}
     */
    private function resolveScopeAndArea(array $data): array
    {
        $role = (string) ($data['role'] ?? '');
        $areaId = isset($data['area_id']) && is_numeric($data['area_id'])
            ? (int) $data['area_id']
            : 0;

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
