<?php

namespace App\Domains\Wilayah\LaporanTahunanPkk\Actions;

use App\Domains\Wilayah\LaporanTahunanPkk\Models\LaporanTahunanPkkReport;
use App\Domains\Wilayah\LaporanTahunanPkk\Repositories\LaporanTahunanPkkRepositoryInterface;

class DeleteLaporanTahunanPkkAction
{
    public function __construct(
        private readonly LaporanTahunanPkkRepositoryInterface $repository
    ) {
    }

    public function execute(LaporanTahunanPkkReport $report): void
    {
        $this->repository->deleteReport($report);
    }
}

