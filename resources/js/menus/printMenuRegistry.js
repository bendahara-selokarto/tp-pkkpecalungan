export const lampiranPlaceholder = '-'

const extractLampiranCode = (label) => {
  if (typeof label !== 'string' || label.length === 0) {
    return null
  }

  const match = label.match(/(\d+(?:\.\d+)*[a-z]?)/i)
  return match ? match[1] : null
}

const resolveModuleSlugFromHref = (href) => {
  if (typeof href !== 'string' || href.length === 0 || href.startsWith('http')) {
    return null
  }

  const normalizedPath = href.split('?')[0]
  const segments = normalizedPath.split('/').filter(Boolean)
  if (segments.length < 2) {
    return null
  }

  return segments[1]
}

const buildScopedPdfReportItemsByGroup = (scope) => ({
  'sekretaris-tpk': [
    { href: '/dashboard/charts/report/pdf', label: 'Grafik Dashboard', external: true },
    { href: `/${scope}/activities/report/pdf`, label: 'Buku Kegiatan | 4.13' },
    { href: `/${scope}/agenda-surat/report/pdf`, label: 'Agenda Surat Masuk/Keluar | 4.10' },
    { href: `/${scope}/agenda-surat/ekspedisi/report/pdf`, label: 'Ekspedisi Agenda Surat | 4.10' },
    { href: `/${scope}/anggota-pokja/report/pdf`, label: 'Buku Anggota Pokja | -' },
    { href: `/${scope}/anggota-tim-penggerak/report/pdf`, label: 'Anggota Tim Penggerak PKK | 4.9a' },
    { href: `/${scope}/anggota-tim-penggerak-kader/report/pdf`, label: 'Anggota dan Kader Tim Penggerak PKK | -' },
    { href: `/${scope}/kader-khusus/report/pdf`, label: 'Kader Khusus | -' },
    { href: `/${scope}/prestasi-lomba/report/pdf`, label: 'Prestasi Lomba | -' },
    { href: `/${scope}/buku-notulen-rapat/report/pdf`, label: 'Buku Notulen Rapat | -' },
    { href: `/${scope}/buku-daftar-hadir/report/pdf`, label: 'Buku Daftar Hadir | -' },
    { href: `/${scope}/buku-tamu/report/pdf`, label: 'Buku Tamu | -' },
    { href: `/${scope}/buku-keuangan/report/pdf`, label: 'Buku Keuangan | 4.11' },
    { href: `/${scope}/bantuans/report/pdf`, label: 'Buku Bantuan | -' },
    { href: `/${scope}/bantuans/keuangan/report/pdf`, label: 'Keuangan Bantuan | -' },
    { href: `/${scope}/inventaris/report/pdf`, label: 'Buku Inventaris | 4.12' },
    { href: `/${scope}/program-prioritas/report/pdf`, label: 'Buku Program Kerja TP PKK | -' },
    { href: `/${scope}/data-warga/report/pdf`, label: 'Data Warga | 4.14.1a' },
    { href: `/${scope}/data-kegiatan-warga/report/pdf`, label: 'Data Kegiatan Warga | 4.14.1b' },
  ],
  'pokja-i': [
    { href: `/${scope}/simulasi-penyuluhan/report/pdf`, label: 'Kelompok Simulasi dan Penyuluhan | 4.14.4f' },
    { href: `/${scope}/bkr/report/pdf`, label: 'BKR | -' },
    { href: `/${scope}/paar/report/pdf`, label: 'Buku PAAR | -' },
  ],
  'pokja-ii': [
    { href: `/${scope}/data-pelatihan-kader/report/pdf`, label: 'Data Pelatihan Kader | 4.14.3' },
    { href: `/${scope}/taman-bacaan/report/pdf`, label: 'Taman Bacaan/Perpustakaan | 4.14.4b' },
    { href: `/${scope}/koperasi/report/pdf`, label: 'Koperasi | 4.14.4c' },
    { href: `/${scope}/kejar-paket/report/pdf`, label: 'Kejar Paket/KF/PAUD | 4.14.4d' },
    { href: `/${scope}/bkl/report/pdf`, label: 'BKL | -' },
  ],
  'pokja-iii': [
    { href: `/${scope}/data-keluarga/report/pdf`, label: 'Data Keluarga | 4.14.2a' },
    { href: `/${scope}/data-industri-rumah-tangga/report/pdf`, label: 'Industri Rumah Tangga | 4.14.2c' },
    { href: `/${scope}/data-pemanfaatan-tanah-pekarangan-hatinya-pkk/report/pdf`, label: 'HATINYA PKK | 4.14.2b' },
    { href: `/${scope}/warung-pkk/report/pdf`, label: 'Data Aset Sarana Desa/Kelurahan | 4.14.4' },
  ],
  'pokja-iv': [
    { href: `/${scope}/catatan-keluarga/data-kegiatan-pkk-pokja-iv/report/pdf`, label: 'Kegiatan PKK Pokja IV | 4.24' },
    { href: `/${scope}/catatan-keluarga/report/pdf`, label: 'Catatan Keluarga | 4.15' },
    { href: `/${scope}/posyandu/report/pdf`, label: 'Data Isian Posyandu oleh TP PKK | 4.14.4e' },
    { href: `/${scope}/catatan-keluarga/rekap-dasa-wisma/report/pdf`, label: 'Kelompok Dasa Wisma | 4.16a' },
    { href: `/${scope}/catatan-keluarga/rekap-pkk-rt/report/pdf`, label: 'Kelompok PKK RT | 4.16b' },
    { href: `/${scope}/catatan-keluarga/catatan-pkk-rw/report/pdf`, label: 'Catatan PKK RW | 4.16c' },
    { href: `/${scope}/catatan-keluarga/rekap-rw/report/pdf`, label: 'Kelompok PKK Dusun | 4.16d' },
    { href: `/${scope}/catatan-keluarga/tp-pkk-desa-kelurahan/report/pdf`, label: 'Catatan TP PKK Desa/Kelurahan | 4.17a' },
    { href: `/${scope}/catatan-keluarga/tp-pkk-kecamatan/report/pdf`, label: 'Catatan TP PKK Kecamatan | 4.17b' },
    { href: `/${scope}/catatan-keluarga/tp-pkk-kabupaten-kota/report/pdf`, label: 'Catatan TP PKK Kabupaten/Kota | 4.17c' },
    { href: `/${scope}/catatan-keluarga/tp-pkk-provinsi/report/pdf`, label: 'Catatan TP PKK Provinsi | 4.17d' },
    { href: `/${scope}/catatan-keluarga/rekap-ibu-hamil-dasawisma/report/pdf`, label: 'Kelompok Dasawisma | 4.18a' },
    { href: `/${scope}/catatan-keluarga/rekap-ibu-hamil-pkk-rt/report/pdf`, label: 'Kelompok PKK RT | 4.18b' },
    { href: `/${scope}/catatan-keluarga/rekap-ibu-hamil-pkk-rw/report/pdf`, label: 'Kelompok PKK RW | 4.18c' },
    { href: `/${scope}/catatan-keluarga/rekap-ibu-hamil-pkk-dusun-lingkungan/report/pdf`, label: 'Kelompok PKK Dusun | 4.18d ' },
    { href: `/${scope}/catatan-keluarga/rekap-ibu-hamil-tp-pkk-kecamatan/report/pdf`, label: 'Kelompok PKK Kecamatan | 4.19b' },
    { href: `/${scope}/catatan-keluarga/data-umum-pkk/report/pdf`, label: 'Data Umum PKK | 4.20a' },
    { href: `/${scope}/catatan-keluarga/data-umum-pkk-kecamatan/report/pdf`, label: 'Data Umum PKK Kecamatan | 4.20b' },
    { href: `/${scope}/pilot-project-naskah-pelaporan/report/pdf`, label: 'Naskah Pelaporan Pilot Project Pokja IV | -' },
    { href: `/${scope}/pilot-project-keluarga-sehat/report/pdf`, label: 'Pilot Project Keluarga Sehat | -' },
  ],
})

