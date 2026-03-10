<?php

namespace Database\Seeders;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class WilayahMissingDomainSeeder extends Seeder
{
    private const TARGET_KECAMATAN = 'Pecalungan';

    public function run(): void
    {
        $kecamatanArea = Area::query()
            ->where('level', 'kecamatan')
            ->where('name', self::TARGET_KECAMATAN)
            ->first();

        if (! $kecamatanArea) {
            $kecamatanArea = Area::query()
                ->where('level', 'kecamatan')
                ->orderBy('id')
                ->first();
        }

        if (! $kecamatanArea) {
            $this->command?->warn('Seeder domain tambahan dibatalkan: area kecamatan tidak ditemukan.');

            return;
        }

        $contexts = $this->buildContexts($kecamatanArea);
        $areaIds = $contexts->pluck('area_id')->all();

        if ($areaIds === []) {
            $this->command?->warn('Seeder domain tambahan dibatalkan: tidak ada context area.');

            return;
        }

        $this->purgeExistingData($areaIds);

        $faker = FakerFactory::create('id_ID');

        foreach ($contexts as $context) {
            $this->seedAnggotaPokja($faker, $context);
            $this->seedBkl($faker, $context);
            $this->seedBkr($faker, $context);
            $this->seedPrestasiLomba($faker, $context);
            $this->seedProgramPrioritas($faker, $context);
            $this->seedPilotProjectKeluargaSehat($faker, $context);
            $this->seedPilotProjectNaskahPelaporan($faker, $context);
        }

        $this->command?->info(sprintf(
            'WilayahMissingDomainSeeder selesai. Area terisi: %d.',
            $contexts->count()
        ));
    }

    private function buildContexts(Area $kecamatanArea)
    {
        $contexts = collect();

        $desaAreas = Area::query()
            ->where('level', 'desa')
            ->where('parent_id', $kecamatanArea->id)
            ->orderBy('id')
            ->get();

        $desaNames = $desaAreas
            ->pluck('name')
            ->filter(static fn ($value): bool => is_string($value) && trim($value) !== '')
            ->map(static fn (string $value): string => trim($value))
            ->values()
            ->all();

        $contexts->push([
            'level' => 'kecamatan',
            'area_id' => (int) $kecamatanArea->id,
            'area_name' => (string) $kecamatanArea->name,
            'kecamatan_name' => (string) $kecamatanArea->name,
            'desa_reference_names' => $desaNames !== [] ? $desaNames : [(string) $kecamatanArea->name],
            'creator_id' => $this->resolveCreatorId('kecamatan', (int) $kecamatanArea->id, (string) $kecamatanArea->name),
        ]);

        foreach ($desaAreas as $desaArea) {
            $desaName = (string) $desaArea->name;

            $contexts->push([
                'level' => 'desa',
                'area_id' => (int) $desaArea->id,
                'area_name' => $desaName,
                'kecamatan_name' => (string) $kecamatanArea->name,
                'desa_reference_names' => [$desaName],
                'creator_id' => $this->resolveCreatorId('desa', (int) $desaArea->id, $desaName),
            ]);
        }

        return $contexts;
    }

    private function resolveCreatorId(string $level, int $areaId, string $areaName): int
    {
        $user = User::query()
            ->where('scope', $level)
            ->where('area_id', $areaId)
            ->first();

        if ($user) {
            return (int) $user->id;
        }

        $wilayahSlug = str($areaName)
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', '-')
            ->trim('-')
            ->value();
        $email = sprintf('seeder-%s+%s@gmail.com', $level, $wilayahSlug !== '' ? $wilayahSlug : $areaId);

        $user = User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => sprintf('Seeder %s %s', ucfirst($level), $areaName),
                'password' => Hash::make('password123'),
                'scope' => $level,
                'area_id' => $areaId,
                'active_budget_year' => $this->defaultBudgetYear(),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );

