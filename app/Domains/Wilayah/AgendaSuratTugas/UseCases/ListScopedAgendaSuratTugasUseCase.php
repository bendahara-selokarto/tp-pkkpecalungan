<?php

namespace App\Domains\Wilayah\AgendaSuratTugas\UseCases;

use App\Domains\Wilayah\AgendaSuratTugas\Repositories\AgendaSuratTugasRepository;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class ListScopedAgendaSuratTugasUseCase
{
    public function __construct(
        private readonly AgendaSuratTugasRepository $repository
    ) {
    }

    public function execute(string $level, int $perPage = 10): LengthAwarePaginator
    {
        /** @var User $user */
        $user = Auth::user();

        return $this->repository->listScoped($user, $level, $perPage);
    }
}