const buildScopedMenuGroups = (scope) => {
  const pdfReportItemsByGroup = buildScopedPdfReportItemsByGroup(scope)

  const groups = [
    {
      key: 'sekretaris-tpk',
      label: 'Sekretaris PKK',
      code: 'ST',
      items: [
        { href: `/${scope}/anggota-tim-penggerak`, label: 'Daftar Anggota Tim Penggerak PKK' },
        { href: `/${scope}/kader-khusus`, label: 'Daftar Kader Tim Penggerak PKK' },
        { href: `/${scope}/agenda-surat`, label: 'Agenda Surat Masuk/Keluar' },
        { href: `/${scope}/buku-daftar-hadir`, label: 'Buku Daftar Hadir' },
        { href: `/${scope}/buku-tamu`, label: 'Buku Tamu' },
        { href: `/${scope}/buku-notulen-rapat`, label: 'Buku Notulen Rapat' },
        { href: `/${scope}/buku-keuangan`, label: 'Buku Keuangan' },
        { href: `/${scope}/inventaris`, label: 'Buku Inventaris | 4.12' },
        { href: `/${scope}/activities`, label: 'Buku Kegiatan | 4.14' },
        {
          href: `/${scope}/data-warga`,
          label: 'Data Warga | 4.14.1a',
          uiVisibility: 'sekretaris-only',
        },
        {
          href: `/${scope}/data-kegiatan-warga`,
          label: 'Kegiatan Warga 4.14.1b',
          uiVisibility: 'sekretaris-only',
        },
        { href: `/${scope}/program-prioritas`, label: 'Buku Program Kerja TP PKK' },
        { href: `/${scope}/anggota-pokja`, label: 'Buku Anggota Pokja' },
        { href: `/${scope}/prestasi-lomba`, label: 'Prestasi Lomba' },
        { href: `/${scope}/laporan-tahunan-pkk`, label: 'Laporan Tahunan Tim Penggerak PKK' },
      ],
    },
    {
      key: 'pokja-i',
      label: 'Pokja I',
      code: 'P1',
      items: [
        { href: `/${scope}/activities`, label: 'Buku Kegiatan' },
        { href: `/${scope}/inventaris`, label: 'Buku Inventaris' },
        { href: `/${scope}/buku-tamu`, label: 'Buku Tamu' },
        {
          href: `/${scope}/simulasi-penyuluhan`,
          label: 'Kelompok Simulasi dan Penyuluhan | 4.14.4f',
          uiVisibility: 'desa-pokja-i-only',
        },
        { href: `/${scope}/bkl`, label: 'BKL' },
        { href: `/${scope}/bkr`, label: 'BKR' },
        { href: `/${scope}/paar`, label: 'Buku PAAR' },
      ],
    },
    {
      key: 'pokja-ii',
      label: 'Pokja II',
      code: 'P2',
      items: [
        { href: `/${scope}/activities`, label: 'Buku Kegiatan' },
        { href: `/${scope}/inventaris`, label: 'Buku Inventaris' },
        { href: `/${scope}/buku-tamu`, label: 'Buku Tamu' },
        { href: `/${scope}/data-pelatihan-kader`, label: 'Data Pelatihan Kader | 4.14.3' },
        { href: `/${scope}/taman-bacaan`, label: 'Data Taman Bacaan/Perpustakaan' },
        { href: `/${scope}/koperasi`, label: 'Data Koperasi' },
        { href: `/${scope}/kejar-paket`, label: 'Data Kejar Paket/KF/PAUD' },
        { href: `/${scope}/literasi-warga`, label: 'Literasi Warga (3 Buta)' },
        { href: `/${scope}/bkb-kegiatan`, label: 'Data BKB (Kegiatan)' },
        { href: `/${scope}/tutor-khusus`, label: 'Tutor Khusus KF/PAUD' },
        { href: `/${scope}/pelatihan-kader-pokja-ii`, label: 'Rekap Pelatihan Kader Pokja II' },
        { href: `/${scope}/pra-koperasi-up2k`, label: 'Pra Koperasi/UP2K' },
      ],
    },
    {
      key: 'pokja-iii',
      label: 'Pokja III',
      code: 'P3',
      items: [
        { href: `/${scope}/activities`, label: 'Buku Kegiatan' },
        { href: `/${scope}/inventaris`, label: 'Buku Inventaris | 4.12 ' },
        { href: `/${scope}/buku-tamu`, label: 'Buku Tamu' },
        { href: `/${scope}/data-keluarga`, label: 'Data Keluarga | 4.14.2a' },
        { href: `/${scope}/data-pemanfaatan-tanah-pekarangan-hatinya-pkk`, label: 'HATINYA PKK | 4.14.2b' },
        { href: `/${scope}/data-industri-rumah-tangga`, label: 'Industri Rumah Tangga | 4.14.2c' },
        { href: `/${scope}/warung-pkk`, label: 'Data Aset Sarana Desa/Kelurahan | 4.14.4' },
      ],
    },
    {
      key: 'pokja-iv',
      label: 'Pokja IV',
      code: 'P4',
      items: [
        { href: `/${scope}/activities`, label: 'Buku Kegiatan' },
        { href: `/${scope}/inventaris`, label: 'Buku Inventaris' },
        { href: `/${scope}/buku-tamu`, label: 'Buku Tamu' },
        { href: `/${scope}/posyandu`, label: 'Data Isian Posyandu oleh TP PKK' },
        { href: `/${scope}/catatan-keluarga`, label: 'Catatan Keluarga | 4.15' },
        { href: `/${scope}/pilot-project-naskah-pelaporan`, label: 'Naskah Pelaporan Pilot Project Pokja IV' },
        { href: `/${scope}/pilot-project-keluarga-sehat`, label: 'Laporan Pelaksanaan Pilot Project Gerakan Keluarga Sehat Tanggap dan Tangguh Bencana' },
      ],
    },
  ]

  return groups.map((group) => ({
    ...group,
    printItems: pdfReportItemsByGroup[group.key] ?? [],
  }))
}

