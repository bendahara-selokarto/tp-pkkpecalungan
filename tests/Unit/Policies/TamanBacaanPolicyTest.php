<?php

namespace Tests\Unit\Policies;

use App\Domains\Wilayah\Models\Area;
use App\Domains\Wilayah\TamanBacaan\Models\TamanBacaan;
use App\Models\User;
use App\Policies\TamanBacaanPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TamanBacaanPolicyTest extends TestCase
{
    use RefreshDatabase;

    private const ACTIVE_BUDGET_YEAR = 2026;

    #[Test]
    public function admin_desa_hanya_boleh_melihat_taman_bacaan_pada_desanya_sendiri(): void
    {
        Role::create(['name' => 'desa-pokja-ii']);

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);
        $desaB = Area::create(['name' => 'Bandung', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $desaA->id,
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('desa-pokja-ii');

        $milikSendiri = TamanBacaan::create([
            'nama_taman_bacaan' => 'Taman Bacaan Mawar',
            'nama_pengelola' => 'Siti Aminah',
            'jumlah_buku_bacaan' => '200 buku',
            'jenis_buku' => 'Tanaman obat',
            'kategori' => 'Pertanian',
            'jumlah' => '40',
            'level' => 'desa',
            'area_id' => $desaA->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $milikDesaLain = TamanBacaan::create([
            'nama_taman_bacaan' => 'Taman Bacaan Melati',
            'nama_pengelola' => 'Rina Wati',
            'jumlah_buku_bacaan' => '180 buku',
            'jenis_buku' => 'Cerita anak',
            'kategori' => 'Pendidikan',
            'jumlah' => '35',
            'level' => 'desa',
            'area_id' => $desaB->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $policy = app(TamanBacaanPolicy::class);

        $this->assertTrue($policy->view($user, $milikSendiri));
        $this->assertFalse($policy->view($user, $milikDesaLain));
    }

    #[Test]
    public function admin_desa_tidak_boleh_melihat_taman_bacaan_tahun_anggaran_lain(): void
    {
        Role::create(['name' => 'desa-pokja-ii']);

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desa = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $desa->id,
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('desa-pokja-ii');

        $tamanBacaan = TamanBacaan::create([
            'nama_taman_bacaan' => 'Taman Lama',
            'nama_pengelola' => 'Siti Aminah',
            'jumlah_buku_bacaan' => '200 buku',
            'jenis_buku' => 'Tanaman obat',
            'kategori' => 'Pertanian',
            'jumlah' => '40',
            'level' => 'desa',
            'area_id' => $desa->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR - 1,
        ]);

        $policy = app(TamanBacaanPolicy::class);

        $this->assertFalse($policy->view($user, $tamanBacaan));
    }

    #[Test]
    public function admin_kecamatan_tidak_boleh_memperbarui_taman_bacaan_kecamatan_lain(): void
    {
        Role::create(['name' => 'kecamatan-pokja-ii']);

        $kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);

        $user = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $kecamatanA->id,
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('kecamatan-pokja-ii');

        $tamanBacaanLuar = TamanBacaan::create([
            'nama_taman_bacaan' => 'Taman Bacaan Luar',
            'nama_pengelola' => 'Santi',
            'jumlah_buku_bacaan' => '140 buku',
            'jenis_buku' => 'Keterampilan keluarga',
            'kategori' => 'Keterampilan',
            'jumlah' => '20',
            'level' => 'kecamatan',
            'area_id' => $kecamatanB->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $policy = app(TamanBacaanPolicy::class);

        $this->assertFalse($policy->update($user, $tamanBacaanLuar));
    }
}
