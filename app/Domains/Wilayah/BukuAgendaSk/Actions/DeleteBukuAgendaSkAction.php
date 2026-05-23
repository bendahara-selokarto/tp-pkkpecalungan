<?php

namespace App\Domains\Wilayah\BukuAgendaSk\Actions;

use App\Domains\Wilayah\BukuAgendaSk\Models\BukuAgendaSk;
use App\Domains\Wilayah\BukuAgendaSk\Repositories\BukuAgendaSkRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class DeleteBukuAgendaSkAction
{
    public function __construct(
        private readonly BukuAgendaSkRepositoryInterface $bukuAgendaSkRepository
    ) {
    }

    public function execute(BukuAgendaSk $bukuAgendaSk): void
    {
        if ($bukuAgendaSk->file_path) {
            Storage::disk('public')->delete($bukuAgendaSk->file_path);
        }

        $this->bukuAgendaSkRepository->delete($bukuAgendaSk);
    }
}
