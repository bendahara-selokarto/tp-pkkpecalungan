<?php

namespace App\Domains\Wilayah\SimulasiPenyuluhan\Controllers;

use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\SimulasiPenyuluhan\Actions\CreateScopedSimulasiPenyuluhanAction;
use App\Domains\Wilayah\SimulasiPenyuluhan\Actions\UpdateSimulasiPenyuluhanAction;
use App\Domains\Wilayah\SimulasiPenyuluhan\Models\SimulasiPenyuluhan;
use App\Domains\Wilayah\SimulasiPenyuluhan\Repositories\SimulasiPenyuluhanRepositoryInterface;
use App\Domains\Wilayah\SimulasiPenyuluhan\Requests\ListSimulasiPenyuluhanRequest;
use App\Domains\Wilayah\SimulasiPenyuluhan\Requests\StoreSimulasiPenyuluhanRequest;
use App\Domains\Wilayah\SimulasiPenyuluhan\Requests\UpdateSimulasiPenyuluhanRequest;
use App\Domains\Wilayah\SimulasiPenyuluhan\UseCases\GetScopedSimulasiPenyuluhanUseCase;
use App\Domains\Wilayah\SimulasiPenyuluhan\UseCases\ListScopedSimulasiPenyuluhanUseCase;
use App\Domains\Wilayah\Dashboard\UseCases\BuildPokjaGeneralChartPayloadUseCase;
use App\Support\Pdf\AcademicChartPdfService;
use App\Support\Pdf\PdfViewFactory;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DesaSimulasiPenyuluhanController extends Controller
{
    public function __construct(
        private readonly SimulasiPenyuluhanRepositoryInterface $simulasiPenyuluhanRepository,
        private readonly ListScopedSimulasiPenyuluhanUseCase $listScopedSimulasiPenyuluhanUseCase,
        private readonly GetScopedSimulasiPenyuluhanUseCase $getScopedSimulasiPenyuluhanUseCase,
        private readonly CreateScopedSimulasiPenyuluhanAction $createScopedSimulasiPenyuluhanAction,
        private readonly UpdateSimulasiPenyuluhanAction $updateSimulasiPenyuluhanAction,
        private readonly AcademicChartPdfService $academicChartPdfService,
        private readonly BuildPokjaGeneralChartPayloadUseCase $buildPokjaGeneralChartPayloadUseCase,
        private readonly PdfViewFactory $pdfViewFactory
    ) {
        $this->middleware('scope.role:desa');
    }

    public function printChartPdf(): \Symfony\Component\HttpFoundation\Response
    {
        $user = auth()->user()?->loadMissing('area');
        $payload = $this->buildPokjaGeneralChartPayloadUseCase->execute($user, 'pokja-i');
        $colorMap = $this->academicChartPdfService->getPokjaColorMap();

        $chartSvgs = [];
        foreach ($payload as $chart) {
            $chartSvgs[] = $this->academicChartPdfService->generateVerticalBarChartBase64(
                $chart['title'],
                $chart['labels'],
                $chart['series'],
                $colorMap,
                'pokja-i'
            );
        }

        $pdf = $this->pdfViewFactory->loadView('pdf.pokja_chart_report', [
            'pokjaName' => 'Pokja I',
            'chartSvgs' => $chartSvgs,
            'printedBy' => $user,
        ]);

        return $pdf->stream('laporan-grafik-pokja-i.pdf');
    }

    public function index(ListSimulasiPenyuluhanRequest $request): Response
    {
        $this->authorize('viewAny', SimulasiPenyuluhan::class);
        $items = $this->listScopedSimulasiPenyuluhanUseCase->execute(ScopeLevel::DESA->value, $request->perPage());

        return Inertia::render('Desa/SimulasiPenyuluhan/Index', [
            'simulasiPenyuluhanItems' => $items->through(fn (SimulasiPenyuluhan $item) => [
                'id' => $item->id,
                'nama_kegiatan' => $item->nama_kegiatan,
                'jenis_simulasi_penyuluhan' => $item->jenis_simulasi_penyuluhan,
                'jumlah_kelompok' => $item->jumlah_kelompok,
                'jumlah_sosialisasi' => $item->jumlah_sosialisasi,
                'jumlah_kader_l' => $item->jumlah_kader_l,
                'jumlah_kader_p' => $item->jumlah_kader_p,
                'keterangan' => $item->keterangan,
                'tahun_anggaran' => $item->tahun_anggaran,
            ]),
            'pagination' => [
                'perPageOptions' => [10, 25, 50],
            ],
            'filters' => [
                'per_page' => $request->perPage(),
                'tahun_anggaran' => (int) $request->user()->active_budget_year,
            ],
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', SimulasiPenyuluhan::class);

        return Inertia::render('Desa/SimulasiPenyuluhan/Create');
    }

    public function store(StoreSimulasiPenyuluhanRequest $request): RedirectResponse
    {
        $this->authorize('create', SimulasiPenyuluhan::class);
        $this->createScopedSimulasiPenyuluhanAction->execute($request->validated(), ScopeLevel::DESA->value);

        return redirect()->route('desa.simulasi-penyuluhan.index')->with('success', 'Data isian kelompok simulasi dan penyuluhan berhasil dibuat');
    }

    public function show(int $id): Response
    {
        $simulasiPenyuluhan = $this->getScopedSimulasiPenyuluhanUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('view', $simulasiPenyuluhan);

        return Inertia::render('Desa/SimulasiPenyuluhan/Show', [
            'simulasiPenyuluhan' => [
                'id' => $simulasiPenyuluhan->id,
                'nama_kegiatan' => $simulasiPenyuluhan->nama_kegiatan,
                'jenis_simulasi_penyuluhan' => $simulasiPenyuluhan->jenis_simulasi_penyuluhan,
                'jumlah_kelompok' => $simulasiPenyuluhan->jumlah_kelompok,
                'jumlah_sosialisasi' => $simulasiPenyuluhan->jumlah_sosialisasi,
                'jumlah_kader_l' => $simulasiPenyuluhan->jumlah_kader_l,
                'jumlah_kader_p' => $simulasiPenyuluhan->jumlah_kader_p,
                'keterangan' => $simulasiPenyuluhan->keterangan,
                'tahun_anggaran' => $simulasiPenyuluhan->tahun_anggaran,
            ],
        ]);
    }

    public function edit(int $id): Response
    {
        $simulasiPenyuluhan = $this->getScopedSimulasiPenyuluhanUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('update', $simulasiPenyuluhan);

        return Inertia::render('Desa/SimulasiPenyuluhan/Edit', [
            'simulasiPenyuluhan' => [
                'id' => $simulasiPenyuluhan->id,
                'nama_kegiatan' => $simulasiPenyuluhan->nama_kegiatan,
                'jenis_simulasi_penyuluhan' => $simulasiPenyuluhan->jenis_simulasi_penyuluhan,
                'jumlah_kelompok' => $simulasiPenyuluhan->jumlah_kelompok,
                'jumlah_sosialisasi' => $simulasiPenyuluhan->jumlah_sosialisasi,
                'jumlah_kader_l' => $simulasiPenyuluhan->jumlah_kader_l,
                'jumlah_kader_p' => $simulasiPenyuluhan->jumlah_kader_p,
                'keterangan' => $simulasiPenyuluhan->keterangan,
                'tahun_anggaran' => $simulasiPenyuluhan->tahun_anggaran,
            ],
        ]);
    }

    public function update(UpdateSimulasiPenyuluhanRequest $request, int $id): RedirectResponse
    {
        $simulasiPenyuluhan = $this->getScopedSimulasiPenyuluhanUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('update', $simulasiPenyuluhan);
        $this->updateSimulasiPenyuluhanAction->execute($simulasiPenyuluhan, $request->validated());

        return redirect()->route('desa.simulasi-penyuluhan.index')->with('success', 'Data isian kelompok simulasi dan penyuluhan berhasil diperbarui');
    }

    public function destroy(int $id): RedirectResponse
    {
        $simulasiPenyuluhan = $this->getScopedSimulasiPenyuluhanUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('delete', $simulasiPenyuluhan);
        $this->simulasiPenyuluhanRepository->delete($simulasiPenyuluhan);

        return redirect()->route('desa.simulasi-penyuluhan.index')->with('success', 'Data isian kelompok simulasi dan penyuluhan berhasil dihapus');
    }
}