const buildLampiranMap = (groups) => {
  const lampiranBySlug = {}

  groups.forEach((group) => {
    group.items.forEach((item) => {
      const lampiran = extractLampiranCode(item.label)
      const slug = resolveModuleSlugFromHref(item.href)
      if (!lampiran || !slug || lampiranBySlug[slug]) {
        return
      }

      lampiranBySlug[slug] = lampiran
    })
  })

  return lampiranBySlug
}

const resolveLampiran = (item, lampiranBySlug) => {
  if (item?.lampiran) {
    return item.lampiran
  }

  const inferred = extractLampiranCode(item?.label ?? '')
  if (inferred) {
    return inferred
  }

  const slug = resolveModuleSlugFromHref(item?.href ?? '')
  if (slug && lampiranBySlug[slug]) {
    return lampiranBySlug[slug]
  }

  return lampiranPlaceholder
}

const formatPrintLabel = (label) => {
  if (typeof label !== 'string') {
    return ''
  }

  const cleanedLabel = label
    .replace(/^Laporan\s+PDF\s+/i, '')
    .replace(/^Laporan\s+/i, '')

  return cleanedLabel.split('|')[0].trim()
}

export {
  buildLampiranMap,
  buildScopedMenuGroups,
  extractLampiranCode,
  formatPrintLabel,
  resolveLampiran,
  resolveModuleSlugFromHref,
}
