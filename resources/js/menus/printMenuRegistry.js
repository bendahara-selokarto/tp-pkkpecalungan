export const lampiranPlaceholder = '-'

const PRINT_SOURCE_OVERRIDES = {
  'agenda-surat/ekspedisi': 'agenda-surat',
  'anggota-tim-penggerak-kader': 'anggota-tim-penggerak',
}

const roleScopedBookModuleSlugs = new Set(['activities', 'bantuans', 'inventaris', 'kader-khusus', 'prestasi-lomba', 'program-prioritas', 'buku-notulen-rapat', 'buku-daftar-hadir', 'buku-ekspedisi', 'agenda-surat', 'agenda-surat-tugas', 'buku-tamu', 'buku-agenda-sk', 'buku-konsultasi', 'laporan-tahunan-pkk', 'data-umum-pkk', 'data-umum-pkk-kecamatan', 'foto-kegiatan'])
const bookGroupContextByMenuGroup = {
  'sekretaris-wajib': 'sekretaris-tpk',
  'sekretaris-bantu': 'sekretaris-tpk',
  'bendahara-wajib': 'bendahara-tpk',
  'penunjang-buku-wajib': 'sekretaris-tpk',
  'pokja-i-wajib': 'pokja-i',
  'pokja-ii-wajib': 'pokja-ii',
  'pokja-iii-wajib': 'pokja-iii',
  'pokja-iv-wajib': 'pokja-iv',
  'pokja-i': 'pokja-i',
  'pokja-ii': 'pokja-ii',
  'pokja-iii': 'pokja-iii',
  'pokja-iv': 'pokja-iv',
  'common-pembantu': 'common', // Will be overridden if merged with pokja-x
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

  // Handle nested paths for Pokja Data Kegiatan and Data Umum
  // e.g., /desa/catatan-keluarga/data-kegiatan-pkk-pokja-ii/report/pdf -> data-kegiatan-pkk-pokja-ii
  if (segments.length >= 3 && segments[1] === 'catatan-keluarga') {
    return segments[2]
  }

  // Handle nested paths for Simulasi books
  // e.g., /desa/simulasi/buku-tamu -> buku-tamu-simulasi
  if (segments.length >= 3 && segments[1] === 'simulasi') {
    return `${segments[2]}-simulasi`
  }

  return segments[1]
}

const isExternalItem = (item) => item?.external === true

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
  'sekretaris-wajib': [
    { href: `/${scope}/activities/report/pdf`, label: 'Buku Kegiatan' },
    { href: `/${scope}/agenda-surat/report/pdf`, label: 'Agenda Surat Masuk/Keluar' },
    { href: `/${scope}/anggota-tim-penggerak/report/pdf`, label: 'Anggota Tim Penggerak PKK' },
    { href: `/${scope}/buku-notulen-rapat/report/pdf`, label: 'Buku Notulen Rapat' },
    { href: `/${scope}/inventaris/report/pdf`, label: 'Buku Inventaris' },
  ],
  'sekretaris-bantu': [
    { href: `/${scope}/buku-daftar-hadir/report/pdf`, label: 'Daftar Hadir' },
    { href: `/${scope}/buku-tamu/report/pdf`, label: 'Buku Tamu' },
    { href: `/${scope}/buku-agenda-sk/report/pdf`, label: 'Buku Agenda SK' },
    { href: `/${scope}/agenda-surat-tugas/report/pdf`, label: 'Agenda Surat Tugas' },
    { href: `/${scope}/prestasi-lomba/report/pdf`, label: 'Buku Prestasi' },
    { href: `/${scope}/bantuans/report/pdf`, label: 'Buku Bantuan' },
    { href: `/${scope}/kader-khusus/report/pdf`, label: 'Buku Kader Khusus' },
  ],
  'bendahara-wajib': [
    { href: `/${scope}/buku-keuangan/report/pdf`, label: 'Buku Keuangan' },
  ],
  'pokja-i-wajib': [
    { href: `/${scope}/activities/report/pdf`, label: 'Buku Kegiatan' },
    { href: `/${scope}/program-prioritas/report/pdf`, label: 'Buku Program Kerja' },
    { href: `/${scope}/data-kegiatan-pkk-pokja-i/report/pdf`, label: 'Buku Data Kegiatan' },
  ],
  'pokja-ii-wajib': [
    { href: `/${scope}/activities/report/pdf`, label: 'Buku Kegiatan' },
    { href: `/${scope}/program-prioritas/report/pdf`, label: 'Buku Program Kerja' },
    { href: `/${scope}/catatan-keluarga/data-kegiatan-pkk-pokja-ii/report/pdf`, label: 'Buku Data Kegiatan' },
  ],
  'pokja-iii-wajib': [
    { href: `/${scope}/activities/report/pdf`, label: 'Buku Kegiatan' },
    { href: `/${scope}/program-prioritas/report/pdf`, label: 'Buku Program Kerja' },
    { href: `/${scope}/catatan-keluarga/data-kegiatan-pkk-pokja-iii/report/pdf`, label: 'Buku Data Kegiatan' },
  ],
  'pokja-iv-wajib': [
    { href: `/${scope}/activities/report/pdf`, label: 'Buku Kegiatan' },
    { href: `/${scope}/program-prioritas/report/pdf`, label: 'Buku Program Kerja' },
    { href: `/${scope}/catatan-keluarga/data-kegiatan-pkk-pokja-iv/report/pdf`, label: 'Buku Data Kegiatan' },
  ],
  'common-pembantu': [
    { href: `/${scope}/prestasi-lomba/report/pdf`, label: 'Buku Prestasi' },
    { href: `/${scope}/bantuans/report/pdf`, label: 'Buku Bantuan' },
    { href: `/${scope}/kader-khusus/report/pdf`, label: 'Buku Kader Khusus' },
  ],
})

