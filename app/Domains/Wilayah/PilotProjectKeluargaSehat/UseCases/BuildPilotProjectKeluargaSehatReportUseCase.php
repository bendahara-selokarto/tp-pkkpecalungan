<?php

namespace App\Domains\Wilayah\PilotProjectKeluargaSehat\UseCases;

class BuildPilotProjectKeluargaSehatReportUseCase
{
    public function __construct(
        private readonly GetScopedPilotProjectKeluargaSehatUseCase $getScopedUseCase
    ) {
    }

    public function execute(int $id, string $level): array
    {
        $report = $this->getScopedUseCase->execute($id, $level);

        return [
            'report' => $report,
            'values' => $report->values->sortBy('sort_order')->values(),
            'sections' => config('pilot_project_keluarga_sehat.sections', []),
            'module' => config('pilot_project_keluarga_sehat.module', []),
        ];
    }
}

