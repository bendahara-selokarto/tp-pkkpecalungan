export const lampiranPlaceholder = '-'

const PRINT_SOURCE_OVERRIDES = {
  'agenda-surat/ekspedisi': 'agenda-surat',
  'anggota-tim-penggerak-kader': 'anggota-tim-penggerak',
}

const roleScopedBookModuleSlugs = new Set(['activities', 'bantuans', 'inventaris', 'kader-khusus', 'prestasi-lomba', 'program-prioritas'])
const bookGroupContextByMenuGroup = {
  'sekretaris-tpk': 'sekretaris-tpk',
  'penunjang-buku-wajib': 'sekretaris-tpk',
  'pokja-i': 'pokja-i',
  'pokja-ii': 'pokja-ii',
  'pokja-iii': 'pokja-iii',
  'pokja-iv': 'pokja-iv',
}

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

const isExternalItem = (item) => item?.external === true || (typeof item?.href === 'string' && item.href.includes('report/pdf'))

const withBookGroupContext = (item, menuGroupKey) => {
  if (isExternalItem(item)) {
    return item
  }

  const bookGroup = bookGroupContextByMenuGroup[menuGroupKey]
  const moduleSlug = resolveModuleSlugFromHref(item?.href ?? '')
  if (!bookGroup || !moduleSlug || !roleScopedBookModuleSlugs.has(moduleSlug)) {
    return item
  }

  const separator = item.href.includes('?') ? '&' : '?'

  return {
    ...item,
    href: `${item.href}${separator}book_group=${bookGroup}`,
  }
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
    { href: `/${scope}/kader-khusus/report/pdf`, label: 'Buku Kader Khusus | -' },
    { href: `/${scope}/prestasi-lomba/report/pdf`, label: 'Buku Prestasi | -' },
    { href: `/${scope}/buku-notulen-rapat/report/pdf`, label: 'Buku Notulen Rapat | -' },
    { href: `/${scope}/buku-daftar-hadir/report/pdf`, label: 'Buku Daftar Hadir | -' },
    { href: `/${scope}/buku-tamu/report/pdf`, label: 'Buku Tamu | -' },
    { href: `/${scope}/buku-keuangan/report/pdf`, label: 'Buku Keuangan | 4.11' },
    { href: `/${scope}/bantuans/report/pdf`, label: 'Buku Bantuan | -' },
    { href: `/${scope}/inventaris/report/pdf`, label: 'Buku Inventaris | 4.12' },
    { href: `/${scope}/data-warga/report/pdf`, label: 'Data Warga | 4.14.1a' },
    { href: `/${scope}/data-kegiatan-warga/report/pdf`, label: 'Data Kegiatan Warga | 4.14.1b' },
  ],
  'penunjang-buku-wajib': [
    {
      href: scope === 'kecamatan'
        ? `/${scope}/catatan-keluarga/data-umum-pkk-kecamatan/report/pdf`
        : `/${scope}/catatan-keluarga/data-umum-pkk/report/pdf`,
      label: 'Data Umum',
    },
    { href: `/${scope}/program-prioritas/report/pdf`, label: 'Program Kerja' },
  ],
  'pokja-i': [
    { href: `/${scope}/activities/report/pdf`, label: 'Buku Kegiatan' },
    { href: `/${scope}/program-prioritas/report/pdf`, label: 'Buku Program Kerja TP PKK | -' },
    { href: `/${scope}/kader-khusus/report/pdf`, label: 'Buku Kader Khusus | -' },
    { href: `/${scope}/bantuans/report/pdf`, label: 'Buku Bantuan | -' },
    { href: `/${scope}/prestasi-lomba/report/pdf`, label: 'Buku Prestasi | -' },
    { href: `/${scope}/data-kegiatan-pkk-pokja-i/report/pdf`, label: 'Data Kegiatan PKK Pokja I | 4.21' },
    { href: `/${scope}/simulasi-penyuluhan/report/pdf`, label: 'Kelompok Simulasi dan Penyuluhan | 4.14.4f' },
    { href: `/${scope}/anggota-pokja/report/pdf`, label: 'Anggota Pokja I | -' },
    { href: `/${scope}/bkl/report/pdf`, label: 'Buku Kegiatan BKL | -' },
    { href: `/${scope}/bkr/report/pdf`, label: 'Buku Kegiatan BKR | -' },
    { href: `/${scope}/paar/report/pdf`, label: 'Buku PAAR | -' },
  ],
  'pokja-ii': [
    { href: `/${scope}/activities/report/pdf`, label: 'Buku Kegiatan' },
    { href: `/${scope}/program-prioritas/report/pdf`, label: 'Buku Program Kerja TP PKK | -' },
    { href: `/${scope}/kader-khusus/report/pdf`, label: 'Buku Kader Khusus | -' },
    { href: `/${scope}/bantuans/report/pdf`, label: 'Buku Bantuan | -' },
    { href: `/${scope}/prestasi-lomba/report/pdf`, label: 'Buku Prestasi | -' },
    { href: `/${scope}/data-pelatihan-kader/report/pdf`, label: 'Data Pelatihan Kader | 4.14.3' },
    { href: `/${scope}/taman-bacaan/report/pdf`, label: 'Taman Bacaan/Perpustakaan | 4.14.4b' },
    { href: `/${scope}/koperasi/report/pdf`, label: 'Koperasi | 4.14.4c' },
    { href: `/${scope}/kejar-paket/report/pdf`, label: 'Kejar Paket/KF/PAUD | 4.14.4d' },
  ],
  'pokja-iii': [
    { href: `/${scope}/activities/report/pdf`, label: 'Buku Kegiatan' },
    { href: `/${scope}/program-prioritas/report/pdf`, label: 'Buku Program Kerja TP PKK | -' },
    { href: `/${scope}/kader-khusus/report/pdf`, label: 'Buku Kader Khusus | -' },
    { href: `/${scope}/bantuans/report/pdf`, label: 'Buku Bantuan | -' },
    { href: `/${scope}/inventaris/report/pdf`, label: 'Buku Inventaris | 4.12' },
    { href: `/${scope}/prestasi-lomba/report/pdf`, label: 'Buku Prestasi | -' },
    { href: `/${scope}/data-keluarga/report/pdf`, label: 'Data Keluarga | 4.14.2a' },
    { href: `/${scope}/data-industri-rumah-tangga/report/pdf`, label: 'Industri Rumah Tangga | 4.14.2c' },
    { href: `/${scope}/data-pemanfaatan-tanah-pekarangan-hatinya-pkk/report/pdf`, label: 'HATINYA PKK | 4.14.2b' },
    { href: `/${scope}/warung-pkk/report/pdf`, label: 'Data Aset Sarana Desa/Kelurahan | 4.14.4' },
  ],
  'pokja-iv': [
    { href: `/${scope}/activities/report/pdf`, label: 'Buku Kegiatan' },
    { href: `/${scope}/program-prioritas/report/pdf`, label: 'Buku Program Kerja TP PKK | -' },
    { href: `/${scope}/kader-khusus/report/pdf`, label: 'Buku Kader Khusus | -' },
    { href: `/${scope}/bantuans/report/pdf`, label: 'Buku Bantuan | -' },
    { href: `/${scope}/prestasi-lomba/report/pdf`, label: 'Buku Prestasi | -' },
    {
      href: `/${scope}/catatan-keluarga`,
      label: 'Pusat Laporan Catatan Keluarga | 4.15-4.24',
      lampiran: 'Hub',
      hub: true,
    },
    { href: `/${scope}/posyandu/report/pdf`, label: 'Data Isian Posyandu oleh TP PKK | 4.14.4e' },
    { href: `/${scope}/pilot-project-naskah-pelaporan/report/pdf`, label: 'Naskah Pelaporan Pilot Project Pokja IV | -' },
    { href: `/${scope}/pilot-project-keluarga-sehat/report/pdf`, label: 'Pilot Project Keluarga Sehat | -' },
  ],
})