const buildScopedMenuGroups = (scope) => {
  const pdfReportItemsByGroup = buildScopedPdfReportItemsByGroup(scope)

  const rawGroups = [
    {
      key: 'sekretaris-wajib',
      label: 'Buku Wajib',
      code: 'BW',
      items: [
        { href: `/${scope}/anggota-tim-penggerak`, label: 'Buku Daftar Anggota Tim Penggerak PKK' },
        { href: `/${scope}/buku-notulen-rapat`, label: 'Buku Notulen' },
        { href: `/${scope}/activities`, label: 'Buku Kegiatan' },
        { href: `/${scope}/inventaris`, label: 'Buku Inventaris' },
        { href: `/${scope}/agenda-surat`, label: 'Buku Agenda Surat Masuk/Keluar' },
      ],
    },
    {
      key: 'bendahara-wajib',
      label: 'Buku Wajib',
      code: 'BW',
      items: [
        { href: `/${scope}/buku-keuangan`, label: 'Buku Keuangan' },
        { href: `/${scope}/activities`, label: 'Buku Kegiatan' },
      ],
    },
    {
      key: 'pokja-i-wajib',
      label: 'Buku Wajib',
      code: 'BW',
      items: [
        { href: `/${scope}/program-prioritas`, label: 'Buku Program Kerja' },
        { href: `/${scope}/activities`, label: 'Buku Kegiatan' },
        { href: `/${scope}/data-kegiatan-pkk-pokja-i/report/pdf`, label: 'Buku Data Kegiatan' },
      ],
    },
    {
      key: 'pokja-ii-wajib',
      label: 'Buku Wajib',
      code: 'BW',
      items: [
        { href: `/${scope}/program-prioritas`, label: 'Buku Program Kerja' },
        { href: `/${scope}/activities`, label: 'Buku Kegiatan' },
        { href: `/${scope}/catatan-keluarga/data-kegiatan-pkk-pokja-ii/report/pdf`, label: 'Buku Data Kegiatan' },
      ],
    },
    {
      key: 'pokja-iii-wajib',
      label: 'Buku Wajib',
      code: 'BW',
      items: [
        { href: `/${scope}/program-prioritas`, label: 'Buku Program Kerja' },
        { href: `/${scope}/activities`, label: 'Buku Kegiatan' },
        { href: `/${scope}/catatan-keluarga/data-kegiatan-pkk-pokja-iii/report/pdf`, label: 'Buku Data Kegiatan' },
      ],
    },
    {
      key: 'pokja-iv-wajib',
      label: 'Buku Wajib',
      code: 'BW',
      items: [
        { href: `/${scope}/program-prioritas`, label: 'Buku Program Kerja' },
        { href: `/${scope}/activities`, label: 'Buku Kegiatan' },
        { href: `/${scope}/catatan-keluarga/data-kegiatan-pkk-pokja-iv/report/pdf`, label: 'Buku Data Kegiatan' },
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
        },
      ],
    },
    {
      key: 'sekretaris-bantu',
      label: 'Buku Bantu',
      code: 'BB',
      items: [
        { href: `/${scope}/buku-daftar-hadir`, label: 'Daftar Hadir' },
        { href: `/${scope}/prestasi-lomba`, label: 'Buku Prestasi' },
        { href: `/${scope}/bantuans`, label: 'Buku Bantuan' },
        { href: `/${scope}/buku-konsultasi`, label: 'Buku Konsultasi' },
        { href: `/${scope}/buku-tamu`, label: 'Buku Tamu' },
        { href: `/${scope}/buku-kliping`, label: 'Buku Kliping' },
        { href: `/${scope}/buku-ekspedisi`, label: 'Buku Ekspedisi' },
        { href: '/dashboard/charts/report/pdf', label: 'Buku Grafik', external: true },
        { href: `/${scope}/buku-agenda-sk`, label: 'Buku Agenda SK' },
        { href: `/${scope}/agenda-surat-tugas`, label: 'Agenda Surat Tugas' },
        { href: `/${scope}/laporan-tahunan-pkk`, label: 'Laporan Tahunan Tim Penggerak PKK' },
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
        { href: `/${scope}/anggota-tim-penggerak`, label: 'Buku Daftar Anggota dan Kader' },
        { href: `/${scope}/kader-khusus`, label: 'Buku Daftar Kader Khusus' },
        { href: `/${scope}/inventaris`, label: 'Buku Inventaris' },
        { href: `/${scope}/simulasi-penyuluhan`, label: 'Buku Kegiatan Simulasi' },
        { href: `/${scope}/simulasi/buku-tamu`, label: 'Buku Tamu Simulasi' },
        { href: `/${scope}/simulasi/buku-daftar-hadir`, label: 'Buku Daftar Hadir Simulasi' },
        { href: `/${scope}/simulasi/buku-notulen`, label: 'Buku Notulen Simulasi' },
        { href: `/${scope}/bkr`, label: 'Buku Kegiatan BKR' },
        { href: `/${scope}/simulasi-penyuluhan/report/pdf/chart`, label: 'Buku Grafik', external: true },
        { href: `/${scope}/bkl`, label: 'Buku Data Lansia' },
        { href: `/${scope}/anggota-pokja`, label: 'Buku Anggota Pokja I' },
        { href: `/${scope}/buku-kliping`, label: 'Kliping' },
        { href: `/${scope}/paar`, label: 'Data PAAR' },
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
        { href: `/${scope}/pra-koperasi-up2k/report/pdf/chart`, label: 'Buku Grafik', external: true },
        { href: `/${scope}/foto-kegiatan`, label: 'Unggah Foto Kegiatan' },
        { href: `/${scope}/pra-koperasi-up2k`, label: 'Buku Rekap Kelompok UP2K' },
        { href: `/${scope}/taman-bacaan`, label: 'Data Taman Bacaan/Perpustakaan' },
        { href: `/${scope}/koperasi`, label: 'Data Koperasi' },
        { href: `/${scope}/kejar-paket`, label: 'Data Kejar Paket/KF/PAUD' },
        { href: `/${scope}/bkb-kegiatan`, label: 'Data BKB (Kegiatan)' },
        { href: `/${scope}/tutor-khusus`, label: 'Tutor Khusus KF/PAUD' },
        { href: `/${scope}/data-pelatihan-kader`, label: 'Data Pelatihan Kader' },
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
        { href: `/${scope}/kader-khusus`, label: 'Buku Kader Khusus' },
        { href: `/${scope}/data-pemanfaatan-tanah-pekarangan-hatinya-pkk`, label: 'Buku HATINYA PKK' },
        { href: '#', label: 'Buku Kader Khusus Pokja III', uiVisibility: 'disabled' },
        { href: '#', label: 'Buku Rumah Sehat and Tidak Sehat', uiVisibility: 'disabled' },
        { href: `/${scope}/data-industri-rumah-tangga`, label: 'Buku Bantu Jumlah Industri Rumah Tangga' },
        { href: `/${scope}/buku-konsultasi`, label: 'Buku Konsultasi' },
        { href: '#', label: 'Buku Kas Pokja', uiVisibility: 'disabled' },
        { href: '#', label: 'Buku Susunan Pengurus Pokja', uiVisibility: 'disabled' },
        { href: `/${scope}/data-pemanfaatan-tanah-pekarangan-hatinya-pkk/report/pdf/chart`, label: 'Buku Grafik', external: true },
        { href: `/${scope}/foto-kegiatan`, label: 'Unggah Foto Kegiatan' },
        { href: `/${scope}/catatan-keluarga/data-kegiatan-pkk-pokja-iii/report/pdf`, label: 'Buku Data Kegiatan' },
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
        },
        { href: `/${scope}/bantuans`, label: 'Buku Bantu Umum' },
        { href: `/${scope}/inventaris`, label: 'Buku Inventaris' },
        { href: `/${scope}/kader-khusus`, label: 'Buku Kader Khusus' },
        { href: '#', label: 'Buku ASI Eksklusif', uiVisibility: 'disabled' },
        { href: '#', label: 'Buku Rekapitulasi IVA Test', uiVisibility: 'disabled' },
        { href: '#', label: 'Buku Capaian Akseptor', uiVisibility: 'disabled' },
        { href: '#', label: 'Buku Catatan Pemeriksaan dan IVA Test', uiVisibility: 'disabled' },
        { href: `/${scope}/posyandu`, label: 'Buku Kegiatan Posyandu' },
        { href: '#', label: 'Data Pengunjung Petugas Posyandu', uiVisibility: 'disabled' },
        { href: `/${scope}/posyandu/report/pdf/chart`, label: 'Buku Grafik', external: true },
        { href: `/${scope}/foto-kegiatan`, label: 'Unggah Foto Kegiatan' },
        { href: `/${scope}/pilot-project-naskah-pelaporan`, label: 'Naskah Pelaporan Pilot Project' },
        { href: `/${scope}/pilot-project-keluarga-sehat`, label: 'Laporan Pilot Project Keluarga Sehat' },
        { href: `/${scope}/catatan-keluarga/data-kegiatan-pkk-pokja-iv/report/pdf`, label: 'Buku Data Kegiatan' },
      ],
    },
  ]

  // Filter out disabled items first
  const filteredGroups = rawGroups.map((group) => ({
    ...group,
    items: group.items.filter((item) => item.uiVisibility !== 'disabled'),
    printItems: pdfReportItemsByGroup[group.key] ?? [],
  })).filter((group) => group.items.length > 0 || group.printItems.length > 0)

  // Use a map to track the order and content of consolidated groups
  const groupMap = new Map()

  filteredGroups.forEach((group) => {
    // We want to keep groups like 'penunjang-buku-wajib' separate if needed,
    // but the user wants to consolidate based on label for Pokja.
    // To be safe, we consolidate only if the labels are EXACTLY the same AND they are not 'Buku Penunjang Buku Wajib'.
    const shouldConsolidate = group.label === 'Buku Wajib' || group.label === 'Buku Bantu'

    const consolidationKey = shouldConsolidate ? group.label : group.key

    if (!groupMap.has(consolidationKey)) {
      groupMap.set(consolidationKey, {
        ...group,
        items: [...group.items.map((item) => ({ ...withBookGroupContext(item, group.key), sourceKey: group.key }))],
        printItems: [...group.printItems.map((item) => ({ ...withBookGroupContext(item, group.key), sourceKey: group.key }))],
        originalKeys: [group.key], // Keep track of which backend keys are contributing
      })
    } else {
      const existing = groupMap.get(consolidationKey)
      existing.items.push(...group.items.map((item) => ({ ...withBookGroupContext(item, group.key), sourceKey: group.key })))
      existing.printItems.push(...group.printItems.map((item) => ({ ...withBookGroupContext(item, group.key), sourceKey: group.key })))
      existing.originalKeys.push(group.key)
    }
  })

  return Array.from(groupMap.values())
}

const desaMenuGroups = buildScopedMenuGroups('desa')

const kecamatanMenuGroups = [
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
