<?php

namespace App\Domains\Wilayah\AgendaSuratTugas\UseCases;

use App\Domains\Wilayah\AgendaSuratTugas\Models\AgendaSuratTugas;
use App\Domains\Wilayah\AgendaSuratTugas\Repositories\AgendaSuratTugasRepository;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetScopedAgendaSuratTugasUseCase
{
    public function __construct(
        private readonly AgendaSuratTugasRepository $repository
    ) {
    }

    public function execute(int $id, string $level): AgendaSuratTugas
    {
        /** @var User $user */
        $user = Auth::user();

        $item = $this->repository->findScoped($id, $level, (int) $user->area_id);

        if (! $item) {
            throw new NotFoundHttpException('Agenda Surat Tugas tidak ditemukan.');
        }

        return $item;
    }
}
