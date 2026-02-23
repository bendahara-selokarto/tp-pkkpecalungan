<?php

namespace Tests\Unit\Policies;

use App\Domains\Wilayah\BukuKeuangan\Models\BukuKeuangan;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use App\Policies\BukuKeuanganPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BukuKeuanganPolicyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_desa_hanya_boleh_melihat_buku_keuangan_pada_desanya_sendiri(): void
    {
        Role::create(['name' => 'admin-desa']);

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);
        $desaB = Area::create(['name' => 'Bandung', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $desaA->id]);
        $user->assignRole('admin-desa');

        $milikSendiri = BukuKeuangan::create([
            'transaction_date' => '2026-02-11',
            'source' => 'kas_tunai',
            'description' => 'Iuran anggota',
            'reference_number' => 'BK-601',
            'entry_type' => 'pemasukan',
            'amount' => 2000000,
            'level' => 'desa',
            'area_id' => $desaA->id,
            'created_by' => $user->id,
        ]);

        $milikDesaLain = BukuKeuangan::create([
            'transaction_date' => '2026-02-12',
            'source' => 'bank',
            'description' => 'Biaya operasional desa lain',
            'reference_number' => 'BK-602',
            'entry_type' => 'pengeluaran',
            'amount' => 3000000,
            'level' => 'desa',
            'area_id' => $desaB->id,
            'created_by' => $user->id,
        ]);

        $policy = app(BukuKeuanganPolicy::class);

        $this->assertTrue($policy->view($user, $milikSendiri));
        $this->assertFalse($policy->view($user, $milikDesaLain));
    }

    #[Test]
    public function admin_kecamatan_tidak_boleh_memperbarui_buku_keuangan_kecamatan_lain(): void
    {
        Role::create(['name' => 'admin-kecamatan']);

        $kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);

        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $kecamatanA->id]);
        $user->assignRole('admin-kecamatan');

        $entryLuar = BukuKeuangan::create([
            'transaction_date' => '2026-02-13',
            'source' => 'bank',
            'description' => 'Belanja luar area',
            'reference_number' => 'BK-603',
            'entry_type' => 'pengeluaran',
            'amount' => 5000000,
            'level' => 'kecamatan',
            'area_id' => $kecamatanB->id,
            'created_by' => $user->id,
        ]);

        $policy = app(BukuKeuanganPolicy::class);

        $this->assertFalse($policy->update($user, $entryLuar));
    }
}
