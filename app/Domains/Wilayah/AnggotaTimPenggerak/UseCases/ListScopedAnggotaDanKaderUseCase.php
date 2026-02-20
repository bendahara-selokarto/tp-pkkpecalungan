<?php

namespace App\Domains\Wilayah\AnggotaTimPenggerak\UseCases;

use App\Domains\Wilayah\KaderKhusus\UseCases\ListScopedKaderKhususUseCase;

class ListScopedAnggotaDanKaderUseCase
{
    public function __construct(
        private readonly ListScopedAnggotaTimPenggerakUseCase $listScopedAnggotaTimPenggerakUseCase,
        private readonly ListScopedKaderKhususUseCase $listScopedKaderKhususUseCase
    ) {
    }

    public function execute(string $level): array
    {
        return [
            'anggotaTimPenggerak' => $this->listScopedAnggotaTimPenggerakUseCase
                ->execute($level)
                ->sortBy('id')
                ->values(),
            'kaderKhusus' => $this->listScopedKaderKhususUseCase
                ->execute($level)
                ->sortBy('id')
                ->values(),
        ];
    }
}
