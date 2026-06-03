<?php

namespace App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Controllers;

use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Actions\CreateScopedDataPemanfaatanTanahPekaranganHatinyaPkkAction;
use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Actions\UpdateDataPemanfaatanTanahPekaranganHatinyaPkkAction;
use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Models\DataPemanfaatanTanahPekaranganHatinyaPkk;
use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Repositories\DataPemanfaatanTanahPekaranganHatinyaPkkRepositoryInterface;
use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Requests\ListDataPemanfaatanTanahPekaranganHatinyaPkkRequest;
use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Requests\StoreDataPemanfaatanTanahPekaranganHatinyaPkkRequest;
use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Requests\UpdateDataPemanfaatanTanahPekaranganHatinyaPkkRequest;
use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\UseCases\GetScopedDataPemanfaatanTanahPekaranganHatinyaPkkUseCase;
use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\UseCases\ListScopedDataPemanfaatanTanahPekaranganHatinyaPkkUseCase;
use App\Domains\Wilayah\Dashboard\UseCases\BuildPokjaGeneralChartPayloadUseCase;
use App\Support\Pdf\AcademicChartPdfService;
use App\Support\Pdf\PdfViewFactory;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class KecamatanDataPemanfaatanTanahPekaranganHatinyaPkkController extends Controller
{
    private const BOOK_LABEL_DEFAULT = 'Buku HATINYA PKK';
    private const BOOK_LABEL_BANTU = 'Buku Bantu Pangan';
    private const ROUTE_PREFIX_DEFAULT = 'kecamatan.data-pemanfaatan-tanah-pekarangan-hatinya-pkk';
    private const ROUTE_PREFIX_BANTU = 'kecamatan.buku-bantu-pangan';

    public function __construct(
        private readonly DataPemanfaatanTanahPekaranganHatinyaPkkRepositoryInterface $dataPemanfaatanTanahPekaranganHatinyaPkkRepository,
        private readonly ListScopedDataPemanfaatanTanahPekaranganHatinyaPkkUseCase $listScopedDataPemanfaatanTanahPekaranganHatinyaPkkUseCase,
        private readonly GetScopedDataPemanfaatanTanahPekaranganHatinyaPkkUseCase $getScopedDataPemanfaatanTanahPekaranganHatinyaPkkUseCase,
        private readonly CreateScopedDataPemanfaatanTanahPekaranganHatinyaPkkAction $createScopedDataPemanfaatanTanahPekaranganHatinyaPkkAction,
        private readonly UpdateDataPemanfaatanTanahPekaranganHatinyaPkkAction $updateDataPemanfaatanTanahPekaranganHatinyaPkkAction,
        private readonly AcademicChartPdfService $academicChartPdfService,
        private readonly BuildPokjaGeneralChartPayloadUseCase $buildPokjaGeneralChartPayloadUseCase,
        private readonly PdfViewFactory $pdfViewFactory
    ) {
        $this->middleware('scope.role:kecamatan');
    }

    public function printChartPdf(): \Symfony\Component\HttpFoundation\Response
    {
        $user = auth()->user()?->loadMissing('area');
        $payload = $this->buildPokjaGeneralChartPayloadUseCase->execute($user, 'pokja-iii');
        $colorMap = $this->academicChartPdfService->getPokjaColorMap();

        $chartSvgs = [];
        foreach ($payload as $chart) {
            $chartSvgs[] = $this->academicChartPdfService->generateVerticalBarChartBase64(
                $chart['title'],
                $chart['labels'],
                $chart['series'],
                $colorMap,
                'pokja-iii'
            );
        }

        $pdf = $this->pdfViewFactory->loadView('pdf.pokja_chart_report', [
            'pokjaName' => 'Pokja III',
            'chartSvgs' => $chartSvgs,
            'printedBy' => $user,
        ]);

        return $pdf->stream('laporan-grafik-pokja-iii.pdf');
    }

    public function index(ListDataPemanfaatanTanahPekaranganHatinyaPkkRequest $request): Response
    {
        $this->authorize('viewAny', DataPemanfaatanTanahPekaranganHatinyaPkk::class);
        $isBukuBantu = request()->route()?->getName() === self::ROUTE_PREFIX_BANTU . '.index';
        $items = $this->listScopedDataPemanfaatanTanahPekaranganHatinyaPkkUseCase
            ->execute(ScopeLevel::KECAMATAN->value, $request->perPage())
            ->through(fn (DataPemanfaatanTanahPekaranganHatinyaPkk $item) => [
                'id' => $item->id,
                'kategori_pemanfaatan_lahan' => $item->kategori_pemanfaatan_lahan,
                'komoditi' => $item->komoditi,
                'jumlah_komoditi' => $item->jumlah_komoditi,
                'tahun_anggaran' => $item->tahun_anggaran,
            ]);

        return Inertia::render('Kecamatan/DataPemanfaatanTanahPekaranganHatinyaPkk/Index', [
            'bookLabel' => $isBukuBantu ? self::BOOK_LABEL_BANTU : self::BOOK_LABEL_DEFAULT,
            'baseRouteName' => $isBukuBantu ? self::ROUTE_PREFIX_BANTU : self::ROUTE_PREFIX_DEFAULT,
            'basePath' => $isBukuBantu ? '/kecamatan/buku-bantu-pangan' : '/kecamatan/data-pemanfaatan-tanah-pekarangan-hatinya-pkk',
            'dataPemanfaatanTanahPekaranganHatinyaPkkItems' => $items,
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
        $this->authorize('create', DataPemanfaatanTanahPekaranganHatinyaPkk::class);
        $isBukuBantu = request()->route()?->getName() === self::ROUTE_PREFIX_BANTU . '.create';

        return Inertia::render('Kecamatan/DataPemanfaatanTanahPekaranganHatinyaPkk/Create', [
            'bookLabel' => $isBukuBantu ? self::BOOK_LABEL_BANTU : self::BOOK_LABEL_DEFAULT,
            'baseRouteName' => $isBukuBantu ? self::ROUTE_PREFIX_BANTU : self::ROUTE_PREFIX_DEFAULT,
            'basePath' => $isBukuBantu ? '/kecamatan/buku-bantu-pangan' : '/kecamatan/data-pemanfaatan-tanah-pekarangan-hatinya-pkk',
            'kategoriPemanfaatanLahanOptions' => DataPemanfaatanTanahPekaranganHatinyaPkk::kategoriPemanfaatanLahanOptions(),
        ]);
    }

    public function store(StoreDataPemanfaatanTanahPekaranganHatinyaPkkRequest $request): RedirectResponse
    {
        $this->authorize('create', DataPemanfaatanTanahPekaranganHatinyaPkk::class);
        $this->createScopedDataPemanfaatanTanahPekaranganHatinyaPkkAction->execute($request->validated(), ScopeLevel::KECAMATAN->value);

        $routeName = request()->route()?->getName() === self::ROUTE_PREFIX_BANTU . '.store'
            ? self::ROUTE_PREFIX_BANTU . '.index'
            : self::ROUTE_PREFIX_DEFAULT . '.index';

        return redirect()->route($routeName)->with('success', 'Buku berhasil dibuat');
    }

    public function show(int $id): Response
    {
        $dataPemanfaatanTanahPekaranganHatinyaPkk = $this->getScopedDataPemanfaatanTanahPekaranganHatinyaPkkUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('view', $dataPemanfaatanTanahPekaranganHatinyaPkk);
        $isBukuBantu = request()->route()?->getName() === self::ROUTE_PREFIX_BANTU . '.show';

        return Inertia::render('Kecamatan/DataPemanfaatanTanahPekaranganHatinyaPkk/Show', [
            'bookLabel' => $isBukuBantu ? self::BOOK_LABEL_BANTU : self::BOOK_LABEL_DEFAULT,
            'baseRouteName' => $isBukuBantu ? self::ROUTE_PREFIX_BANTU : self::ROUTE_PREFIX_DEFAULT,
            'basePath' => $isBukuBantu ? '/kecamatan/buku-bantu-pangan' : '/kecamatan/data-pemanfaatan-tanah-pekarangan-hatinya-pkk',
            'dataPemanfaatanTanahPekaranganHatinyaPkk' => [
                'id' => $dataPemanfaatanTanahPekaranganHatinyaPkk->id,
                'kategori_pemanfaatan_lahan' => $dataPemanfaatanTanahPekaranganHatinyaPkk->kategori_pemanfaatan_lahan,
                'komoditi' => $dataPemanfaatanTanahPekaranganHatinyaPkk->komoditi,
                'jumlah_komoditi' => $dataPemanfaatanTanahPekaranganHatinyaPkk->jumlah_komoditi,
                'tahun_anggaran' => $dataPemanfaatanTanahPekaranganHatinyaPkk->tahun_anggaran,
            ],
        ]);
    }

    public function edit(int $id): Response
    {
        $dataPemanfaatanTanahPekaranganHatinyaPkk = $this->getScopedDataPemanfaatanTanahPekaranganHatinyaPkkUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('update', $dataPemanfaatanTanahPekaranganHatinyaPkk);
        $isBukuBantu = request()->route()?->getName() === self::ROUTE_PREFIX_BANTU . '.edit';

        return Inertia::render('Kecamatan/DataPemanfaatanTanahPekaranganHatinyaPkk/Edit', [
            'bookLabel' => $isBukuBantu ? self::BOOK_LABEL_BANTU : self::BOOK_LABEL_DEFAULT,
            'baseRouteName' => $isBukuBantu ? self::ROUTE_PREFIX_BANTU : self::ROUTE_PREFIX_DEFAULT,
            'basePath' => $isBukuBantu ? '/kecamatan/buku-bantu-pangan' : '/kecamatan/data-pemanfaatan-tanah-pekarangan-hatinya-pkk',
            'dataPemanfaatanTanahPekaranganHatinyaPkk' => [
                'id' => $dataPemanfaatanTanahPekaranganHatinyaPkk->id,
                'kategori_pemanfaatan_lahan' => $dataPemanfaatanTanahPekaranganHatinyaPkk->kategori_pemanfaatan_lahan,
                'komoditi' => $dataPemanfaatanTanahPekaranganHatinyaPkk->komoditi,
                'jumlah_komoditi' => $dataPemanfaatanTanahPekaranganHatinyaPkk->jumlah_komoditi,
                'tahun_anggaran' => $dataPemanfaatanTanahPekaranganHatinyaPkk->tahun_anggaran,
            ],
            'kategoriPemanfaatanLahanOptions' => DataPemanfaatanTanahPekaranganHatinyaPkk::kategoriPemanfaatanLahanOptions(),
        ]);
    }

    public function update(UpdateDataPemanfaatanTanahPekaranganHatinyaPkkRequest $request, int $id): RedirectResponse
    {
        $dataPemanfaatanTanahPekaranganHatinyaPkk = $this->getScopedDataPemanfaatanTanahPekaranganHatinyaPkkUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('update', $dataPemanfaatanTanahPekaranganHatinyaPkk);
        $this->updateDataPemanfaatanTanahPekaranganHatinyaPkkAction->execute($dataPemanfaatanTanahPekaranganHatinyaPkk, $request->validated());

        $routeName = request()->route()?->getName() === self::ROUTE_PREFIX_BANTU . '.update'
            ? self::ROUTE_PREFIX_BANTU . '.index'
            : self::ROUTE_PREFIX_DEFAULT . '.index';

        return redirect()->route($routeName)->with('success', 'Buku berhasil diperbarui');
    }

    public function destroy(int $id): RedirectResponse
    {
        $dataPemanfaatanTanahPekaranganHatinyaPkk = $this->getScopedDataPemanfaatanTanahPekaranganHatinyaPkkUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('delete', $dataPemanfaatanTanahPekaranganHatinyaPkk);
        $this->dataPemanfaatanTanahPekaranganHatinyaPkkRepository->delete($dataPemanfaatanTanahPekaranganHatinyaPkk);

        $routeName = request()->route()?->getName() === self::ROUTE_PREFIX_BANTU . '.destroy'
            ? self::ROUTE_PREFIX_BANTU . '.index'
            : self::ROUTE_PREFIX_DEFAULT . '.index';

        return redirect()->route($routeName)->with('success', 'Buku berhasil dihapus');
    }
}
