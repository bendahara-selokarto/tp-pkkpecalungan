<?php

namespace App\Domains\Wilayah\PilotProjectNaskahPelaporan\Actions;

use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Models\PilotProjectNaskahPelaporanReport;
use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Repositories\PilotProjectNaskahPelaporanRepositoryInterface;
use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Services\PilotProjectNaskahPelaporanAttachmentService;

class DeletePilotProjectNaskahPelaporanAction
{
    public function __construct(
        private readonly PilotProjectNaskahPelaporanRepositoryInterface $repository,
        private readonly PilotProjectNaskahPelaporanAttachmentService $attachmentService
    ) {
    }

    public function execute(PilotProjectNaskahPelaporanReport $report): void
    {
        $this->attachmentService->deleteFiles($report->attachments);
        $this->repository->deleteReport($report);
    }
}
