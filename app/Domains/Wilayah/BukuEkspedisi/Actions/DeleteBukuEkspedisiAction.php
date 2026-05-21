<?php

namespace App\Domains\Wilayah\BukuEkspedisi\Actions;

use App\Domains\Wilayah\BukuEkspedisi\Models\BukuEkspedisi;
use App\Domains\Wilayah\BukuEkspedisi\Repositories\BukuEkspedisiRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class DeleteBukuEkspedisiAction
{
    public function __construct(
        private readonly BukuEkspedisiRepositoryInterface $bukuEkspedisiRepository
    ) {
    }

    public function execute(BukuEkspedisi $bukuEkspedisi): void
    {
        if ($bukuEkspedisi->file_path) {
            Storage::disk('public')->delete($bukuEkspedisi->file_path);
        }

        $this->bukuEkspedisiRepository->delete($bukuEkspedisi);
    }
}
