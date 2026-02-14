<?php

namespace App\Domains\Wilayah\UseCases;

use App\Models\User;
use App\Domains\Wilayah\Repositories\AreaRepository;

class GetWilayahByUser
{
    public function __construct(
        protected AreaRepository $areaRepository
    ) {}

    public function handle(User $user)
    {
        return $this->areaRepository->getByUser($user);
    }
}
