<?php

namespace App\UseCases\SuperAdmin;

use App\Domains\Wilayah\Repositories\AreaRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListAreasUseCase
{
    public function __construct(
        private readonly AreaRepositoryInterface $areaRepository
    ) {}

    public function execute(int $perPage = 10): LengthAwarePaginator
    {
        return $this->areaRepository->paginate($perPage);
    }
}
