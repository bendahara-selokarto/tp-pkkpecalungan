<?php

namespace App\UseCases\SuperAdmin;

use App\Domains\Wilayah\Models\Area;
use App\Domains\Wilayah\Repositories\AreaRepositoryInterface;

class UpdateAreaUseCase
{
    public function __construct(
        private readonly AreaRepositoryInterface $areaRepository
    ) {}

    public function execute(Area $area, array $data): bool
    {
        return $this->areaRepository->update($area, $data);
    }
}
