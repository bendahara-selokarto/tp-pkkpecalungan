<?php

namespace App\Domains\Wilayah\CatatanKeluarga\Controllers;

use App\Domains\Wilayah\CatatanKeluarga\Models\CatatanKeluarga;
use App\Domains\Wilayah\CatatanKeluarga\UseCases\ListScopedCatatanKeluargaUseCase;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class KecamatanCatatanKeluargaController extends Controller
{
    public function __construct(
        private readonly ListScopedCatatanKeluargaUseCase $listScopedCatatanKeluargaUseCase
    ) {
        $this->middleware('scope.role:kecamatan');
    }

    public function index(): Response
    {
        $this->authorize('viewAny', CatatanKeluarga::class);
        $items = $this->listScopedCatatanKeluargaUseCase->execute(ScopeLevel::KECAMATAN->value);

        return Inertia::render('Kecamatan/CatatanKeluarga/Index', [
            'catatanKeluargaItems' => $items->values(),
        ]);
    }
}

