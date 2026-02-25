<?php

namespace App\Domains\Wilayah\CatatanKeluarga\Controllers;

use App\Domains\Wilayah\CatatanKeluarga\Models\CatatanKeluarga;
use App\Domains\Wilayah\CatatanKeluarga\Requests\ListCatatanKeluargaRequest;
use App\Domains\Wilayah\CatatanKeluarga\UseCases\ListScopedCatatanKeluargaUseCase;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class DesaCatatanKeluargaController extends Controller
{
    public function __construct(
        private readonly ListScopedCatatanKeluargaUseCase $listScopedCatatanKeluargaUseCase
    ) {
        $this->middleware('scope.role:desa');
    }

    public function index(ListCatatanKeluargaRequest $request): Response
    {
        $this->authorize('viewAny', CatatanKeluarga::class);
        $items = $this->listScopedCatatanKeluargaUseCase->execute(ScopeLevel::DESA->value, $request->perPage());

        return Inertia::render('Desa/CatatanKeluarga/Index', [
            'catatanKeluargaItems' => $items,
            'pagination' => [
                'perPageOptions' => [10, 25, 50],
            ],
            'filters' => [
                'per_page' => $request->perPage(),
            ],
        ]);
    }
}
