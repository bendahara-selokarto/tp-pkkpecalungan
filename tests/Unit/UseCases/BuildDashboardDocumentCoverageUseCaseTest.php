<?php

namespace Tests\Unit\UseCases;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\AgendaSurat\Models\AgendaSurat;
use App\Domains\Wilayah\Dashboard\UseCases\BuildDashboardDocumentCoverageUseCase;
use App\Domains\Wilayah\DataWarga\Models\DataWarga;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BuildDashboardDocumentCoverageUseCaseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
        Role::create(['name' => 'admin-desa']);
        Role::create(['name' => 'desa-sekretaris']);
    }

    public function test_use_case_menghitung_agregasi_per_modul_dan_per_lampiran(): void
    {
        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desa = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $desa->id,
        ]);
        $user->assignRole('admin-desa');

        Activity::create([
            'title' => 'Aktivitas A',
            'level' => 'desa',
            'area_id' => $desa->id,
            'created_by' => $user->id,
            'activity_date' => now()->toDateString(),
            'status' => 'published',
        ]);

        AgendaSurat::create([
            'jenis_surat' => 'masuk',
            'tanggal_terima' => now()->toDateString(),
            'tanggal_surat' => now()->toDateString(),
            'nomor_surat' => 'A-001',
            'asal_surat' => 'Asal',
            'dari' => 'Dari',
            'kepada' => 'Kepada',
            'perihal' => 'Perihal',
            'lampiran' => null,
            'diteruskan_kepada' => null,
            'tembusan' => null,
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $desa->id,
            'created_by' => $user->id,
        ]);

        DataWarga::create([
            'dasawisma' => 'Melati',
            'nama_kepala_keluarga' => 'Kepala A',
            'alamat' => 'Alamat A',
            'jumlah_warga_laki_laki' => 1,
            'jumlah_warga_perempuan' => 1,
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $desa->id,
            'created_by' => $user->id,
        ]);

        $payload = app(BuildDashboardDocumentCoverageUseCase::class)->execute($user);

        $this->assertSame(19, $payload['stats']['total_buku_tracked']);
        $this->assertSame(4, $payload['stats']['buku_terisi']);
        $this->assertSame(15, $payload['stats']['buku_belum_terisi']);
        $this->assertSame(4, $payload['stats']['total_entri_buku']);

        $this->assertSame([4, 0], $payload['charts']['level_distribution']['values']);
        $this->assertSame([0, 1, 0, 0, 1, 1, 1], $payload['charts']['coverage_per_lampiran']['values']);

        $items = collect($payload['charts']['coverage_per_buku']['items'])->keyBy('slug');

        $this->assertSame(1, $items->get('activities')['total']);
        $this->assertSame(1, $items->get('agenda-surat')['total']);
        $this->assertSame(1, $items->get('data-warga')['total']);
        $this->assertSame(1, $items->get('catatan-keluarga')['total']);
    }

    public function test_use_case_cache_dashboard_terinvalidasi_otomatis_saat_data_berubah(): void
    {
        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desa = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $desa->id,
        ]);
        $user->assignRole('admin-desa');

        Activity::create([
            'title' => 'Aktivitas A',
            'level' => 'desa',
            'area_id' => $desa->id,
            'created_by' => $user->id,
            'activity_date' => now()->toDateString(),
            'status' => 'published',
        ]);

        $useCase = app(BuildDashboardDocumentCoverageUseCase::class);

        $firstPayload = $useCase->execute($user);
        $this->assertSame(1, $firstPayload['stats']['total_entri_buku']);

        Activity::create([
            'title' => 'Aktivitas B',
            'level' => 'desa',
            'area_id' => $desa->id,
            'created_by' => $user->id,
            'activity_date' => now()->toDateString(),
            'status' => 'published',
        ]);

        $freshAfterMutationPayload = $useCase->execute($user);
        $this->assertSame(2, $freshAfterMutationPayload['stats']['total_entri_buku']);

        $cachedPayload = $useCase->execute($user);
        $this->assertSame(2, $cachedPayload['stats']['total_entri_buku']);
    }

    public function test_use_case_cache_key_memisahkan_role_signature_dan_filter_signature(): void
    {
        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desa = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $desa->id,
        ]);
        $user->assignRole('admin-desa');

        Activity::create([
            'title' => 'Aktivitas A',
            'level' => 'desa',
            'area_id' => $desa->id,
            'created_by' => $user->id,
            'activity_date' => now()->toDateString(),
            'status' => 'published',
        ]);

        $useCase = app(BuildDashboardDocumentCoverageUseCase::class);
        $defaultContext = [
            'mode' => 'all',
            'level' => 'all',
            'sub_level' => 'all',
            'block' => 'documents',
        ];

        $firstPayload = $useCase->execute($user, $defaultContext);
        $this->assertSame(1, $firstPayload['stats']['total_entri_buku']);

        Activity::create([
            'title' => 'Aktivitas B',
            'level' => 'desa',
            'area_id' => $desa->id,
            'created_by' => $user->id,
            'activity_date' => now()->toDateString(),
            'status' => 'published',
        ]);

        $recalculatedByInvalidationPayload = $useCase->execute($user, $defaultContext);
        $this->assertSame(2, $recalculatedByInvalidationPayload['stats']['total_entri_buku']);

        $user->assignRole('desa-sekretaris');
        $recalculatedByRolePayload = $useCase->execute($user, $defaultContext);
        $this->assertSame(2, $recalculatedByRolePayload['stats']['total_entri_buku']);

        Activity::create([
            'title' => 'Aktivitas C',
            'level' => 'desa',
            'area_id' => $desa->id,
            'created_by' => $user->id,
            'activity_date' => now()->toDateString(),
            'status' => 'published',
        ]);

        $recalculatedByFilterPayload = $useCase->execute($user, [
            'mode' => 'by-level',
            'level' => 'desa',
            'sub_level' => 'all',
            'block' => 'documents',
        ]);
        $this->assertSame(3, $recalculatedByFilterPayload['stats']['total_entri_buku']);
    }
}
