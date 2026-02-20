<?php
namespace App\Actions\User;

use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\Repositories\AreaRepositoryInterface;
use App\Models\User;
use App\Support\RoleScopeMatrix;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
        $this->assertValidRoleScopeArea($data);

        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'scope' => $data['scope'] ?? ScopeLevel::DESA->value,
                'area_id' => $data['area_id'] ?? null,
            ]);

            $user->syncRoles([$data['role']]);

            return $user;
        });
    }

    private function assertValidRoleScopeArea(array $data): void
    {
        $scope = (string) ($data['scope'] ?? '');
        $role = (string) ($data['role'] ?? '');
        $areaId = isset($data['area_id']) ? (int) $data['area_id'] : 0;

        try {
            $area = $this->areaRepository->find($areaId);
        } catch (ModelNotFoundException) {
            throw ValidationException::withMessages([
                'area_id' => 'Area tidak sesuai dengan scope yang dipilih.',
            ]);
        }

        if (! $area || $area->level !== $scope) {
            throw ValidationException::withMessages([
                'area_id' => 'Area tidak sesuai dengan scope yang dipilih.',
            ]);
        }

        if (! RoleScopeMatrix::isRoleCompatibleWithScope($role, $scope)) {
            throw ValidationException::withMessages([
                'role' => 'Role tidak sesuai dengan scope yang dipilih.',
            ]);
        }
    }
}
