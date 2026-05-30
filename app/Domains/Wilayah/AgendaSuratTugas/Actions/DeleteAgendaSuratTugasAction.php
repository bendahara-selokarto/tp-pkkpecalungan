<?php

namespace App\Domains\Wilayah\AgendaSuratTugas\Actions;

use App\Domains\Wilayah\AgendaSuratTugas\Models\AgendaSuratTugas;
use App\Domains\Wilayah\AgendaSuratTugas\Repositories\AgendaSuratTugasRepository;
use Illuminate\Support\Facades\Storage;

class DeleteAgendaSuratTugasAction
{
    public function __construct(
        private readonly AgendaSuratTugasRepository $repository
    ) {
    }

    public function execute(AgendaSuratTugas $model): bool
    {
        if ($model->file_path) {
            Storage::disk('public')->delete($model->file_path);
        }

        return $this->repository->delete($model);
    }
}