const buildScopedMenuGroups = (scope) => {
  const pdfReportItemsByGroup = buildScopedPdfReportItemsByGroup(scope)

  const groups = [
    {
      key: 'sekretaris-wajib',
      label: 'Buku Wajib',
      code: 'BW',
      items: [
        { href: `/${scope}/anggota-tim-penggerak`, label: 'Buku Daftar Anggota Tim Penggerak PKK' },
        { href: `/${scope}/agenda-surat`, label: 'Buku Agenda Surat Masuk/Keluar' },
        { href: `/${scope}/buku-notulen-rapat`, label: 'Buku Notulen Rapat' },
        { href: `/${scope}/inventaris`, label: 'Buku Inventaris' },
        { href: `/${scope}/activities`, label: 'Buku Kegiatan' },
      ],
    },
    {
      key: 'bendahara-wajib',
      label: 'Buku Wajib',
      code: 'BW',
      items: [
        { href: `/${scope}/buku-keuangan`, label: 'Buku Keuangan' },
      ],
    },
    {
      key: 'pokja-i-wajib',
      label: 'Buku Wajib',
      code: 'BW',
      items: [
        { href: `/${scope}/program-prioritas`, label: 'Buku Program Kerja' },
        { href: `/${scope}/activities`, label: 'Buku Kegiatan' },
        { href: `/${scope}/data-kegiatan-pkk-pokja-i/report/pdf`, label: 'Buku Data Kegiatan', external: true },
      ],
    },
    {
      key: 'pokja-ii-wajib',
      label: 'Buku Wajib',
      code: 'BW',
      items: [
        { href: `/${scope}/program-prioritas`, label: 'Buku Program Kerja' },
        { href: `/${scope}/activities`, label: 'Buku Kegiatan' },
        { href: `/${scope}/catatan-keluarga/data-kegiatan-pkk-pokja-ii/report/pdf`, label: 'Buku Data Kegiatan', external: true },
      ],
    },
    {
      key: 'pokja-iii-wajib',
      label: 'Buku Wajib',
      code: 'BW',
      items: [
        { href: `/${scope}/program-prioritas`, label: 'Buku Program Kerja' },
        { href: `/${scope}/activities`, label: 'Buku Kegiatan' },
        { href: `/${scope}/catatan-keluarga/data-kegiatan-pkk-pokja-iii/report/pdf`, label: 'Buku Data Kegiatan', external: true },
      ],
    },
    {
      key: 'pokja-iv-wajib',
      label: 'Buku Wajib',
      code: 'BW',
      items: [
        { href: `/${scope}/program-prioritas`, label: 'Buku Program Kerja' },
        { href: `/${scope}/activities`, label: 'Buku Kegiatan' },
        { href: `/${scope}/catatan-keluarga/data-kegiatan-pkk-pokja-iv/report/pdf`, label: 'Buku Data Kegiatan', external: true },
      ],
    },
    {
      key: 'penunjang-buku-wajib',
      label: 'Buku Penunjang Buku Wajib',
      code: 'PB',
      items: [
        { href: `/${scope}/program-prioritas`, label: 'Buku Program Kerja' },
        {
          href: scope === 'kecamatan'
            ? `/${scope}/catatan-keluarga/data-umum-pkk-kecamatan/report/pdf`
            : `/${scope}/catatan-keluarga/data-umum-pkk/report/pdf`,
          label: 'Buku Data Umum',
          external: true,
        },
      ],
    },
    {
      key: 'common-pembantu',
      label: 'Buku Bantu',
      code: 'BB',
      items: [
        { href: `/${scope}/prestasi-lomba`, label: 'Buku Prestasi' },
        { href: `/${scope}/bantuans`, label: 'Buku Bantuan' },
        { href: `/${scope}/kader-khusus`, label: 'Buku Kader Khusus' },
      ],
    },
    {
      key: 'pokja-i',
      label: 'Buku Bantu',
      code: 'BB',
      items: [
        { href: `/${scope}/bantuans`, label: 'Buku Bantuan' },
        { href: `/${scope}/prestasi-lomba`, label: 'Buku Prestasi' },
        { href: '#', label: 'Buku Daftar Anggota', uiVisibility: 'disabled' },
        { href: `/${scope}/kader-khusus`, label: 'Buku Kader Khusus' },
        { href: `/${scope}/inventaris`, label: 'Buku Inventaris' },
        { href: `/${scope}/simulasi-penyuluhan`, label: 'Buku Kegiatan Simulasi' },
        { href: '#', label: 'Buku Daftar Anggota Simulasi', uiVisibility: 'disabled' },
        { href: '#', label: 'Buku Tamu Simulasi', uiVisibility: 'disabled' },
        { href: '#', label: 'Buku Daftar Hadir Simulasi', uiVisibility: 'disabled' },
        { href: '#', label: 'Buku Notulen Simulasi', uiVisibility: 'disabled' },
        { href: '#', label: 'Buku Grafik', uiVisibility: 'disabled' },
        { href: `/${scope}/anggota-pokja`, label: 'Buku Anggota Pokja I' },
        { href: '#', label: 'Buku Kliping', uiVisibility: 'disabled' },
        { href: `/${scope}/bkr`, label: 'Buku Kegiatan BKR' },
        { href: `/${scope}/bkl`, label: 'Buku Data Lansia' },
        { href: `/${scope}/paar`, label: 'Buku Data PAAR' },
      ],
    },
    {
      key: 'pokja-ii',
      label: 'Buku Bantu',
      code: 'BB',
      items: [
        { href: `/${scope}/bantuans`, label: 'Buku Bantuan' },
        { href: `/${scope}/prestasi-lomba`, label: 'Buku Prestasi' },
        { href: `/${scope}/pelatihan-kader-pokja-ii`, label: 'Buku Rekap Khusus Kader Pokja II' },
        { href: '#', label: 'Buku Grafik', uiVisibility: 'disabled' },
        { href: '#', label: 'Unggah Foto Kegiatan', uiVisibility: 'disabled' },
        { href: `/${scope}/pra-koperasi-up2k`, label: 'Buku Rekap Kelompok UP2K' },
      ],
    },
    {
      key: 'pokja-iii',
      label: 'Buku Bantu',
      code: 'BB',
      items: [
        { href: `/${scope}/bantuans`, label: 'Buku Bantuan' },
        { href: `/${scope}/prestasi-lomba`, label: 'Buku Prestasi' },
        { href: `/${scope}/buku-daftar-hadir`, label: 'Buku Daftar Hadir' },
        { href: `/${scope}/buku-notulen-rapat`, label: 'Buku Notulen' },
        { href: `/${scope}/inventaris`, label: 'Buku Inventaris' },
        { href: `/${scope}/data-pemanfaatan-tanah-pekarangan-hatinya-pkk`, label: 'Buku HATINYA PKK' },
        { href: '#', label: 'Buku Kader Khusus Pokja III', uiVisibility: 'disabled' },
        { href: '#', label: 'Buku Rumah Sehat dan Tidak Sehat', uiVisibility: 'disabled' },
        { href: `/${scope}/data-industri-rumah-tangga`, label: 'Buku Bantu Jumlah Industri Rumah Tangga' },
        { href: '#', label: 'Buku Konsultasi', uiVisibility: 'disabled' },
        { href: '#', label: 'Buku Kas Pokja', uiVisibility: 'disabled' },
        { href: '#', label: 'Buku Susunan Pengurus Pokja', uiVisibility: 'disabled' },
        { href: '#', label: 'Buku Grafik', uiVisibility: 'disabled' },
        { href: '#', label: 'Kliping', uiVisibility: 'disabled' },
        { href: '#', label: 'Unggah File Foto Kegiatan', uiVisibility: 'disabled' },
      ],
    },
    {
      key: 'pokja-iv',
      label: 'Buku Bantu',
      code: 'BB',
      items: [
        { href: '#', label: 'Buku Kader Khusus Pokja IV', uiVisibility: 'disabled' },
        { href: '#', label: 'Buku Bantu Catatan Lansia', uiVisibility: 'disabled' },
        { href: `/${scope}/prestasi-lomba`, label: 'Buku Prestasi' },
        {
          href: scope === 'kecamatan'
            ? `/${scope}/catatan-keluarga/data-umum-pkk-kecamatan/report/pdf`
            : `/${scope}/catatan-keluarga/data-umum-pkk/report/pdf`,
          label: 'Buku Data Umum',
          external: true,
        },
        { href: `/${scope}/bantuans`, label: 'Buku Bantu Umum' },
        { href: `/${scope}/inventaris`, label: 'Buku Inventaris' },
        { href: '#', label: 'Buku ASI Eksklusif', uiVisibility: 'disabled' },
        { href: '#', label: 'Buku Rekapitulasi IVA Test', uiVisibility: 'disabled' },
        { href: '#', label: 'Buku Capaian Akseptor', uiVisibility: 'disabled' },
        { href: '#', label: 'Buku Catatan Pemeriksaan dan IVA Test', uiVisibility: 'disabled' },
        { href: `/${scope}/posyandu`, label: 'Buku Kegiatan Posyandu' },
        { href: '#', label: 'Data Pengunjung Petugas Posyandu', uiVisibility: 'disabled' },
        { href: '#', label: 'Buku Grafik', uiVisibility: 'disabled' },
        { href: '#', label: 'Upload Foto Kegiatan', uiVisibility: 'disabled' },
      ],
    },
  ]

  // Filter groups based on items visibility and combine Pokja bantu groups
  const processedGroups = groups.map((group) => ({
    ...group,
    items: group.items
      .filter((item) => item.uiVisibility !== 'disabled')
      .map((item) => withBookGroupContext(item, group.key)),
    printItems: (pdfReportItemsByGroup[group.key] ?? []).map((item) => withBookGroupContext(item, group.key)),
  }))

  return processedGroups.filter((group) => group.items.length > 0)
}

