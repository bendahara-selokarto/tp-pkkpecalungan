<?php

namespace App\Domains\Wilayah\UseCases;

use App\Models\User;
use App\Domains\Wilayah\Repositories\AreaRepositoryInterface;

class GetWilayahByUser
{
    public function __construct(
        protected AreaRepositoryInterface $areaRepository
    ) {}

    public function handle(User $user)
    {
        return $this->areaRepository->getByUser($user);
    }
}
