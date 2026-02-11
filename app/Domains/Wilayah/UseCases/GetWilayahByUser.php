<?php

namespace App\Domains\Wilayah\UseCases;

use App\Domains\Wilayah\Repositories\AreaRepositoryInterface;
use App\Models\User;

class GetWilayahByUser
{
    public function __construct(
        protected AreaRepositoryInterface $repo
    ) {}

    public function handle(User $user)
    {
        return $this->repo->getByUser($user);
    }
}
