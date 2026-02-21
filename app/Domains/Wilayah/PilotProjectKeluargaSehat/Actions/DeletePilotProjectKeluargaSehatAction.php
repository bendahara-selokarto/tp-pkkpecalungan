<?php

namespace App\Domains\Wilayah\PilotProjectKeluargaSehat\Actions;

use App\Domains\Wilayah\PilotProjectKeluargaSehat\Models\PilotProjectKeluargaSehatReport;
use App\Domains\Wilayah\PilotProjectKeluargaSehat\Repositories\PilotProjectKeluargaSehatRepositoryInterface;

class DeletePilotProjectKeluargaSehatAction
{
    public function __construct(
        private readonly PilotProjectKeluargaSehatRepositoryInterface $repository
    ) {
    }

    public function execute(PilotProjectKeluargaSehatReport $report): void
    {
        $this->repository->deleteReport($report);
    }
}

