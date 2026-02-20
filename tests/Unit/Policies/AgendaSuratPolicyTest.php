<?php

namespace Tests\Unit\Policies;

use App\Domains\Wilayah\AgendaSurat\Models\AgendaSurat;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use App\Policies\AgendaSuratPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AgendaSuratPolicyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_desa_hanya_boleh_melihat_agenda_surat_pada_desanya_sendiri()
    {
        Role::create(['name' => 'admin-desa']);

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);
        $desaB = Area::create(['name' => 'Bandung', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $desaA->id]);
        $user->assignRole('admin-desa');

        $milikSendiri = AgendaSurat::create([
            'jenis_surat' => 'masuk',
            'tanggal_terima' => '2026-02-20',
            'tanggal_surat' => '2026-02-19',
            'nomor_surat' => '001/DSA/II/2026',
            'asal_surat' => 'Kecamatan',
            'dari' => 'Sekretariat Kecamatan',
            'kepada' => null,
            'perihal' => 'Undangan',
            'lampiran' => null,
            'diteruskan_kepada' => null,
            'tembusan' => null,
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $desaA->id,
            'created_by' => $user->id,
        ]);

        $milikDesaLain = AgendaSurat::create([
            'jenis_surat' => 'masuk',
            'tanggal_terima' => '2026-02-20',
            'tanggal_surat' => '2026-02-19',
            'nomor_surat' => '009/DSB/II/2026',
            'asal_surat' => 'Kecamatan',
            'dari' => 'Sekretariat Kecamatan',
            'kepada' => null,
            'perihal' => 'Undangan',
            'lampiran' => null,
            'diteruskan_kepada' => null,
            'tembusan' => null,
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $desaB->id,
            'created_by' => $user->id,
        ]);

        $policy = app(AgendaSuratPolicy::class);

        $this->assertTrue($policy->view($user, $milikSendiri));
        $this->assertFalse($policy->view($user, $milikDesaLain));
    }

    #[Test]
    public function admin_kecamatan_tidak_boleh_memperbarui_agenda_surat_kecamatan_lain()
    {
        Role::create(['name' => 'admin-kecamatan']);

        $kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);

        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $kecamatanA->id]);
        $user->assignRole('admin-kecamatan');

        $agendaLuar = AgendaSurat::create([
            'jenis_surat' => 'keluar',
            'tanggal_terima' => null,
            'tanggal_surat' => '2026-02-21',
            'nomor_surat' => '002/KCB/II/2026',
            'asal_surat' => null,
            'dari' => null,
            'kepada' => 'Kabupaten',
            'perihal' => 'Laporan',
            'lampiran' => null,
            'diteruskan_kepada' => null,
            'tembusan' => 'Arsip',
            'keterangan' => null,
            'level' => 'kecamatan',
            'area_id' => $kecamatanB->id,
            'created_by' => $user->id,
        ]);

        $policy = app(AgendaSuratPolicy::class);

        $this->assertFalse($policy->update($user, $agendaLuar));
    }
}
