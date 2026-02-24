<?php

namespace Database\Seeders;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DashboardNaturalBatangSeeder extends Seeder
{
    private const TARGET_KECAMATAN = 'Pecalungan';

    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            WilayahSeeder::class,
            AdminWilayahUserSeeder::class,
            SyncUserScopeAreaSeeder::class,
        ]);

        $kecamatanArea = Area::query()
            ->where('level', 'kecamatan')
            ->where('name', self::TARGET_KECAMATAN)
            ->first();

        if (! $kecamatanArea) {
            $this->command?->warn('Seeder dashboard dibatalkan: area kecamatan target tidak ditemukan.');

            return;
        }

        $contexts = $this->buildContexts($kecamatanArea);
        $areaIds = $contexts->pluck('area_id')->all();

        $this->purgeExistingData($areaIds);

        $faker = FakerFactory::create('id_ID');

        foreach ($contexts as $context) {
            $this->seedContext($faker, $context);
        }

        $this->command?->info(sprintf(
            'DashboardNaturalBatangSeeder selesai. Area terisi: %d (1 kecamatan + %d desa).',
            $contexts->count(),
            max(0, $contexts->count() - 1)
        ));
    }

    private function buildContexts(Area $kecamatanArea)
    {
        $contexts = collect();

        $contexts->push([
            'level' => 'kecamatan',
            'area_id' => (int) $kecamatanArea->id,
            'area_name' => (string) $kecamatanArea->name,
            'kecamatan_name' => (string) $kecamatanArea->name,
            'creator_id' => $this->resolveCreatorId('kecamatan', (int) $kecamatanArea->id, (string) $kecamatanArea->name),
        ]);

        $desaAreas = Area::query()
            ->where('level', 'desa')
            ->where('parent_id', $kecamatanArea->id)
            ->orderBy('id')
            ->get();

        foreach ($desaAreas as $desaArea) {
            $contexts->push([
                'level' => 'desa',
                'area_id' => (int) $desaArea->id,
                'area_name' => (string) $desaArea->name,
                'kecamatan_name' => (string) $kecamatanArea->name,
                'creator_id' => $this->resolveCreatorId('desa', (int) $desaArea->id, (string) $desaArea->name),
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
        $name = sprintf('Seeder %s %s', ucfirst($level), $areaName);

        $user = User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make('password123'),
                'scope' => $level,
                'area_id' => $areaId,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );

        $user->syncRoles([$level === 'desa' ? 'desa-sekretaris' : 'kecamatan-sekretaris']);

        return (int) $user->id;
    }

    private function purgeExistingData(array $areaIds): void
    {
        $tables = [
            'simulasi_penyuluhans',
            'posyandus',
            'kejar_pakets',
            'koperasis',
            'taman_bacaans',
            'warung_pkks',
            'data_pelatihan_kaders',
            'data_industri_rumah_tanggas',
            'data_pemanfaatan_tanah_pekarangan_hatinya_pkks',
            'data_keluargas',
            'data_kegiatan_wargas',
            'data_warga_anggotas',
            'data_wargas',
            'activities',
            'inventaris',
            'bantuans',
            'agenda_surats',
            'kader_khusus',
            'anggota_tim_penggeraks',
        ];

        foreach ($tables as $table) {
            DB::table($table)
                ->whereIn('area_id', $areaIds)
                ->delete();
        }
    }

    private function seedContext(\Faker\Generator $faker, array $context): void
    {
        $this->seedAnggotaTimPenggerak($faker, $context);
        $this->seedKaderKhusus($faker, $context);
        $this->seedAgendaSurat($faker, $context);
        $this->seedBantuan($faker, $context);
        $this->seedInventaris($faker, $context);
        $this->seedActivities($faker, $context);
        $this->seedDataWargaAndAnggota($faker, $context);
        $this->seedDataKegiatanWarga($faker, $context);
        $this->seedDataKeluarga($faker, $context);
        $this->seedDataPemanfaatanTanah($faker, $context);
        $this->seedDataIndustriRumahTangga($faker, $context);
        $this->seedDataPelatihanKader($faker, $context);
        $this->seedWarungPkk($faker, $context);
        $this->seedTamanBacaan($faker, $context);
        $this->seedKoperasi($faker, $context);
        $this->seedKejarPaket($faker, $context);
        $this->seedPosyandu($faker, $context);
        $this->seedSimulasiPenyuluhan($faker, $context);
    }

    private function seedAnggotaTimPenggerak(\Faker\Generator $faker, array $context): void
    {
        $count = $this->countFor($context['level'], 7, 16, 14, 28);
        $rows = [];

        $jabatanList = ['Ketua', 'Wakil Ketua', 'Sekretaris', 'Bendahara', 'Anggota Pokja I', 'Anggota Pokja II', 'Anggota Pokja III', 'Anggota Pokja IV'];
        $pendidikanList = ['SMA', 'D3', 'S1', 'S2'];
        $pekerjaanList = ['Ibu Rumah Tangga', 'Wiraswasta', 'Guru', 'Perangkat Desa', 'Kader PKK'];

        for ($i = 1; $i <= $count; $i++) {
            $rows[] = [
                'nama' => $faker->name(),
                'jabatan' => $faker->randomElement($jabatanList),
                'jenis_kelamin' => $faker->randomElement(['L', 'P']),
                'tempat_lahir' => $faker->randomElement(['Batang', 'Pekalongan', 'Kendal']),
                'tanggal_lahir' => $faker->dateTimeBetween('-58 years', '-23 years')->format('Y-m-d'),
                'status_perkawinan' => $faker->boolean(85) ? 'kawin' : 'tidak_kawin',
                'alamat' => $this->randomAddress($faker, $context),
                'pendidikan' => $faker->randomElement($pendidikanList),
                'pekerjaan' => $faker->randomElement($pekerjaanList),
                'keterangan' => $faker->boolean(70) ? null : 'Aktif pada kegiatan PKK tingkat '.$context['level'],
                'level' => $context['level'],
                'area_id' => $context['area_id'],
                'created_by' => $context['creator_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('anggota_tim_penggeraks')->insert($rows);
    }

    private function seedKaderKhusus(\Faker\Generator $faker, array $context): void
    {
        $count = $this->countFor($context['level'], 6, 12, 10, 22);
        $rows = [];

        $jenisKaderList = ['Posyandu', 'PAUD', 'UP2K', 'Lingkungan', 'Pangan', 'Kesehatan'];
        $pendidikanList = ['SMP', 'SMA', 'D3', 'S1'];

        for ($i = 1; $i <= $count; $i++) {
            $rows[] = [
                'nama' => $faker->name(),
                'jenis_kelamin' => $faker->randomElement(['L', 'P']),
                'tempat_lahir' => $faker->randomElement(['Batang', 'Pekalongan', 'Kendal']),
                'tanggal_lahir' => $faker->dateTimeBetween('-57 years', '-21 years')->format('Y-m-d'),
                'status_perkawinan' => $faker->boolean(85) ? 'kawin' : 'tidak_kawin',
                'alamat' => $this->randomAddress($faker, $context),
                'pendidikan' => $faker->randomElement($pendidikanList),
                'jenis_kader_khusus' => $faker->randomElement($jenisKaderList),
                'keterangan' => $faker->boolean(65) ? null : 'Pembinaan kader rutin',
                'level' => $context['level'],
                'area_id' => $context['area_id'],
                'created_by' => $context['creator_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('kader_khusus')->insert($rows);
    }

    private function seedAgendaSurat(\Faker\Generator $faker, array $context): void
    {
        $count = $this->countFor($context['level'], 12, 24, 18, 36);
        $rows = [];
        $perihalList = [
            'Undangan Rapat Koordinasi PKK',
            'Laporan Kegiatan Bulanan',
            'Permohonan Dukungan Kegiatan Posyandu',
            'Pemberitahuan Jadwal Monitoring',
            'Penyampaian Data Administrasi',
        ];

        for ($i = 1; $i <= $count; $i++) {
            $jenisSurat = $faker->randomElement(['masuk', 'keluar']);
            $tanggalSurat = $faker->dateTimeBetween('-12 months', 'now')->format('Y-m-d');

            $rows[] = [
                'jenis_surat' => $jenisSurat,
                'tanggal_terima' => $jenisSurat === 'masuk' ? $faker->dateTimeBetween($tanggalSurat, 'now')->format('Y-m-d') : null,
                'tanggal_surat' => $tanggalSurat,
                'nomor_surat' => sprintf('PKK/%s/%03d/%s', strtoupper($context['level']), $i, date('Y')),
                'asal_surat' => $jenisSurat === 'masuk' ? $faker->randomElement(['Kecamatan Pecalungan', 'TP PKK Kabupaten Batang', 'Puskesmas']) : null,
                'dari' => $jenisSurat === 'masuk' ? $faker->company() : 'TP PKK '.$context['area_name'],
                'kepada' => $jenisSurat === 'keluar' ? $faker->randomElement(['TP PKK Kecamatan', 'TP PKK Kabupaten Batang', 'Kepala Desa']) : 'TP PKK '.$context['area_name'],
                'perihal' => $faker->randomElement($perihalList),
                'lampiran' => $faker->boolean(40) ? (string) random_int(1, 5).' berkas' : null,
                'diteruskan_kepada' => $faker->boolean(30) ? 'Pokja terkait' : null,
                'tembusan' => $faker->boolean(25) ? 'Arsip Sekretariat' : null,
                'keterangan' => $faker->boolean(70) ? null : 'Perlu tindak lanjut minggu ini',
                'level' => $context['level'],
                'area_id' => $context['area_id'],
                'created_by' => $context['creator_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('agenda_surats')->insert($rows);
    }

    private function seedBantuan(\Faker\Generator $faker, array $context): void
    {
        $count = $this->countFor($context['level'], 5, 12, 8, 18);
        $rows = [];
        $categories = ['uang', 'barang'];
        $sources = ['pusat', 'provinsi', 'kabupaten', 'pihak_ketiga', 'lainnya'];
        $targetLocations = [
            'Dusun Krajan',
            'Dusun Sidomulyo',
            'Dusun Karanganyar',
            'RW 01',
            'RW 02',
            'RT 01/RW 03',
            'Posyandu Melati',
            'Kelompok Dasawisma Mawar',
            'Kelompok BKL',
        ];

        for ($i = 1; $i <= $count; $i++) {
            $rows[] = [
                'name' => $faker->randomElement($targetLocations),
                'category' => $faker->randomElement($categories),
                'description' => $faker->boolean(65) ? null : 'Dukungan kegiatan pemberdayaan keluarga',
                'source' => $faker->randomElement($sources),
                'amount' => (float) random_int(500000, 25000000),
                'received_date' => $faker->dateTimeBetween('-12 months', 'now')->format('Y-m-d'),
                'level' => $context['level'],
                'area_id' => $context['area_id'],
                'created_by' => $context['creator_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('bantuans')->insert($rows);
    }

    private function seedInventaris(\Faker\Generator $faker, array $context): void
    {
        $count = $this->countFor($context['level'], 8, 18, 14, 28);
        $rows = [];
        $units = ['unit', 'buah', 'set', 'paket'];
        $conditions = ['baik', 'rusak_ringan', 'rusak_berat'];
        $items = ['Meja', 'Kursi', 'Lemari Arsip', 'Printer', 'Laptop', 'Papan Informasi', 'Tenda', 'Sound System'];

        for ($i = 1; $i <= $count; $i++) {
            $rows[] = [
                'name' => $faker->randomElement($items).' '.$i,
                'asal_barang' => $faker->randomElement(['APBDes', 'Swadaya', 'Bantuan Kabupaten', 'Donasi']),
                'tanggal_penerimaan' => $faker->dateTimeBetween('-4 years', 'now')->format('Y-m-d'),
                'description' => $faker->boolean(60) ? null : 'Digunakan untuk kegiatan rutin PKK',
                'keterangan' => $faker->boolean(75) ? null : 'Perlu perawatan berkala',
                'quantity' => random_int(1, 15),
                'unit' => $faker->randomElement($units),
                'tempat_penyimpanan' => $faker->randomElement(['Sekretariat PKK', 'Balai Desa', 'Gudang RT']),
                'condition' => $faker->randomElement($conditions),
                'level' => $context['level'],
                'area_id' => $context['area_id'],
                'created_by' => $context['creator_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('inventaris')->insert($rows);
    }

    private function seedActivities(\Faker\Generator $faker, array $context): void
    {
        $count = $this->countFor($context['level'], 12, 26, 20, 44);
        $rows = [];
        $titles = [
            'Rapat Pokja Bulanan',
            'Sosialisasi Kesehatan Keluarga',
            'Pelatihan Administrasi PKK',
            'Monitoring Dasawisma',
            'Kunjungan Posyandu',
            'Penyuluhan Lingkungan Bersih',
            'Pembinaan Kelompok UP2K',
        ];
        $jabatan = ['Ketua TP PKK', 'Sekretaris', 'Pokja I', 'Pokja II', 'Pokja III', 'Pokja IV'];

        $forcedThisMonth = max(2, (int) floor($count * 0.2));

        for ($i = 1; $i <= $count; $i++) {
            $isThisMonth = $i <= $forcedThisMonth;
            $activityDate = $isThisMonth
                ? $faker->dateTimeBetween(now()->startOfMonth(), now()->endOfMonth())->format('Y-m-d')
                : $faker->dateTimeBetween(now()->subMonths(5)->startOfMonth(), now())->format('Y-m-d');

            $rows[] = [
                'title' => $faker->randomElement($titles).' #'.$i,
                'nama_petugas' => $faker->name(),
                'jabatan_petugas' => $faker->randomElement($jabatan),
                'description' => 'Kegiatan TP PKK '.$context['area_name'].' Kab. Batang',
                'uraian' => $faker->sentence(8),
                'level' => $context['level'],
                'area_id' => $context['area_id'],
                'created_by' => $context['creator_id'],
                'activity_date' => $activityDate,
                'tempat_kegiatan' => $faker->randomElement(['Balai Desa', 'Aula Kecamatan', 'Posyandu', 'Rumah Kader']),
                'status' => $faker->boolean(72) ? 'published' : 'draft',
                'tanda_tangan' => $faker->name(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('activities')->insert($rows);
    }

    private function seedDataWargaAndAnggota(\Faker\Generator $faker, array $context): void
    {
        $householdCount = $this->countFor($context['level'], 10, 24, 20, 42);
        $statusKawinList = ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'];
        $agamaList = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Budha', 'Konghucu'];
        $pendidikanList = ['Tidak Sekolah', 'SD', 'SMP', 'SMA', 'D3', 'S1'];
        $pekerjaanList = ['Pelajar', 'IRT', 'Petani', 'Wiraswasta', 'Buruh', 'Perangkat Desa', 'Guru'];
        $jabatanList = ['Ketua RT', 'Kader Dasawisma', 'Anggota', 'Sekretaris RT', 'Bendahara RT'];

        for ($i = 1; $i <= $householdCount; $i++) {
            $anggotaCount = random_int(2, 7);
            $anggotaRows = [];
            $lakiLaki = 0;
            $perempuan = 0;

            for ($j = 1; $j <= $anggotaCount; $j++) {
                $isKepala = $j === 1;
                $jenisKelamin = $isKepala ? 'L' : $faker->randomElement(['L', 'P']);
                $tanggalLahir = $faker->dateTimeBetween('-72 years', '-1 years')->format('Y-m-d');
                $umur = max(1, (int) now()->diffInYears($tanggalLahir));
                $statusDalamKeluarga = $isKepala
                    ? 'Kepala Keluarga'
                    : $faker->randomElement(['Istri', 'Anak', 'Anggota Keluarga Lain']);

                if ($jenisKelamin === 'L') {
                    $lakiLaki++;
                } else {
                    $perempuan++;
                }

                $anggotaRows[] = [
                    'nomor_urut' => $j,
                    'nomor_registrasi' => sprintf('DW-%d-%03d-%02d', $context['area_id'], $i, $j),
                    'nomor_ktp_kk' => $umur >= 17 ? (string) random_int(3175000000000000, 3175999999999999) : null,
                    'nama' => $faker->name($jenisKelamin === 'L' ? 'male' : 'female'),
                    'jabatan' => $faker->boolean(18) ? $faker->randomElement($jabatanList) : null,
                    'jenis_kelamin' => $jenisKelamin,
                    'tempat_lahir' => $faker->randomElement(['Batang', 'Pekalongan', 'Kendal']),
                    'tanggal_lahir' => $tanggalLahir,
                    'umur_tahun' => $umur,
                    'status_perkawinan' => $umur < 17 ? 'Belum Kawin' : $faker->randomElement($statusKawinList),
                    'status_dalam_keluarga' => $statusDalamKeluarga,
                    'agama' => $faker->randomElement($agamaList),
                    'alamat' => $this->randomAddress($faker, $context),
                    'desa_kel_sejenis' => $context['area_name'],
                    'pendidikan' => $faker->randomElement($pendidikanList),
                    'pekerjaan' => $umur < 17 ? 'Pelajar' : $faker->randomElement($pekerjaanList),
                    'akseptor_kb' => $jenisKelamin === 'P' && $umur >= 21 ? $faker->boolean(55) : false,
                    'aktif_posyandu' => $faker->boolean(50),
                    'ikut_bkb' => $faker->boolean(35),
                    'memiliki_tabungan' => $faker->boolean(45),
                    'ikut_kelompok_belajar' => $faker->boolean(20),
                    'jenis_kelompok_belajar' => $faker->boolean(20) ? $faker->randomElement(['PAUD', 'Kejar Paket A', 'Kejar Paket B', 'Taman Bacaan']) : null,
                    'ikut_paud' => $umur <= 6 ? $faker->boolean(65) : false,
                    'ikut_koperasi' => $umur >= 20 ? $faker->boolean(33) : false,
                    'keterangan' => $faker->boolean(80) ? null : 'Pendataan warga Dasawisma '.$context['area_name'],
                    'level' => $context['level'],
                    'area_id' => $context['area_id'],
                    'created_by' => $context['creator_id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            $dataWargaId = DB::table('data_wargas')->insertGetId([
                'dasawisma' => sprintf('Dasawisma %s-%02d', strtoupper(substr($context['area_name'], 0, 3)), $i),
                'nama_kepala_keluarga' => $anggotaRows[0]['nama'],
                'alamat' => $this->randomAddress($faker, $context),
                'jumlah_warga_laki_laki' => $lakiLaki,
                'jumlah_warga_perempuan' => $perempuan,
                'keterangan' => $faker->boolean(70) ? null : 'Pendataan rutin semester berjalan',
                'level' => $context['level'],
                'area_id' => $context['area_id'],
                'created_by' => $context['creator_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($anggotaRows as &$anggotaRow) {
                $anggotaRow['data_warga_id'] = $dataWargaId;
            }
            unset($anggotaRow);

            DB::table('data_warga_anggotas')->insert($anggotaRows);
        }
    }

    private function seedDataKegiatanWarga(\Faker\Generator $faker, array $context): void
    {
        $kegiatanList = [
            'Penghayatan dan Pengamalan Pancasila',
            'Kerja Bakti',
            'Rukun Kematian',
            'Kegiatan Keagamaan',
            'Jimpitan',
            'Arisan',
            'Lain-Lain',
        ];

        $rows = [];
        foreach ($kegiatanList as $kegiatan) {
            $rows[] = [
                'kegiatan' => $kegiatan,
                'aktivitas' => $faker->boolean(75),
                'keterangan' => $faker->boolean(65) ? null : 'Jenis kegiatan PKK yang diikuti warga',
                'level' => $context['level'],
                'area_id' => $context['area_id'],
                'created_by' => $context['creator_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('data_kegiatan_wargas')->insert($rows);
    }

    private function seedDataKeluarga(\Faker\Generator $faker, array $context): void
    {
        $distributions = [
            'Pra Sejahtera' => [8, 30],
            'Sejahtera I' => [16, 42],
            'Sejahtera II' => [20, 58],
            'Sejahtera III' => [15, 44],
            'Sejahtera III Plus' => [4, 24],
        ];

        $rows = [];
        foreach ($distributions as $kategori => [$min, $max]) {
            $rows[] = [
                'kategori_keluarga' => $kategori,
                'jumlah_keluarga' => $this->countFor($context['level'], $min, $max, (int) round($min * 1.2), (int) round($max * 1.6)),
                'keterangan' => $faker->boolean(80) ? null : 'Rekap keluarga '.$kategori,
                'level' => $context['level'],
                'area_id' => $context['area_id'],
                'created_by' => $context['creator_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('data_keluargas')->insert($rows);
    }

    private function seedDataPemanfaatanTanah(\Faker\Generator $faker, array $context): void
    {
        $komoditiByKategori = [
            'Peternakan' => ['Ayam Kampung', 'Bebek', 'Kambing'],
            'Perikanan' => ['Lele', 'Nila', 'Gurame'],
            'Warung Hidup' => ['Cabai Rawit', 'Tomat', 'Terong'],
            'TOGA' => ['Jahe', 'Kunyit', 'Serai'],
            'Tanaman Keras' => ['Mangga', 'Kelapa', 'Jambu'],
            'Lainnya' => ['Hidroponik', 'Jamur', 'Bibit Bunga'],
        ];

        $rows = [];
        foreach ($komoditiByKategori as $kategori => $komoditiList) {
            $picked = $faker->randomElements($komoditiList, random_int(1, 2));
            foreach ($picked as $komoditi) {
                $rows[] = [
                    'kategori_pemanfaatan_lahan' => $kategori,
                    'komoditi' => $komoditi,
                    'jumlah_komoditi' => (string) random_int(8, 120).' unit',
                    'level' => $context['level'],
                    'area_id' => $context['area_id'],
                    'created_by' => $context['creator_id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('data_pemanfaatan_tanah_pekarangan_hatinya_pkks')->insert($rows);
    }

    private function seedDataIndustriRumahTangga(\Faker\Generator $faker, array $context): void
    {
        $komoditiByKategori = [
            'Pangan' => ['Keripik Pisang', 'Kue Kering', 'Abon Lele'],
            'Sandang' => ['Batik Rumahan', 'Jilbab', 'Seragam PKK'],
            'Konveksi' => ['Tas Kain', 'Taplak', 'Spanduk Kain'],
            'Jasa' => ['Jasa Jahit', 'Laundry Rumahan', 'Les Privat'],
            'Lain-lain' => ['Kerajinan Bambu', 'Aksesoris', 'Souvenir'],
        ];

        $rows = [];
        foreach ($komoditiByKategori as $kategori => $komoditiList) {
            $picked = $faker->randomElements($komoditiList, random_int(1, 2));
            foreach ($picked as $komoditi) {
                $rows[] = [
                    'kategori_jenis_industri' => $kategori,
                    'komoditi' => $komoditi,
                    'jumlah_komoditi' => (string) random_int(5, 95).' unit',
                    'level' => $context['level'],
                    'area_id' => $context['area_id'],
                    'created_by' => $context['creator_id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('data_industri_rumah_tanggas')->insert($rows);
    }

    private function seedDataPelatihanKader(\Faker\Generator $faker, array $context): void
    {
        $count = $this->countFor($context['level'], 6, 14, 10, 24);
        $rows = [];
        $judulList = [
            'Pelatihan Administrasi Dasawisma',
            'Pelatihan Kader Posyandu',
            'Pelatihan Pengelolaan UP2K',
            'Pelatihan Penyuluhan Kesehatan Keluarga',
            'Pelatihan Ketahanan Pangan Keluarga',
        ];
        $kriteriaList = ['Dasar', 'Lanjutan', 'Tematik', 'TOT'];
        $institusiList = ['TP PKK Kabupaten Batang', 'Dinas Kesehatan Batang', 'UPTD Pemberdayaan Masyarakat', 'TP PKK Kecamatan Pecalungan'];

        for ($i = 1; $i <= $count; $i++) {
            $tahun = random_int((int) date('Y') - 4, (int) date('Y'));
            $rows[] = [
                'nomor_registrasi' => sprintf('KDR-%d-%03d', $context['area_id'], $i),
                'nama_lengkap_kader' => $faker->name('female'),
                'tanggal_masuk_tp_pkk' => $faker->dateTimeBetween('-10 years', '-1 years')->format('d-m-Y'),
                'jabatan_fungsi' => $faker->randomElement(['Ketua Pokja', 'Sekretaris Pokja', 'Anggota Pokja', 'Kader Dasawisma']),
                'nomor_urut_pelatihan' => $i,
                'judul_pelatihan' => $faker->randomElement($judulList),
                'jenis_kriteria_kaderisasi' => $faker->randomElement($kriteriaList),
                'tahun_penyelenggaraan' => $tahun,
                'institusi_penyelenggara' => $faker->randomElement($institusiList),
                'status_sertifikat' => $faker->boolean(78) ? 'Bersertifikat' : 'Tidak',
                'level' => $context['level'],
                'area_id' => $context['area_id'],
                'created_by' => $context['creator_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('data_pelatihan_kaders')->insert($rows);
    }

    private function seedWarungPkk(\Faker\Generator $faker, array $context): void
    {
        $count = $this->countFor($context['level'], 3, 8, 6, 14);
        $rows = [];
        $kategoriList = ['Pangan', 'Kerajinan', 'Jasa', 'Campuran'];
        $komoditiList = ['Sembako', 'Sayuran', 'Produk Olahan', 'Kerajinan Tangan', 'Perlengkapan Rumah Tangga'];

        for ($i = 1; $i <= $count; $i++) {
            $rows[] = [
                'nama_warung_pkk' => 'Warung PKK '.$context['area_name'].' '.$i,
                'nama_pengelola' => $faker->name('female'),
                'komoditi' => $faker->randomElement($komoditiList),
                'kategori' => $faker->randomElement($kategoriList),
                'volume' => (string) random_int(20, 240).' item/bulan',
                'level' => $context['level'],
                'area_id' => $context['area_id'],
                'created_by' => $context['creator_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('warung_pkks')->insert($rows);
    }

    private function seedTamanBacaan(\Faker\Generator $faker, array $context): void
    {
        $count = $this->countFor($context['level'], 2, 6, 4, 10);
        $rows = [];
        $jenisBukuList = ['Anak', 'Pertanian', 'Kesehatan', 'Keagamaan', 'Kewirausahaan', 'Umum'];
        $kategoriList = ['Perpustakaan Dasawisma', 'Taman Bacaan Warga', 'Sudut Baca Posyandu'];

        for ($i = 1; $i <= $count; $i++) {
            $rows[] = [
                'nama_taman_bacaan' => 'Taman Bacaan '.$context['area_name'].' '.$i,
                'nama_pengelola' => $faker->name(),
                'jumlah_buku_bacaan' => (string) random_int(80, 650),
                'jenis_buku' => $faker->randomElement($jenisBukuList),
                'kategori' => $faker->randomElement($kategoriList),
                'jumlah' => (string) random_int(15, 120).' pengunjung/bulan',
                'level' => $context['level'],
                'area_id' => $context['area_id'],
                'created_by' => $context['creator_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('taman_bacaans')->insert($rows);
    }

    private function seedKoperasi(\Faker\Generator $faker, array $context): void
    {
        $count = $this->countFor($context['level'], 2, 6, 4, 10);
        $rows = [];
        $jenisUsahaList = ['Simpan Pinjam', 'Konsumsi', 'Produksi', 'Jasa', 'Serba Usaha'];

        for ($i = 1; $i <= $count; $i++) {
            $berbadanHukum = $faker->boolean(70);
            $rows[] = [
                'nama_koperasi' => 'Koperasi '.$context['area_name'].' '.$i,
                'jenis_usaha' => $faker->randomElement($jenisUsahaList),
                'berbadan_hukum' => $berbadanHukum,
                'belum_berbadan_hukum' => ! $berbadanHukum,
                'jumlah_anggota_l' => random_int(8, 65),
                'jumlah_anggota_p' => random_int(14, 120),
                'level' => $context['level'],
                'area_id' => $context['area_id'],
                'created_by' => $context['creator_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('koperasis')->insert($rows);
    }

    private function seedKejarPaket(\Faker\Generator $faker, array $context): void
    {
        $count = $this->countFor($context['level'], 2, 6, 4, 10);
        $rows = [];
        $jenisList = ['Paket A', 'Paket B', 'Paket C'];

        for ($i = 1; $i <= $count; $i++) {
            $rows[] = [
                'nama_kejar_paket' => 'Kejar Paket '.$context['area_name'].' '.$i,
                'jenis_kejar_paket' => $faker->randomElement($jenisList),
                'jumlah_warga_belajar_l' => random_int(4, 42),
                'jumlah_warga_belajar_p' => random_int(6, 56),
                'jumlah_pengajar_l' => random_int(1, 8),
                'jumlah_pengajar_p' => random_int(2, 10),
                'level' => $context['level'],
                'area_id' => $context['area_id'],
                'created_by' => $context['creator_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('kejar_pakets')->insert($rows);
    }

    private function seedPosyandu(\Faker\Generator $faker, array $context): void
    {
        $count = $this->countFor($context['level'], 3, 9, 6, 14);
        $rows = [];
        $jenisPosyanduList = ['Pratama', 'Madya', 'Purnama', 'Mandiri'];
        $jenisKegiatanList = ['Balita', 'Lansia', 'Remaja', 'Ibu Hamil', 'Balita dan Lansia'];

        for ($i = 1; $i <= $count; $i++) {
            $rows[] = [
                'nama_posyandu' => 'Posyandu '.$context['area_name'].' '.$i,
                'nama_pengelola' => $faker->name('female'),
                'nama_sekretaris' => $faker->name('female'),
                'jenis_posyandu' => $faker->randomElement($jenisPosyanduList),
                'jumlah_kader' => random_int(5, 26),
                'jenis_kegiatan' => $faker->randomElement($jenisKegiatanList),
                'frekuensi_layanan' => random_int(8, 30),
                'jumlah_pengunjung_l' => random_int(20, 180),
                'jumlah_pengunjung_p' => random_int(35, 240),
                'jumlah_petugas_l' => random_int(1, 8),
                'jumlah_petugas_p' => random_int(3, 16),
                'level' => $context['level'],
                'area_id' => $context['area_id'],
                'created_by' => $context['creator_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('posyandus')->insert($rows);
    }

    private function seedSimulasiPenyuluhan(\Faker\Generator $faker, array $context): void
    {
        $count = $this->countFor($context['level'], 3, 8, 5, 12);
        $rows = [];
        $jenisList = ['Simulasi Kesehatan Keluarga', 'Simulasi Administrasi Dasawisma', 'Simulasi Ketahanan Pangan', 'Simulasi Lingkungan Sehat'];

        for ($i = 1; $i <= $count; $i++) {
            $rows[] = [
                'nama_kegiatan' => 'Simulasi '.$context['area_name'].' '.$i,
                'jenis_simulasi_penyuluhan' => $faker->randomElement($jenisList),
                'jumlah_kelompok' => random_int(2, 20),
                'jumlah_sosialisasi' => random_int(4, 36),
                'jumlah_kader_l' => random_int(2, 18),
                'jumlah_kader_p' => random_int(8, 60),
                'keterangan' => $faker->boolean(70) ? null : 'Monitoring rutin per triwulan',
                'level' => $context['level'],
                'area_id' => $context['area_id'],
                'created_by' => $context['creator_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('simulasi_penyuluhans')->insert($rows);
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