const desaMenuGroups = buildScopedMenuGroups('desa')

const kecamatanMenuGroups = [
  {
    key: 'belum-ada-pemilik',
    label: 'Belum Ada Pemilik',
    code: 'BP',
    items: [
      { href: '/kecamatan/buku-daftar-hadir', label: 'Buku Daftar Hadir' },
      { href: '/kecamatan/buku-tamu', label: 'Buku Tamu' },
      { href: '/kecamatan/buku-keuangan', label: 'Buku Keuangan' },
      { href: '/kecamatan/laporan-tahunan-pkk', label: 'Laporan Tahunan Tim Penggerak PKK' },
      { href: '/kecamatan/data-warga', label: 'Data Warga | 4.14.1a' },
      { href: '/kecamatan/data-kegiatan-warga', label: 'Kegiatan Warga | 4.14.1b' },
      { href: '/kecamatan/bkl', label: 'Buku Kegiatan BKL' },
      { href: '/kecamatan/data-pelatihan-kader', label: 'Data Pelatihan Kader | 4.14.3' },
      { href: '/kecamatan/taman-bacaan', label: 'Data Taman Bacaan/Perpustakaan' },
      { href: '/kecamatan/koperasi', label: 'Data Koperasi' },
      { href: '/kecamatan/kejar-paket', label: 'Data Kejar Paket/KF/PAUD' },
      { href: '/kecamatan/literasi-warga', label: 'Literasi Warga (3 Buta)' },
      { href: '/kecamatan/bkb-kegiatan', label: 'Data BKB (Kegiatan)' },
      { href: '/kecamatan/tutor-khusus', label: 'Tutor Khusus KF/PAUD' },
      { href: '/kecamatan/pelatihan-kader-pokja-ii', label: 'Rekap Pelatihan Kader Pokja II' },
      { href: '/kecamatan/warung-pkk', label: 'Data Aset Sarana Desa/Kelurahan | 4.14.4' },
      { href: '/kecamatan/pilot-project-naskah-pelaporan', label: 'Naskah Pelaporan Pilot Project Pokja IV' },
      { href: '/kecamatan/pilot-project-keluarga-sehat', label: 'Laporan Pelaksanaan Pilot Project Gerakan Keluarga Sehat Tanggap dan Tangguh Bencana' },
    ],
  },
  ...buildScopedMenuGroups('kecamatan'),
  {
    key: 'monitoring',
    label: 'Monitoring Kecamatan',
    code: 'MON',
    items: [
      { href: '/kecamatan/desa-activities', label: 'Rekap Kegiatan Desa', uiVisibility: 'disabled' },
      { href: '/kecamatan/desa-arsip', label: 'Rekap Arsip Desa', uiVisibility: 'disabled' },
    ],
  },
]

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

const formatMenuLabel = (label) => formatPrintLabel(label)

const resolvePrintSourceSlug = (href) => {
  if (typeof href !== 'string' || href.length === 0 || href.startsWith('http')) {
    return null
  }

  const normalizedPath = href.split('?')[0]
  const segments = normalizedPath.split('/').filter(Boolean)
  const scope = segments[0]

  if (!['desa', 'kecamatan'].includes(scope)) {
    return null
  }

  const moduleSlug = segments[1] ?? null
  if (!moduleSlug) {
    return null
  }

  const subSlug = segments[2] ?? null
  const overrideKey = subSlug ? `${moduleSlug}/${subSlug}` : moduleSlug

  return PRINT_SOURCE_OVERRIDES[overrideKey] ?? moduleSlug
}

export {
  buildLampiranMap,
  buildScopedMenuGroups,
  extractLampiranCode,
  formatPrintLabel,
  formatMenuLabel,
  resolvePrintSourceSlug,
  resolveLampiran,
  resolveModuleSlugFromHref,
  desaMenuGroups,
  kecamatanMenuGroups,
}