        $roleName = $level === 'desa' ? 'desa-sekretaris' : 'kecamatan-sekretaris';
        Role::firstOrCreate(['name' => $roleName]);
        $user->syncRoles([$roleName]);

        return (int) $user->id;
    }

    private function purgeExistingData(array $areaIds): void
    {
        $tables = [
            'pilot_project_naskah_pelaporan_attachments',
            'pilot_project_keluarga_sehat_values',
            'pilot_project_naskah_pelaporan_reports',
            'pilot_project_keluarga_sehat_reports',
            'program_prioritas',
            'prestasi_lombas',
            'bkrs',
            'bkls',
            'anggota_pokjas',
        ];

        foreach ($tables as $table) {
            DB::table($table)
                ->whereIn('area_id', $areaIds)
                ->delete();
        }
    }

    private function seedAnggotaPokja(\Faker\Generator $faker, array $context): void
    {
        $count = $this->countFor($context['level'], 6, 12, 12, 24);
        $rows = [];
        $jabatanList = ['Ketua', 'Wakil Ketua', 'Sekretaris', 'Bendahara', 'Anggota'];
        $pendidikanList = ['SMA', 'D3', 'S1', 'S2'];
        $pekerjaanList = ['Ibu Rumah Tangga', 'Wiraswasta', 'Guru', 'Perangkat Desa', 'Kader PKK'];
        $pokjaList = ['Pokja I', 'Pokja II', 'Pokja III', 'Pokja IV'];

        for ($i = 1; $i <= $count; $i++) {
            $rows[] = [
                'nama' => $faker->name(),
                'jabatan' => $faker->randomElement($jabatanList),
                'jenis_kelamin' => $faker->randomElement(['L', 'P']),
                'tempat_lahir' => $faker->randomElement(['Batang', 'Pekalongan', 'Kendal']),
                'tanggal_lahir' => $faker->dateTimeBetween('-58 years', '-22 years')->format('Y-m-d'),
                'status_perkawinan' => $faker->boolean(85) ? 'kawin' : 'tidak_kawin',
                'alamat' => $this->randomAddress($faker, $context),
                'pendidikan' => $faker->randomElement($pendidikanList),
                'pekerjaan' => $faker->randomElement($pekerjaanList),
                'keterangan' => $faker->boolean(70) ? null : 'Pendataan anggota pokja tingkat '.$context['level'],
                'pokja' => $faker->randomElement($pokjaList),
                'tahun_anggaran' => $this->defaultBudgetYear(),
                'level' => $context['level'],
                'area_id' => $context['area_id'],
                'created_by' => $context['creator_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('anggota_pokjas')->insert($rows);
    }

    private function seedBkl(\Faker\Generator $faker, array $context): void
    {
        $count = $this->countFor($context['level'], 2, 6, 4, 10);
        $rows = [];
        $desaPool = is_array($context['desa_reference_names']) ? $context['desa_reference_names'] : [$context['area_name']];

        for ($i = 1; $i <= $count; $i++) {
            $rows[] = [
                'desa' => $faker->randomElement($desaPool),
                'nama_bkl' => 'Kelompok BKL '.$context['area_name'].' '.$i,
                'no_tgl_sk' => sprintf('SK-BKL/%s/%03d/%d', strtoupper($context['level']), $i, (int) date('Y')),
                'nama_ketua_kelompok' => $faker->name('female'),
                'jumlah_anggota' => random_int(10, 60),
                'kegiatan' => $faker->sentence(12),
                'tahun_anggaran' => $this->defaultBudgetYear(),
                'level' => $context['level'],
                'area_id' => $context['area_id'],
                'created_by' => $context['creator_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('bkls')->insert($rows);
    }

    private function seedBkr(\Faker\Generator $faker, array $context): void
    {
        $count = $this->countFor($context['level'], 2, 6, 4, 10);
        $rows = [];
        $desaPool = is_array($context['desa_reference_names']) ? $context['desa_reference_names'] : [$context['area_name']];

        for ($i = 1; $i <= $count; $i++) {
            $rows[] = [
                'desa' => $faker->randomElement($desaPool),
                'nama_bkr' => 'Kelompok BKR '.$context['area_name'].' '.$i,
                'no_tgl_sk' => sprintf('SK-BKR/%s/%03d/%d', strtoupper($context['level']), $i, (int) date('Y')),
                'nama_ketua_kelompok' => $faker->name('female'),
                'jumlah_anggota' => random_int(10, 55),
                'kegiatan' => $faker->sentence(12),
                'tahun_anggaran' => $this->defaultBudgetYear(),
                'level' => $context['level'],
                'area_id' => $context['area_id'],
                'created_by' => $context['creator_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('bkrs')->insert($rows);
    }

    private function seedPrestasiLomba(\Faker\Generator $faker, array $context): void
    {
        $count = $this->countFor($context['level'], 3, 8, 6, 12);
        $rows = [];
        $jenisLombaList = [
            'Lomba Administrasi PKK',
            'Lomba Hatinya PKK',
            'Lomba Dasawisma',
            'Lomba Posyandu',
            'Lomba Tertib Administrasi',
        ];

        for ($i = 1; $i <= $count; $i++) {
            $prestasiFlags = $this->atLeastOneTrue([
                'prestasi_kecamatan',
                'prestasi_kabupaten',
                'prestasi_provinsi',
                'prestasi_nasional',
            ]);

            $rows[] = [
                'tahun' => $tahun = random_int((int) date('Y') - 4, (int) date('Y')),
                'jenis_lomba' => $faker->randomElement($jenisLombaList),
                'lokasi' => $context['area_name'].', Kab. Batang',
                'prestasi_kecamatan' => $prestasiFlags['prestasi_kecamatan'],
                'prestasi_kabupaten' => $prestasiFlags['prestasi_kabupaten'],
                'prestasi_provinsi' => $prestasiFlags['prestasi_provinsi'],
                'prestasi_nasional' => $prestasiFlags['prestasi_nasional'],
                'keterangan' => $faker->boolean(65) ? null : 'Prestasi lomba kader PKK '.$context['area_name'],
                'tahun_anggaran' => $this->resolveBudgetYear(year: $tahun),
                'level' => $context['level'],
                'area_id' => $context['area_id'],
                'created_by' => $context['creator_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('prestasi_lombas')->insert($rows);
    }

    private function seedProgramPrioritas(\Faker\Generator $faker, array $context): void
    {
        $count = $this->countFor($context['level'], 4, 10, 6, 14);
        $programList = ['Pokja I', 'Pokja II', 'Pokja III', 'Pokja IV'];
        $prioritasList = [
            'Penguatan administrasi',
            'Pemberdayaan ekonomi keluarga',
            'Kesehatan keluarga',
            'Ketahanan pangan',
            'Pendidikan keluarga',
        ];

        for ($i = 1; $i <= $count; $i++) {
            $monthlyJadwalFlags = $this->atLeastOneTrue(array_map(
                static fn (int $month): string => "jadwal_bulan_{$month}",
                range(1, 12)
            ));
            $quarterJadwalFlags = $this->deriveQuarterFlagsFromMonthly($monthlyJadwalFlags);
            $sumberDanaFlags = $this->atLeastOneTrue([
                'sumber_dana_pusat',
                'sumber_dana_apbd',
                'sumber_dana_swd',
                'sumber_dana_bant',
            ]);

            $programPrioritasId = DB::table('program_prioritas')->insertGetId([
                'program' => $faker->randomElement($programList),
                'prioritas_program' => $faker->randomElement($prioritasList),
                'kegiatan' => $faker->sentence(14),
                'sasaran_target' => $faker->sentence(10),
                'jadwal_bulan_1' => $monthlyJadwalFlags['jadwal_bulan_1'],
                'jadwal_bulan_2' => $monthlyJadwalFlags['jadwal_bulan_2'],
                'jadwal_bulan_3' => $monthlyJadwalFlags['jadwal_bulan_3'],
                'jadwal_bulan_4' => $monthlyJadwalFlags['jadwal_bulan_4'],
                'jadwal_bulan_5' => $monthlyJadwalFlags['jadwal_bulan_5'],
                'jadwal_bulan_6' => $monthlyJadwalFlags['jadwal_bulan_6'],
                'jadwal_bulan_7' => $monthlyJadwalFlags['jadwal_bulan_7'],
                'jadwal_bulan_8' => $monthlyJadwalFlags['jadwal_bulan_8'],
                'jadwal_bulan_9' => $monthlyJadwalFlags['jadwal_bulan_9'],
                'jadwal_bulan_10' => $monthlyJadwalFlags['jadwal_bulan_10'],
                'jadwal_bulan_11' => $monthlyJadwalFlags['jadwal_bulan_11'],
                'jadwal_bulan_12' => $monthlyJadwalFlags['jadwal_bulan_12'],
                'jadwal_i' => $quarterJadwalFlags['jadwal_i'],
                'jadwal_ii' => $quarterJadwalFlags['jadwal_ii'],
                'jadwal_iii' => $quarterJadwalFlags['jadwal_iii'],
                'jadwal_iv' => $quarterJadwalFlags['jadwal_iv'],
                'sumber_dana_pusat' => $sumberDanaFlags['sumber_dana_pusat'],
                'sumber_dana_apbd' => $sumberDanaFlags['sumber_dana_apbd'],
                'sumber_dana_swd' => $sumberDanaFlags['sumber_dana_swd'],
                'sumber_dana_bant' => $sumberDanaFlags['sumber_dana_bant'],
                'keterangan' => $faker->boolean(70) ? null : 'Program prioritas '.$context['area_name'].' periode berjalan',
                'tahun_anggaran' => $this->defaultBudgetYear(),
                'level' => $context['level'],
                'area_id' => $context['area_id'],
                'created_by' => $context['creator_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->seedProgramPrioritasNormalized(
                programPrioritasId: $programPrioritasId,
                monthlyJadwalFlags: $monthlyJadwalFlags,
                sumberDanaFlags: $sumberDanaFlags,
                context: $context
            );
        }
    }

    /**
     * @param  array<string, bool>  $monthlyFlags
     * @return array{jadwal_i: bool, jadwal_ii: bool, jadwal_iii: bool, jadwal_iv: bool}
     */
    private function deriveQuarterFlagsFromMonthly(array $monthlyFlags): array
    {
        return [
            'jadwal_i' => ($monthlyFlags['jadwal_bulan_1'] ?? false)
                || ($monthlyFlags['jadwal_bulan_2'] ?? false)
                || ($monthlyFlags['jadwal_bulan_3'] ?? false),
            'jadwal_ii' => ($monthlyFlags['jadwal_bulan_4'] ?? false)
                || ($monthlyFlags['jadwal_bulan_5'] ?? false)
                || ($monthlyFlags['jadwal_bulan_6'] ?? false),
            'jadwal_iii' => ($monthlyFlags['jadwal_bulan_7'] ?? false)
                || ($monthlyFlags['jadwal_bulan_8'] ?? false)
                || ($monthlyFlags['jadwal_bulan_9'] ?? false),
            'jadwal_iv' => ($monthlyFlags['jadwal_bulan_10'] ?? false)
                || ($monthlyFlags['jadwal_bulan_11'] ?? false)
                || ($monthlyFlags['jadwal_bulan_12'] ?? false),
        ];
    }

    /**
     * @param  array<string, bool>  $monthlyJadwalFlags
     * @param  array<string, bool>  $sumberDanaFlags
     * @param  array<string, mixed>  $context
     */
    private function seedProgramPrioritasNormalized(
        int $programPrioritasId,
        array $monthlyJadwalFlags,
        array $sumberDanaFlags,
        array $context
    ): void {
        $now = now();
        $jadwalRows = [];

        for ($month = 1; $month <= 12; $month++) {
            $key = "jadwal_bulan_{$month}";
            if (! ($monthlyJadwalFlags[$key] ?? false)) {
                continue;
            }

            $jadwalRows[] = [
                'program_prioritas_id' => $programPrioritasId,
                'month' => $month,
                'level' => $context['level'],
                'area_id' => $context['area_id'],
                'created_by' => $context['creator_id'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($jadwalRows !== []) {
            DB::table('program_prioritas_jadwal_months')->insert($jadwalRows);
        }

        $sourceMap = [
            'pusat' => $sumberDanaFlags['sumber_dana_pusat'] ?? false,
            'apbd' => $sumberDanaFlags['sumber_dana_apbd'] ?? false,
            'swd' => $sumberDanaFlags['sumber_dana_swd'] ?? false,
            'bant' => $sumberDanaFlags['sumber_dana_bant'] ?? false,
        ];

        $sourceRows = [];
        foreach ($sourceMap as $source => $flag) {
            if (! $flag) {
                continue;
            }

            $sourceRows[] = [
                'program_prioritas_id' => $programPrioritasId,
                'source' => $source,
                'level' => $context['level'],
                'area_id' => $context['area_id'],
                'created_by' => $context['creator_id'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($sourceRows !== []) {
            DB::table('program_prioritas_funding_sources')->insert($sourceRows);
        }
    }

    private function seedPilotProjectKeluargaSehat(\Faker\Generator $faker, array $context): void
    {
        $tahunAwal = 2021;
        $tahunAkhir = 2024;

        $reportId = DB::table('pilot_project_keluarga_sehat_reports')->insertGetId([
            'judul_laporan' => 'Laporan Pilot Project Keluarga Sehat '.$context['area_name'],
            'dasar_hukum' => 'Peraturan dan pedoman TP PKK yang berlaku.',
            'pendahuluan' => 'Pendahuluan pelaksanaan pilot project tingkat '.$context['level'].'.',
            'maksud_tujuan' => 'Meningkatkan ketahanan keluarga sehat dan tangguh bencana.',
            'pelaksanaan' => 'Pelaksanaan dilakukan melalui kolaborasi kader dan lintas sektor.',
            'dokumentasi' => 'Dokumentasi kegiatan tersimpan pada lampiran laporan.',
            'penutup' => 'Demikian laporan pelaksanaan pilot project disusun.',
            'tahun_awal' => $tahunAwal,
            'tahun_akhir' => $tahunAkhir,
            'tahun_anggaran' => $this->defaultBudgetYear(),
            'level' => $context['level'],
            'area_id' => $context['area_id'],
            'created_by' => $context['creator_id'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $sections = config('pilot_project_keluarga_sehat.sections', []);
        $valueRows = [];
        $sortOrder = 1;

        foreach ($sections as $section) {
            if (! is_array($section)) {
                continue;
            }

            $storageSection = (string) ($section['storage_section'] ?? 'pilot_project');
            $clusters = $section['clusters'] ?? [];
            if (! is_array($clusters)) {
                continue;
            }

            foreach ($clusters as $cluster) {
                if (! is_array($cluster)) {
                    continue;
                }

                $clusterCode = strtoupper((string) ($cluster['code'] ?? 'SUPPORT'));
                $indicators = $cluster['indicators'] ?? [];
                if (! is_array($indicators) || $indicators === []) {
                    continue;
                }

                $indicator = $indicators[0];
                if (! is_array($indicator)) {
                    continue;
                }

                $valueRows[] = [
                    'report_id' => $reportId,
                    'section' => $storageSection,
                    'cluster_code' => $clusterCode,
                    'indicator_code' => (string) ($indicator['code'] ?? 'indikator_'.$sortOrder),
                    'indicator_label' => (string) ($indicator['label'] ?? 'Indikator '.$sortOrder),
                    'year' => $tahunAkhir,
                    'semester' => random_int(1, 2),
                    'value' => random_int(0, 500),
                    'evaluation_note' => $faker->boolean(65) ? null : 'Evaluasi indikator '.$context['area_name'],
                    'sort_order' => $sortOrder,
                    'tahun_anggaran' => $this->defaultBudgetYear(),
                    'level' => $context['level'],
                    'area_id' => $context['area_id'],
                    'created_by' => $context['creator_id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $sortOrder++;
            }
        }

        if ($valueRows === []) {
            $valueRows[] = [
                'report_id' => $reportId,
                'section' => 'pilot_project',
                'cluster_code' => 'I',
                'indicator_code' => 'indikator_awal',
                'indicator_label' => 'Indikator Awal',
                'year' => $tahunAkhir,
                'semester' => 1,
                'value' => random_int(0, 100),
                'evaluation_note' => null,
                'sort_order' => 1,
                'tahun_anggaran' => $this->defaultBudgetYear(),
                'level' => $context['level'],
                'area_id' => $context['area_id'],
                'created_by' => $context['creator_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('pilot_project_keluarga_sehat_values')->insert($valueRows);
    }

    private function seedPilotProjectNaskahPelaporan(\Faker\Generator $faker, array $context): void
    {
        $pelaksanaan = [
            1 => $faker->sentence(14),
            2 => $faker->sentence(14),
            3 => $faker->sentence(14),
            4 => $faker->sentence(14),
            5 => $faker->sentence(14),
        ];
        $suratTembusan = 'Arsip Sekretariat TP PKK';

        $reportId = DB::table('pilot_project_naskah_pelaporan_reports')->insertGetId([
            'judul_laporan' => 'Naskah Pelaporan Pilot Project '.$context['area_name'],
            'surat_kepada' => 'Ketua TP PKK Kabupaten Batang',
            'surat_dari' => 'TP PKK '.$context['area_name'],
            'surat_tembusan' => $suratTembusan,
            'surat_tanggal' => now()->format('Y-m-d'),
            'surat_nomor' => sprintf('NP/%s/%d', strtoupper($context['level']), random_int(100, 999)),
            'surat_sifat' => 'Biasa',
            'surat_lampiran' => '4 berkas',
            'surat_hal' => 'Penyampaian naskah pelaporan pilot project',
            'dasar_pelaksanaan' => 'Pelaksanaan berdasarkan rencana kerja TP PKK.',
            'pendahuluan' => 'Pendahuluan naskah pelaporan pilot project.',
            'pelaksanaan_1' => $pelaksanaan[1],
            'pelaksanaan_2' => $pelaksanaan[2],
            'pelaksanaan_3' => $pelaksanaan[3],
            'pelaksanaan_4' => $pelaksanaan[4],
            'pelaksanaan_5' => $pelaksanaan[5],
            'penutup' => 'Penutup naskah pelaporan.',
            'tahun_anggaran' => $this->defaultBudgetYear(),
            'level' => $context['level'],
            'area_id' => $context['area_id'],
            'created_by' => $context['creator_id'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->seedPilotProjectNaskahPelaporanPelaksanaanItems(
            reportId: $reportId,
            pelaksanaan: $pelaksanaan,
            context: $context
        );
        $this->seedPilotProjectNaskahPelaporanTembusanItems(
            reportId: $reportId,
            tembusan: $suratTembusan,
            context: $context
        );

        $attachments = [];
        $categories = ['6a_photo', '6b_photo', '6d_document', '6e_photo'];

        foreach ($categories as $index => $category) {
            $isDocument = $category === '6d_document';
            $extension = $isDocument ? 'pdf' : 'jpg';
            $mimeType = $isDocument ? 'application/pdf' : 'image/jpeg';
            $attachments[] = [
                'report_id' => $reportId,
                'category' => $category,
                'file_path' => sprintf(
                    'seed/pilot-project-naskah/%s-%d/%s-%d.%s',
                    $context['level'],
                    $context['area_id'],
                    $category,
                    $index + 1,
                    $extension
                ),
                'original_name' => sprintf('%s-%s.%s', $context['area_name'], $category, $extension),
                'mime_type' => $mimeType,
                'file_size' => random_int(150000, 900000),
                'tahun_anggaran' => $this->defaultBudgetYear(),
                'level' => $context['level'],
                'area_id' => $context['area_id'],
                'created_by' => $context['creator_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('pilot_project_naskah_pelaporan_attachments')->insert($attachments);
    }

    /**
     * @param  array<int, string>  $pelaksanaan
     * @param  array<string, mixed>  $context
     */
    private function seedPilotProjectNaskahPelaporanPelaksanaanItems(
        int $reportId,
        array $pelaksanaan,
        array $context
    ): void {
        $now = now();
        $rows = [];

        foreach ($pelaksanaan as $sequence => $description) {
            $rows[] = [
                'report_id' => $reportId,
                'sequence' => $sequence,
                'description' => $description,
                'level' => $context['level'],
                'area_id' => $context['area_id'],
                'created_by' => $context['creator_id'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('pilot_project_naskah_pelaporan_pelaksanaan_items')->insert($rows);
    }

    /**
     * @param  array<string, mixed>  $context
     */
    private function seedPilotProjectNaskahPelaporanTembusanItems(
        int $reportId,
        ?string $tembusan,
        array $context
    ): void {
        $items = $this->splitLines($tembusan);
        if ($items === []) {
            return;
        }

        $now = now();
        $rows = [];
        $sequence = 1;

        foreach ($items as $value) {
            $rows[] = [
                'report_id' => $reportId,
                'sequence' => $sequence,
                'value' => $value,
                'level' => $context['level'],
                'area_id' => $context['area_id'],
                'created_by' => $context['creator_id'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $sequence++;
        }

        DB::table('pilot_project_naskah_pelaporan_tembusan_items')->insert($rows);
    }

    private function atLeastOneTrue(array $keys): array
    {
        $flags = [];
        foreach ($keys as $key) {
            $flags[$key] = (bool) random_int(0, 1);
        }

        if (collect($flags)->contains(true)) {
            return $flags;
        }

        $forcedKey = $keys[array_rand($keys)];
        $flags[$forcedKey] = true;

        return $flags;
    }

    /**
     * @return array<int, string>
     */
    private function splitLines(?string $value): array
    {
        $text = trim((string) ($value ?? ''));
        if ($text === '') {
            return [];
        }

        $parts = preg_split('/\\r\\n|\\r|\\n/', $text) ?: [];

        return array_values(array_filter(array_map('trim', $parts), static fn (string $part): bool => $part !== ''));
    }

    private function defaultBudgetYear(): int
    {
        return (int) now()->format('Y');
    }

    private function resolveBudgetYear(?int $year = null): int
    {
        return $year ?? $this->defaultBudgetYear();
    }

    private function randomAddress(\Faker\Generator $faker, array $context): string
    {
        if ($context['level'] === 'desa') {
            return sprintf(
                'Dk. %s RT %02d/RW %02d, Desa %s, Kec. %s, Kab. Batang, Jawa Tengah',
                $faker->citySuffix(),
                random_int(1, 12),
                random_int(1, 8),
                $context['area_name'],
                $context['kecamatan_name']
            );
        }

        return sprintf(
            'Jl. %s No. %d, Kec. %s, Kab. Batang, Jawa Tengah',
            $faker->streetName(),
            random_int(1, 120),
            $context['kecamatan_name']
        );
    }

    private function countFor(
        string $level,
        int $desaMin,
        int $desaMax,
        int $kecamatanMin,
        int $kecamatanMax
    ): int {
        $min = $level === 'desa' ? $desaMin : $kecamatanMin;
        $max = $level === 'desa' ? $desaMax : $kecamatanMax;
        $sampleA = random_int($min, $max);
        $sampleB = random_int($min, $max);

        return (int) round(($sampleA + $sampleB) / 2);
    }
}
