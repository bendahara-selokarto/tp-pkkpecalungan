<script setup>
import { computed, ref, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import CardBox from '@/admin-one/components/CardBox.vue'
import CardBoxWidget from '@/admin-one/components/CardBoxWidget.vue'
import BarChart from '@/admin-one/components/Charts/BarChart.vue'
import BaseButton from '@/admin-one/components/BaseButton.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import {
  mdiBookOpenVariant,
  mdiChartBar,
  mdiChartTimelineVariant,
  mdiClipboardList,
  mdiFileDocumentCheck,
  mdiFileDocumentMinus,
  mdiPrinter,
} from '@mdi/js'

const props = defineProps({
  dashboardBlocks: {
    type: Array,
    default: () => [],
  },
  dashboardStats: {
    type: Object,
    default: () => ({
      total: 0,
      this_month: 0,
      activity: {
        total: 0,
        this_month: 0,
      },
      documents: {
        total_buku_tracked: 0,
        buku_terisi: 0,
        buku_belum_terisi: 0,
        total_entri_buku: 0,
      },
    }),
  },
  dashboardCharts: {
    type: Object,
    default: () => ({
      documents: {
        coverage_per_buku: {
          labels: [],
          values: [],
        },
        coverage_per_lampiran: {
          labels: [],
          values: [],
        },
        level_distribution: {
          labels: ['Desa', 'Kecamatan'],
          values: [0, 0],
        },
      },
    }),
  },
})

const page = usePage()
const authUser = computed(() => page.props?.auth?.user ?? null)
const authRoles = computed(() => {
  const roles = authUser.value?.roles
  if (!Array.isArray(roles)) {
    return []
  }

  return roles.map((role) => String(role).toLowerCase())
})
const isDesaPokjaUser = computed(() =>
  authUser.value?.scope === 'desa'
  && authRoles.value.some((role) => role.startsWith('desa-pokja-')),
)
const isSekretarisUser = computed(() =>
  authRoles.value.includes('desa-sekretaris')
  || authRoles.value.includes('kecamatan-sekretaris')
  || authRoles.value.includes('admin-desa')
  || authRoles.value.includes('admin-kecamatan'),
)
const SECTION_GROUP_OPTIONS = [
  { value: 'all', label: 'Semua Pokja' },
  { value: 'pokja-i', label: 'Pokja I' },
  { value: 'pokja-ii', label: 'Pokja II' },
  { value: 'pokja-iii', label: 'Pokja III' },
  { value: 'pokja-iv', label: 'Pokja IV' },
]

const MODE_OPTIONS = [
  { value: 'all', label: 'Semua Data' },
  { value: 'by-level', label: 'Per Tingkat Wilayah' },
  { value: 'by-sub-level', label: 'Per Wilayah Turunan' },
]

const LEVEL_OPTIONS = [
  { value: 'all', label: 'Semua Tingkat' },
  { value: 'desa', label: 'Desa' },
  { value: 'kecamatan', label: 'Kecamatan' },
]

const SECTION1_MONTH_OPTIONS = [
  { value: 'all', label: 'Semua Bulan' },
  { value: '1', label: 'Januari' },
  { value: '2', label: 'Februari' },
  { value: '3', label: 'Maret' },
  { value: '4', label: 'April' },
  { value: '5', label: 'Mei' },
  { value: '6', label: 'Juni' },
  { value: '7', label: 'Juli' },
  { value: '8', label: 'Agustus' },
  { value: '9', label: 'September' },
  { value: '10', label: 'Oktober' },
  { value: '11', label: 'November' },
  { value: '12', label: 'Desember' },
]
const CHART_EMPTY_STATE_TEXT = 'Belum ada data untuk filter yang dipilih.'
const CHART_AXIS_LABEL_COLOR = '#64748b'
const CHART_AXIS_LABEL_FONT_SIZE = '11px'
const CHART_GRID_BORDER_COLOR = '#e2e8f0'
const CHART_GRID_STROKE_DASH = 4
const CHART_TOOLTIP_THEME = 'light'
const CHART_BAR_BORDER_RADIUS = 4
const CHART_PIE_COLORS = ['#06b6d4', '#f97316', '#ef4444', '#22c55e', '#a855f7', '#eab308', '#0f766e', '#db2777', '#1d4ed8', '#65a30d']

const USER_SECTION_LABELS = {
  'sekretaris-section-1': 'Ringkasan Tugas Sekretaris',
  'sekretaris-section-2': 'Ringkasan Pokja di Level Anda',
  'sekretaris-section-3': 'Ringkasan Pokja per Desa',
  'sekretaris-section-4': 'Rincian Pokja I per Desa',
}

const normalizeToken = (value, fallback = 'all') => {
  if (typeof value !== 'string') {
    return fallback
  }

  const token = value.trim().toLowerCase()
  return token === '' ? fallback : token
}

const parseQuery = (url) => {
  const raw = String(url ?? '')
  const query = raw.includes('?') ? raw.split('?')[1] : ''

  return new URLSearchParams(query)
}

const resolveOptionValue = (rawValue, options, fallback) => {
  const normalized = normalizeToken(rawValue, fallback)
  const allowed = options.map((option) => option.value)

  return allowed.includes(normalized) ? normalized : fallback
}

const currentQuery = parseQuery(page.url)
const selectedMode = ref(resolveOptionValue(currentQuery.get('mode'), MODE_OPTIONS, 'all'))
const selectedLevel = ref(resolveOptionValue(currentQuery.get('level'), LEVEL_OPTIONS, 'all'))
const selectedSubLevel = ref(normalizeToken(currentQuery.get('sub_level'), 'all'))
const selectedSection1Month = ref(resolveOptionValue(currentQuery.get('section1_month'), SECTION1_MONTH_OPTIONS, 'all'))
const selectedSection2Group = ref(resolveOptionValue(currentQuery.get('section2_group'), SECTION_GROUP_OPTIONS, 'all'))
const selectedSection3Group = ref(resolveOptionValue(currentQuery.get('section3_group'), SECTION_GROUP_OPTIONS, 'all'))
const dashboardChartPdfUrl = computed(() => {
  const params = parseQuery(page.url)
  const query = params.toString()

  return query === '' ? '/dashboard/charts/report/pdf' : `/dashboard/charts/report/pdf?${query}`
})

const dynamicBlocks = computed(() =>
  Array.isArray(props.dashboardBlocks)
    ? props.dashboardBlocks.filter((block) => block && typeof block === 'object')
    : [],
)

const hasDynamicBlocks = computed(() => dynamicBlocks.value.length > 0)
const showLegacyFallback = computed(() =>
  !hasDynamicBlocks.value && Boolean(import.meta.env.DEV),
)
const sekretarisSection1Blocks = computed(() =>
  dynamicBlocks.value.filter((block) => block?.section?.key === 'sekretaris-section-1'),
)
const sekretarisSection2Blocks = computed(() =>
  dynamicBlocks.value.filter((block) => block?.section?.key === 'sekretaris-section-2'),
)
const sekretarisSection3Blocks = computed(() =>
  dynamicBlocks.value.filter((block) => block?.section?.key === 'sekretaris-section-3'),
)
const sekretarisSection4Blocks = computed(() =>
  dynamicBlocks.value.filter((block) => block?.section?.key === 'sekretaris-section-4'),
)
const hasSekretarisLowerSection = computed(() => sekretarisSection3Blocks.value.length > 0)
const hasSekretarisFourthSection = computed(() => sekretarisSection4Blocks.value.length > 0)
const hasSekretarisSections = computed(() =>
  isSekretarisUser.value
  && (
    sekretarisSection1Blocks.value.length > 0
    || sekretarisSection2Blocks.value.length > 0
    || sekretarisSection3Blocks.value.length > 0
    || sekretarisSection4Blocks.value.length > 0
  ),
)
const shouldShowGlobalDashboardFilters = computed(() =>
  !isDesaPokjaUser.value && !hasSekretarisSections.value,
)

const expandedBlockKeys = ref({})

const syncExpandedBlocks = (sections) => {
  const next = {}

  sections.forEach((section) => {
    const blocks = Array.isArray(section?.blocks) ? section.blocks : []
    blocks.forEach((block, index) => {
      const key = String(block?.key ?? '')
      if (key === '') {
        return
      }

      next[key] = expandedBlockKeys.value[key] ?? index === 0
    })
  })

  expandedBlockKeys.value = next
}

const isBlockExpanded = (blockKey) => expandedBlockKeys.value[String(blockKey)] !== false

const toggleBlockExpanded = (blockKey) => {
  const key = String(blockKey ?? '')
  if (key === '') {
    return
  }

  expandedBlockKeys.value = {
    ...expandedBlockKeys.value,
    [key]: !isBlockExpanded(key),
  }
}

const filterBlocksByGroup = (blocks, selectedGroup) => {
  if (selectedGroup === 'all') {
    return blocks
  }

  return blocks.filter((block) => normalizeToken(block?.group, 'all') === selectedGroup)
}

const filteredSekretarisSection2Blocks = computed(() =>
  filterBlocksByGroup(sekretarisSection2Blocks.value, selectedSection2Group.value),
)
const filteredSekretarisSection3Blocks = computed(() =>
  filterBlocksByGroup(sekretarisSection3Blocks.value, selectedSection3Group.value),
)

const resolveSectionLabel = (sectionKey, blocks, fallback) => {
  const firstBlock = blocks[0]
  const sectionLabel = firstBlock?.section?.label

  if (typeof sectionLabel === 'string' && sectionLabel.trim() !== '') {
    return sectionLabel
  }

  if (typeof USER_SECTION_LABELS[sectionKey] === 'string') {
    return USER_SECTION_LABELS[sectionKey]
  }

  return fallback
}

const resolveSectionDescription = (sectionKey) => {
  if (sectionKey === 'sekretaris-section-1') {
    return 'Ringkasan untuk peran sekretaris ditampilkan tanpa filter pokja.'
  }

  if (sectionKey === 'sekretaris-section-4') {
    return 'Rincian Pokja I per desa turunan mengikuti pilihan pada bagian sebelumnya.'
  }

  return 'Gunakan pilihan pokja untuk fokus ke kelompok tertentu atau tampilkan semuanya.'
}

const resolveSectionFilter = (blocks, fallbackQueryKey) => {
  const firstBlock = blocks[0]
  const filter = firstBlock?.section?.filter

  if (!filter || typeof filter !== 'object') {
    return null
  }

  const options = Array.isArray(filter.options) && filter.options.length > 0
    ? filter.options
    : SECTION_GROUP_OPTIONS

  const queryKey = typeof filter.query_key === 'string' && filter.query_key.trim() !== ''
    ? filter.query_key
    : fallbackQueryKey

  return {
    queryKey,
    options,
  }
}

const dashboardSections = computed(() => {
  if (!hasSekretarisSections.value) {
    return [{
      key: 'default',
      label: 'Dashboard',
      filter: null,
      blocks: dynamicBlocks.value,
    }]
  }

  const sections = [{
    key: 'sekretaris-section-1',
    label: resolveSectionLabel('sekretaris-section-1', sekretarisSection1Blocks.value, 'Ringkasan Tugas Sekretaris'),
    filter: null,
    blocks: sekretarisSection1Blocks.value,
  }]

  if (sekretarisSection2Blocks.value.length > 0) {
    sections.push({
      key: 'sekretaris-section-2',
      label: resolveSectionLabel('sekretaris-section-2', sekretarisSection2Blocks.value, 'Ringkasan Pokja di Level Anda'),
      filter: resolveSectionFilter(sekretarisSection2Blocks.value, 'section2_group'),
      blocks: filteredSekretarisSection2Blocks.value,
    })
  }

  if (sekretarisSection3Blocks.value.length > 0) {
    sections.push({
      key: 'sekretaris-section-3',
      label: resolveSectionLabel('sekretaris-section-3', sekretarisSection3Blocks.value, 'Ringkasan Pokja per Desa'),
      filter: resolveSectionFilter(sekretarisSection3Blocks.value, 'section3_group'),
      blocks: filteredSekretarisSection3Blocks.value,
    })
  }

  if (hasSekretarisFourthSection.value) {
    sections.push({
      key: 'sekretaris-section-4',
      label: resolveSectionLabel('sekretaris-section-4', sekretarisSection4Blocks.value, 'Rincian Pokja I per Desa'),
      filter: null,
      blocks: sekretarisSection4Blocks.value,
    })
  }

  return sections
})

const visibleDashboardSections = computed(() => dashboardSections.value)
const visibleDashboardBlocks = computed(() =>
  visibleDashboardSections.value.flatMap((section) =>
    Array.isArray(section?.blocks) ? section.blocks : []),
)
const blockSupportsMonthFilter = (block) =>
  block?.kind === 'activity'
  && ['labels', 'values', 'books_total', 'books_filled'].some((metricKey) => {
    const values = block?.charts?.by_desa?.[metricKey]
    return Array.isArray(values) && values.length > 0
  })
const hasMonthFilterAwareBlocks = computed(() =>
  visibleDashboardBlocks.value.some((block) => blockSupportsMonthFilter(block)),
)
const normalizedSection1Month = computed(() =>
  hasMonthFilterAwareBlocks.value ? selectedSection1Month.value : 'all',
)

watch(
  visibleDashboardSections,
  (sections) => {
    syncExpandedBlocks(sections)
  },
  { immediate: true },
)

const sekretarisDefaultLevel = computed(() =>
  authUser.value?.scope === 'kecamatan' ? 'kecamatan' : 'desa',
)

watch(
  () => page.url,
  (url) => {
    const params = parseQuery(url)
    selectedMode.value = resolveOptionValue(params.get('mode'), MODE_OPTIONS, 'all')
    selectedLevel.value = resolveOptionValue(params.get('level'), LEVEL_OPTIONS, 'all')
    selectedSubLevel.value = normalizeToken(params.get('sub_level'), 'all')
    selectedSection1Month.value = resolveOptionValue(params.get('section1_month'), SECTION1_MONTH_OPTIONS, 'all')
    selectedSection2Group.value = resolveOptionValue(params.get('section2_group'), SECTION_GROUP_OPTIONS, 'all')
    selectedSection3Group.value = resolveOptionValue(params.get('section3_group'), SECTION_GROUP_OPTIONS, 'all')
  },
)

const isByLevelMode = computed(() => selectedMode.value === 'by-level')
const isBySubLevelMode = computed(() => selectedMode.value === 'by-sub-level')

const buildGlobalFilterQuery = () => ({
  mode: selectedMode.value,
  level: isByLevelMode.value ? selectedLevel.value : 'all',
  sub_level: isBySubLevelMode.value ? selectedSubLevel.value : 'all',
  section1_month: normalizedSection1Month.value,
})

const applyFilters = () => {
  router.get('/dashboard', buildGlobalFilterQuery(), {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  })
}

const onModeChange = () => {
  if (!isByLevelMode.value) {
    selectedLevel.value = 'all'
  }

  if (!isBySubLevelMode.value) {
    selectedSubLevel.value = 'all'
  }

  applyFilters()
}

const onLevelChange = () => {
  applyFilters()
}

const onSubLevelApply = () => {
  selectedSubLevel.value = normalizeToken(selectedSubLevel.value, 'all')
  applyFilters()
}

const applySekretarisSectionFilters = () => {
  router.get('/dashboard', {
    mode: 'by-level',
    level: sekretarisDefaultLevel.value,
    sub_level: 'all',
    section1_month: normalizedSection1Month.value,
    section2_group: selectedSection2Group.value,
    section3_group: hasSekretarisLowerSection.value ? selectedSection3Group.value : 'all',
  }, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  })
}

const onSection1MonthChange = () => {
  selectedSection1Month.value = resolveOptionValue(selectedSection1Month.value, SECTION1_MONTH_OPTIONS, 'all')
  if (!hasMonthFilterAwareBlocks.value) {
    selectedSection1Month.value = 'all'
  }

  if (hasSekretarisSections.value) {
    applySekretarisSectionFilters()
    return
  }

  applyFilters()
}

const resolveSectionGroupFilterValue = (queryKey) => {
  if (queryKey === 'section2_group') {
    return selectedSection2Group.value
  }

  if (queryKey === 'section3_group') {
    return selectedSection3Group.value
  }

  return 'all'
}

const onSectionGroupFilterChange = (queryKey, rawValue) => {
  if (queryKey === 'section2_group') {
    selectedSection2Group.value = resolveOptionValue(rawValue, SECTION_GROUP_OPTIONS, 'all')
    applySekretarisSectionFilters()
    return
  }

  if (queryKey === 'section3_group') {
    selectedSection3Group.value = resolveOptionValue(rawValue, SECTION_GROUP_OPTIONS, 'all')
    applySekretarisSectionFilters()
  }
}

watch(
  isDesaPokjaUser,
  (isPokjaDesa) => {
    if (!isPokjaDesa) {
      return
    }

    if (
      selectedMode.value === 'all'
      && selectedLevel.value === 'all'
      && selectedSubLevel.value === 'all'
    ) {
      return
    }

    selectedMode.value = 'all'
    selectedLevel.value = 'all'
    selectedSubLevel.value = 'all'
    applyFilters()
  },
  { immediate: true },
)

watch(
  [hasSekretarisSections, () => page.url],
  ([hasSectionModel, url]) => {
    if (!hasSectionModel) {
      return
    }

    const params = parseQuery(url)
    const expectedQuery = {
      mode: 'by-level',
      level: sekretarisDefaultLevel.value,
      sub_level: 'all',
      section1_month: normalizedSection1Month.value,
      section2_group: selectedSection2Group.value,
      section3_group: hasSekretarisLowerSection.value ? selectedSection3Group.value : 'all',
    }

    const isSynced = normalizeToken(params.get('mode'), 'all') === expectedQuery.mode
      && normalizeToken(params.get('level'), 'all') === expectedQuery.level
      && normalizeToken(params.get('sub_level'), 'all') === expectedQuery.sub_level
      && normalizeToken(params.get('section1_month'), 'all') === expectedQuery.section1_month
      && normalizeToken(params.get('section2_group'), 'all') === expectedQuery.section2_group
      && normalizeToken(params.get('section3_group'), 'all') === expectedQuery.section3_group

    if (isSynced) {
      return
    }

    router.get('/dashboard', expectedQuery, {
      preserveState: true,
      preserveScroll: true,
      replace: true,
    })
  },
  { immediate: true },
)

watch(
  hasMonthFilterAwareBlocks,
  (isMonthFilterRelevant) => {
    if (isMonthFilterRelevant || selectedSection1Month.value === 'all') {
      return
    }

    selectedSection1Month.value = 'all'
  },
  { immediate: true },
)

watch(
  [hasSekretarisSections, hasMonthFilterAwareBlocks, () => page.url],
  ([isSekretarisView, isMonthFilterRelevant, url]) => {
    if (isSekretarisView || isMonthFilterRelevant) {
      return
    }

    const params = parseQuery(url)
    if (normalizeToken(params.get('section1_month'), 'all') === 'all') {
      return
    }

    router.get('/dashboard', buildGlobalFilterQuery(), {
      preserveState: true,
      preserveScroll: true,
      replace: true,
    })
  },
  { immediate: true },
)

const humanizeLabel = (value) => String(value ?? '')
  .replace(/[-_]+/g, ' ')
  .trim()
  .replace(/\b\w/g, (char) => char.toUpperCase())

const toSlugToken = (value) => String(value ?? '')
  .toLowerCase()
  .trim()
  .replace(/[^a-z0-9]+/g, '-')
  .replace(/^-+|-+$/g, '')

const availableSubLevelOptions = computed(() => {
  const collectedNames = new Set()

  dynamicBlocks.value.forEach((block) => {
    const coverage = block?.charts?.coverage_per_module
    if (normalizeToken(coverage?.dimension, '') === 'desa') {
      const items = Array.isArray(coverage?.items) ? coverage.items : []
      items.forEach((item) => {
        const rawLabel = String(item?.label ?? '').trim()
        if (rawLabel !== '') {
          collectedNames.add(rawLabel)
        }
      })
    }

    const byDesaLabels = Array.isArray(block?.charts?.by_desa?.labels)
      ? block.charts.by_desa.labels
      : []
    byDesaLabels.forEach((label) => {
      const rawLabel = String(label ?? '').trim()
      if (rawLabel !== '') {
        collectedNames.add(rawLabel)
      }
    })
  })

  const options = Array.from(collectedNames)
    .sort((a, b) => a.localeCompare(b, 'id'))
    .map((name) => ({
      value: `desa-${toSlugToken(name)}`,
      label: `Desa ${name}`,
    }))

  return [{ value: 'all', label: 'Semua Wilayah' }, ...options]
})

const toNumber = (value) => Number(value ?? 0)
const buildAxisLabelStyles = (labels) => ({
  colors: (labels ?? []).map(() => CHART_AXIS_LABEL_COLOR),
  fontSize: CHART_AXIS_LABEL_FONT_SIZE,
})
const buildChartNoData = () => ({
  text: CHART_EMPTY_STATE_TEXT,
  align: 'center',
  verticalAlign: 'middle',
})
const buildCartesianGrid = () => ({
  borderColor: CHART_GRID_BORDER_COLOR,
  strokeDashArray: CHART_GRID_STROKE_DASH,
})
const formatPieAbsoluteValue = (_value, options) => {
  const seriesIndex = toNumber(options?.seriesIndex)
  const seriesValue = toNumber(options?.w?.config?.series?.[seriesIndex] ?? 0)

  return seriesValue.toLocaleString('id-ID')
}

const buildSingleDataset = (labels, values, backgroundColor) => ({
  labels,
  datasets: [
    {
      data: values,
      backgroundColor,
      borderRadius: 6,
    },
  ],
})

const sourceModulesLabel = (block) => {
  const modules = block?.sources?.source_modules
  if (!Array.isArray(modules) || modules.length === 0) {
    return '-'
  }

  return modules.map((moduleSlug) => humanizeLabel(moduleSlug)).join(', ')
}

const sourceAreaTypeLabel = (block) => {
  const sourceAreaType = normalizeToken(block?.sources?.source_area_type, '-')

  if (sourceAreaType === 'area-sendiri+desa-turunan') {
    return 'Area sendiri dan desa turunan'
  }

  if (sourceAreaType === 'area-sendiri') {
    return 'Area sendiri'
  }

  if (sourceAreaType === 'desa-turunan') {
    return 'Desa turunan'
  }

  return humanizeLabel(sourceAreaType)
}

const resolveModeContextLabel = (mode) => {
  const token = normalizeToken(mode, 'all')
  if (token === 'by-level') {
    return 'Per Tingkat Wilayah'
  }

  if (token === 'by-sub-level') {
    return 'Per Wilayah Turunan'
  }

  return 'Semua Data'
}

const resolveLevelContextLabel = (level) => {
  const token = normalizeToken(level, 'all')
  if (token === 'desa') {
    return 'Desa'
  }

  if (token === 'kecamatan') {
    return 'Kecamatan'
  }

  return 'Semua Tingkat'
}

const resolveSubLevelContextLabel = (subLevel) => {
  const token = normalizeToken(subLevel, 'all')
  return token === 'all' ? 'Semua Wilayah Turunan' : humanizeLabel(token)
}

const resolveGroupContextLabel = (group) => {
  const token = normalizeToken(group, 'all')
  if (token === 'all') {
    return 'Semua Pokja'
  }

  return humanizeLabel(token)
}

const resolveFilterContextSummary = (context) => {
  const modeLabel = resolveModeContextLabel(context?.mode)
  const modeToken = normalizeToken(context?.mode, 'all')

  if (modeToken === 'by-sub-level') {
    return `Cara Tampil ${modeLabel} | Wilayah Turunan ${resolveSubLevelContextLabel(context?.sub_level)}`
  }

  return `Cara Tampil ${modeLabel} | Tingkat Wilayah ${resolveLevelContextLabel(context?.level)}`
}

const resolveBlockAccessModeLabel = (block) => {
  const mode = normalizeToken(block?.mode, '')

  if (mode === 'read-only') {
    return 'Akses baca saja'
  }

  if (mode === 'read-write') {
    return 'Akses baca dan ubah'
  }

  return 'Akses mengikuti hak akun'
}

const blockSummaryLabel = (block) => `Data dari ${sourceModulesLabel(block)} | Cakupan ${sourceAreaTypeLabel(block)}`

const buildBlockStats = (block) => {
  const groupLabel = String(block?.group_label ?? 'Dashboard').trim()

  if (block?.kind === 'activity') {
    const stats = block?.stats ?? {}
    const activityStats = [
      {
        key: 'activity-total',
        icon: mdiClipboardList,
        label: 'Total Kegiatan',
        number: toNumber(stats.total),
        color: 'text-blue-500',
      },
      {
        key: 'activity-this-month',
        icon: mdiChartTimelineVariant,
        label: 'Kegiatan Bulan Ini',
        number: toNumber(stats.this_month),
        color: 'text-indigo-500',
      },
    ]

    if (shouldShowActivityByDesaChart(block)) {
      const { syncedTotalBookValues, syncedFilledBookValues } = resolveActivityByDesaMetrics(block)
      const totalBooks = syncedTotalBookValues.reduce((total, value) => total + toNumber(value), 0)
      const filledBooks = syncedFilledBookValues.reduce((total, value) => total + toNumber(value), 0)

      activityStats.push(
        {
          key: 'activity-total-book',
          icon: mdiBookOpenVariant,
          label: 'Jumlah Buku',
          number: totalBooks,
          color: 'text-cyan-500',
        },
        {
          key: 'activity-filled-book',
          icon: mdiFileDocumentCheck,
          label: 'Buku Terisi',
          number: filledBooks,
          color: 'text-emerald-500',
        },
      )
    }

    return activityStats
  }

  const stats = block?.stats ?? {}

  return [
    {
      key: 'documents-total-book',
      icon: mdiBookOpenVariant,
      label: `Total Buku ${groupLabel}`,
      number: toNumber(stats.total_buku_tracked),
      color: 'text-cyan-500',
    },
    {
      key: 'documents-filled',
      icon: mdiFileDocumentCheck,
      label: `Buku Terisi ${groupLabel}`,
      number: toNumber(stats.buku_terisi),
      color: 'text-emerald-500',
    },
    {
      key: 'documents-empty',
      icon: mdiFileDocumentMinus,
      label: `Buku Belum Terisi ${groupLabel}`,
      number: toNumber(stats.buku_belum_terisi),
      color: 'text-rose-500',
    },
    {
      key: 'documents-total-entry',
      icon: mdiChartBar,
      label: `Total Entri ${groupLabel}`,
      number: toNumber(stats.total_entri_buku),
      color: 'text-violet-500',
    },
  ]
}

const resolveBlockKindLabel = (kind) => (kind === 'activity' ? 'Kegiatan' : 'Dokumen')

const resolveBlockModeDescription = (mode) =>
  (mode === 'read-only' ? 'Hanya Lihat' : 'Bisa Lihat dan Ubah')

const buildLevelRows = (block) => {
  if (block?.kind === 'activity') {
    const labels = block?.charts?.level?.labels ?? []
    const values = block?.charts?.level?.values ?? []

    if (!Array.isArray(labels) || !Array.isArray(values) || labels.length === 0) {
      return []
    }

    return labels.map((label, index) => ({
      info: `Level ${String(label)}`,
      value: toNumber(values[index] ?? 0).toLocaleString('id-ID'),
      note: 'Jumlah kegiatan pada tingkat wilayah ini.',
    }))
  }

  const items = block?.charts?.coverage_per_module?.items
  if (!Array.isArray(items) || items.length === 0) {
    return []
  }

  const hasLevelBreakdown = items.some((item) => item && (item.desa !== undefined || item.kecamatan !== undefined))
  if (!hasLevelBreakdown) {
    return []
  }

  const levelDesa = items.reduce((total, item) => total + toNumber(item?.desa ?? 0), 0)
  const levelKecamatan = items.reduce((total, item) => total + toNumber(item?.kecamatan ?? 0), 0)

  return [
    {
      info: 'Level Desa',
      value: levelDesa.toLocaleString('id-ID'),
      note: 'Total entri yang berasal dari desa.',
    },
    {
      info: 'Level Kecamatan',
      value: levelKecamatan.toLocaleString('id-ID'),
      note: 'Total entri yang berasal dari kecamatan.',
    },
  ]
}

const buildInformativeTableRows = (block) => {
  const context = block?.sources?.filter_context ?? {}
  const levelRows = buildLevelRows(block)
  const statsRows = buildBlockStats(block).map((item) => ({
    info: item.label,
    value: toNumber(item.number).toLocaleString('id-ID'),
    note: block?.kind === 'activity'
      ? 'Ringkasan angka kegiatan pada tampilan saat ini.'
      : 'Ringkasan angka dokumen pada tampilan saat ini.',
  }))

  const profileRows = [
    {
      info: 'Jenis Ringkasan',
      value: resolveBlockKindLabel(block?.kind),
      note: 'Menjelaskan kategori ringkasan data.',
    },
    {
      info: 'Hak Akses',
      value: resolveBlockModeDescription(block?.mode),
      note: 'Hak lihat/ubah sesuai peran akun Anda.',
    },
    {
      info: 'Jangkauan Wilayah',
      value: sourceAreaTypeLabel(block),
      note: 'Menjelaskan wilayah asal data yang dihitung.',
    },
    {
      info: 'Pilihan Tampilan Aktif',
      value: resolveFilterContextSummary(context),
      note: 'Pilihan tampilan yang sedang Anda pakai.',
    },
  ]

  return [...profileRows, ...levelRows, ...statsRows]
}

const resolveDocumentCoverageItems = (block) => {
  const rawItems = block?.charts?.coverage_per_module?.items
  if (Array.isArray(rawItems) && rawItems.length > 0) {
    return rawItems.map((item, index) => ({
      label: String(item?.label ?? humanizeLabel(item?.slug ?? `Modul ${index + 1}`)),
      total: toNumber(item?.resolved_total ?? item?.total ?? 0),
    }))
  }

  const labels = block?.charts?.coverage_per_module?.labels ?? []
  const values = block?.charts?.coverage_per_module?.values ?? []

  return labels.map((label, index) => ({
    label: humanizeLabel(label),
    total: toNumber(values[index] ?? 0),
  }))
}

const resolveCoverageDimension = (block) =>
  normalizeToken(block?.charts?.coverage_per_module?.dimension, 'module')

const resolveCoverageChartHeading = (block) =>
  (resolveCoverageDimension(block) === 'desa' ? 'Cakupan Per Desa' : 'Cakupan Per Modul')

const resolveCoverageListHeading = (block) =>
  (resolveCoverageDimension(block) === 'desa' ? 'Nilai Per Desa' : 'Nilai Per Modul')

const resolveCoverageItemDetail = (item) => {
  const perModule = item?.per_module
  if (!perModule || typeof perModule !== 'object') {
    return ''
  }

  const entries = Object.entries(perModule)
    .filter(([, count]) => toNumber(count) > 0)
    .map(([slug, count]) => `${humanizeLabel(slug)}: ${toNumber(count)}`)

  return entries.join(' | ')
}

const buildDocumentCoverageChartData = (block) => {
  const items = resolveDocumentCoverageItems(block)

  return buildSingleDataset(
    items.map((item) => item.label),
    items.map((item) => item.total),
    '#10b981',
  )
}

const hasDocumentCoverageData = (block) =>
  resolveDocumentCoverageItems(block).some((item) => item.total > 0)

const buildActivityMonthlyMultiAxisSeries = (block) => {
  const values = (block?.charts?.monthly?.values ?? []).map((value) => toNumber(value))
  const cumulativeValues = values.reduce((result, value, index) => {
    const previous = index > 0 ? result[index - 1] : 0
    result.push(previous + value)
    return result
  }, [])

  return [
    {
      name: 'Jumlah Kegiatan',
      type: 'bar',
      data: values,
    },
    {
      name: 'Akumulasi 6 Bulan',
      type: 'bar',
      data: cumulativeValues,
    },
  ]
}

const buildActivityMonthlyMultiAxisOptions = (block) => {
  const labels = block?.charts?.monthly?.labels ?? []
  const axisLabelStyles = buildAxisLabelStyles(labels)

  return {
    chart: {
      type: 'bar',
      toolbar: {
        show: false,
      },
      animations: {
        enabled: true,
      },
    },
    plotOptions: {
      bar: {
        horizontal: false,
        distributed: false,
        columnWidth: '55%',
        borderRadius: CHART_BAR_BORDER_RADIUS,
      },
    },
    colors: ['#0ea5e9', '#6366f1'],
    dataLabels: {
      enabled: false,
    },
    grid: buildCartesianGrid(),
    tooltip: {
      theme: CHART_TOOLTIP_THEME,
    },
    legend: {
      show: true,
      position: 'top',
      horizontalAlign: 'left',
    },
    xaxis: {
      categories: labels,
      labels: {
        style: axisLabelStyles,
      },
      axisBorder: {
        show: false,
      },
      axisTicks: {
        show: false,
      },
    },
    yaxis: [
      {
        title: {
          text: 'Jumlah Kegiatan',
        },
        labels: {
          style: {
            colors: ['#0ea5e9'],
          },
        },
      },
      {
        opposite: true,
        title: {
          text: 'Akumulasi',
        },
        labels: {
          style: {
            colors: ['#6366f1'],
          },
        },
      },
    ],
    noData: buildChartNoData(),
  }
}

const hasActivityMonthlyData = (block) =>
  (block?.charts?.monthly?.values ?? []).some((value) => toNumber(value) > 0)

const hasByDesaActivityMetrics = (block) => {
  const byDesa = block?.charts?.by_desa
  if (!byDesa || typeof byDesa !== 'object') {
    return false
  }

  return ['labels', 'values', 'books_total', 'books_filled'].some((metricKey) => {
    const values = byDesa?.[metricKey]
    return Array.isArray(values) && values.length > 0
  })
}

const shouldShowActivityByDesaChart = (block) =>
  hasByDesaActivityMetrics(block)
const activityByDesaChartModeLabel = () =>
  'Grafik pai ditampilkan di sisi kiri, dan grafik batang ditampilkan di sisi kanan.'

const resolveActivityByDesaMetrics = (block) => {
  const labels = block?.charts?.by_desa?.labels ?? []
  const activityValues = (block?.charts?.by_desa?.values ?? []).map((value) => toNumber(value))
  const totalBookValues = (block?.charts?.by_desa?.books_total ?? []).map((value) => toNumber(value))
  const filledBookValues = (block?.charts?.by_desa?.books_filled ?? []).map((value) => toNumber(value))
  const syncedActivityValues = labels.map((_, index) => toNumber(activityValues[index] ?? 0))
  const syncedTotalBookValues = labels.map((_, index) => toNumber(totalBookValues[index] ?? 0))
  const syncedFilledBookValues = labels.map((_, index) => toNumber(filledBookValues[index] ?? 0))

  return {
    labels,
    syncedActivityValues,
    syncedTotalBookValues,
    syncedFilledBookValues,
  }
}

const buildActivityByDesaKegiatanSeries = (block) => {
  const { syncedActivityValues } = resolveActivityByDesaMetrics(block)

  return syncedActivityValues
}

const buildActivityByDesaKegiatanOptions = (block) => {
  const { labels } = resolveActivityByDesaMetrics(block)

  return {
    chart: {
      type: 'pie',
      toolbar: {
        show: false,
      },
      animations: {
        enabled: true,
      },
    },
    labels,
    colors: CHART_PIE_COLORS,
    dataLabels: {
      enabled: true,
      formatter: formatPieAbsoluteValue,
    },
    legend: {
      show: true,
      position: 'bottom',
    },
    tooltip: {
      theme: CHART_TOOLTIP_THEME,
    },
    noData: buildChartNoData(),
  }
}
const buildActivityByDesaBookCoverageSeries = (block) => {
  const { syncedTotalBookValues, syncedFilledBookValues } = resolveActivityByDesaMetrics(block)

  return [
    {
      name: 'Jumlah Buku',
      type: 'bar',
      data: syncedTotalBookValues,
    },
    {
      name: 'Buku Terisi',
      type: 'bar',
      data: syncedFilledBookValues,
    },
  ]
}

const buildActivityByDesaBookCoverageOptions = (block) => {
  const { labels } = resolveActivityByDesaMetrics(block)
  const axisLabelStyles = buildAxisLabelStyles(labels)

  return {
    chart: {
      type: 'bar',
      toolbar: {
        show: false,
      },
      animations: {
        enabled: true,
      },
    },
    plotOptions: {
      bar: {
        horizontal: false,
        columnWidth: '45%',
        borderRadius: CHART_BAR_BORDER_RADIUS,
      },
    },
    colors: ['#7e22ce', '#16a34a'],
    dataLabels: {
      enabled: false,
    },
    legend: {
      show: true,
      position: 'top',
      horizontalAlign: 'left',
    },
    grid: buildCartesianGrid(),
    tooltip: {
      theme: CHART_TOOLTIP_THEME,
    },
    xaxis: {
      categories: labels,
      labels: {
        style: axisLabelStyles,
      },
      axisBorder: {
        show: false,
      },
      axisTicks: {
        show: false,
      },
    },
    yaxis: {
      title: {
        text: 'Jumlah Buku',
      },
      labels: {
        style: {
          colors: [CHART_AXIS_LABEL_COLOR],
        },
      },
    },
    noData: buildChartNoData(),
  }
}

const hasActivityByDesaActivityData = (block) =>
  (block?.charts?.by_desa?.values ?? []).some((value) => toNumber(value) > 0)

const hasActivityByDesaBookCoverageData = (block) =>
  ['books_total', 'books_filled'].some((metricKey) =>
    (block?.charts?.by_desa?.[metricKey] ?? []).some((value) => toNumber(value) > 0))

const buildActivityLevelChartData = (block) => {
  const labels = block?.charts?.level?.labels ?? ['Desa', 'Kecamatan']
  const values = (block?.charts?.level?.values ?? [0, 0]).map((value) => toNumber(value))

  return buildSingleDataset(labels, values, ['#06b6d4', '#6366f1'])
}

// Legacy fallback while dynamic blocks are still rolling out.
const activityStats = computed(() => props.dashboardStats.activity ?? {
  total: props.dashboardStats.total ?? 0,
  this_month: props.dashboardStats.this_month ?? 0,
})

const documentStats = computed(() => props.dashboardStats.documents ?? {
  total_buku_tracked: 0,
  buku_terisi: 0,
  buku_belum_terisi: 0,
  total_entri_buku: 0,
})

const documentCharts = computed(() => props.dashboardCharts.documents ?? {
  coverage_per_buku: { labels: [], values: [] },
  coverage_per_lampiran: { labels: [], values: [] },
  level_distribution: { labels: ['Desa', 'Kecamatan'], values: [0, 0] },
})

const legacyCoveragePerBukuItems = computed(() => {
  const rawItems = documentCharts.value.coverage_per_buku?.items
  if (Array.isArray(rawItems) && rawItems.length > 0) {
    return rawItems.map((item, index) => ({
      label: String(item?.label ?? humanizeLabel(item?.slug ?? `Buku ${index + 1}`)),
      total: toNumber(item?.total ?? 0),
    }))
  }

  const labels = documentCharts.value.coverage_per_buku?.labels ?? []
  const values = documentCharts.value.coverage_per_buku?.values ?? []

  return labels.map((label, index) => ({
    label: humanizeLabel(label),
    total: toNumber(values[index] ?? 0),
  }))
})

const legacyCoveragePerBukuChartData = computed(() => buildSingleDataset(
  legacyCoveragePerBukuItems.value.map((item) => item.label),
  legacyCoveragePerBukuItems.value.map((item) => item.total),
  '#10b981',
))

const legacyCoveragePerLampiranChartData = computed(() => buildSingleDataset(
  documentCharts.value.coverage_per_lampiran?.labels ?? [],
  (documentCharts.value.coverage_per_lampiran?.values ?? []).map((value) => toNumber(value)),
  '#0ea5e9',
))

const legacyLevelDistributionChartData = computed(() => buildSingleDataset(
  documentCharts.value.level_distribution?.labels ?? ['Desa', 'Kecamatan'],
  (documentCharts.value.level_distribution?.values ?? [0, 0]).map((value) => toNumber(value)),
  ['#f59e0b', '#6366f1'],
))

const legacyLampiranCoverageItems = computed(() => {
  const rawItems = documentCharts.value.coverage_per_lampiran?.items
  if (Array.isArray(rawItems) && rawItems.length > 0) {
    return rawItems.map((item) => ({
      label: String(item?.lampiran_group ?? '-'),
      total: toNumber(item?.total ?? 0),
    }))
  }

  const labels = documentCharts.value.coverage_per_lampiran?.labels ?? []
  const values = documentCharts.value.coverage_per_lampiran?.values ?? []

  return labels.map((label, index) => ({
    label: String(label),
    total: toNumber(values[index] ?? 0),
  }))
})

const legacyLevelDistributionItems = computed(() => {
  const labels = documentCharts.value.level_distribution?.labels ?? ['Desa', 'Kecamatan']
  const values = documentCharts.value.level_distribution?.values ?? [0, 0]

  return labels.map((label, index) => ({
    label: String(label),
    total: toNumber(values[index] ?? 0),
  }))
})

const hasLegacyLampiranCoverageData = computed(() =>
  legacyLampiranCoverageItems.value.some((item) => item.total > 0),
)

const hasLegacyLevelDistributionData = computed(() =>
  legacyLevelDistributionItems.value.some((item) => item.total > 0),
)
</script>

<template>
  <SectionMain class="!pt-2">
    <SectionTitleLineWithButton :icon="mdiChartTimelineVariant" title="Dashboard" main>
      <BaseButton
        :icon="mdiPrinter"
        label="Cetak Chart PDF"
        color="info"
        :href="dashboardChartPdfUrl"
        target="_blank"
        small
      />
    </SectionTitleLineWithButton>

    <CardBox v-if="shouldShowGlobalDashboardFilters" class="mb-6">
      <div class="grid grid-cols-1 gap-4 lg:grid-cols-4">
        <div>
          <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
            Cara Tampil
          </label>
          <select
            v-model="selectedMode"
            class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-800"
            @change="onModeChange"
          >
            <option v-for="modeOption in MODE_OPTIONS" :key="modeOption.value" :value="modeOption.value">
              {{ modeOption.label }}
            </option>
          </select>
        </div>

        <div>
          <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
            Cakupan Wilayah
          </label>
          <select
            v-model="selectedLevel"
            :disabled="!isByLevelMode"
            class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm disabled:cursor-not-allowed disabled:opacity-60 dark:border-slate-600 dark:bg-slate-800"
            @change="onLevelChange"
          >
            <option v-for="levelOption in LEVEL_OPTIONS" :key="levelOption.value" :value="levelOption.value">
              {{ levelOption.label }}
            </option>
          </select>
        </div>

        <div>
          <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
            Wilayah Turunan
          </label>
          <select
            v-if="availableSubLevelOptions.length > 1"
            v-model="selectedSubLevel"
            :disabled="!isBySubLevelMode"
            class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm disabled:cursor-not-allowed disabled:opacity-60 dark:border-slate-600 dark:bg-slate-800"
            @change="onSubLevelApply"
          >
            <option
              v-for="subLevelOption in availableSubLevelOptions"
              :key="`sub-level-${subLevelOption.value}`"
              :value="subLevelOption.value"
            >
              {{ subLevelOption.label }}
            </option>
          </select>
          <input
            v-else
            v-model="selectedSubLevel"
            :disabled="!isBySubLevelMode"
            type="text"
            class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm disabled:cursor-not-allowed disabled:opacity-60 dark:border-slate-600 dark:bg-slate-800"
            placeholder="Ketik nama atau kode wilayah turunan"
            @keyup.enter="onSubLevelApply"
            @blur="onSubLevelApply"
          >
          <p class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">
            Pilih nama desa agar fokus wilayah lebih jelas.
          </p>
        </div>

        <div class="flex items-end">
          <button
            type="button"
            class="w-full rounded-md bg-emerald-600 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-700"
            @click="applyFilters"
          >
            Tampilkan Data
          </button>
        </div>
      </div>
      <p class="mt-3 text-xs text-slate-500 dark:text-slate-300">
        Pilihan ini akan diingat, jadi tampilan yang sama bisa dibuka lagi kapan saja.
      </p>
    </CardBox>

    <template v-if="hasDynamicBlocks">
      <div class="space-y-8">
        <div v-for="section in visibleDashboardSections" :key="section.key" class="space-y-4">
          <CardBox v-if="hasSekretarisSections">
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
              <div>
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-100">{{ section.label }}</h3>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-300">
                  {{ resolveSectionDescription(section.key) }}
                </p>
              </div>
              <div v-if="section.filter" class="lg:ml-auto lg:w-64">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                  Pilih Pokja
                </label>

                <select
                  v-if="section.filter.queryKey === 'section2_group' || section.filter.queryKey === 'section3_group'"
                  :value="resolveSectionGroupFilterValue(section.filter.queryKey)"
                  class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-800"
                  @change="onSectionGroupFilterChange(section.filter.queryKey, $event.target.value)"
                >
                  <option
                    v-for="option in section.filter.options"
                    :key="`${section.key}-${option.value}`"
                    :value="option.value"
                  >
                    {{ option.label }}
                  </option>
                </select>
              </div>
            </div>
          </CardBox>

          <CardBox v-for="block in section.blocks" :key="block.key">
            <div class="flex flex-wrap items-start justify-between gap-3">
              <div class="min-w-0">
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-100">{{ block.title }}</h3>
                <p class="mt-2 text-xs text-slate-600 dark:text-slate-300">
                  {{ blockSummaryLabel(block) }}
                </p>
                <p class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">
                  {{ resolveBlockAccessModeLabel(block) }}
                </p>
              </div>
              <div class="ml-auto flex items-center gap-2">
                <button
                  type="button"
                  class="rounded-md border border-slate-300 px-2 py-1 text-[11px] font-semibold text-slate-600 hover:bg-slate-100 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-700"
                  @click="toggleBlockExpanded(block.key)"
                >
                  {{ isBlockExpanded(block.key) ? 'Sembunyikan Grafik' : 'Tampilkan Grafik' }}
                </button>
              </div>
            </div>

            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
              <CardBoxWidget
                v-for="statItem in buildBlockStats(block)"
                :key="`${block.key}-${statItem.key}`"
                :icon="statItem.icon"
                :number="statItem.number"
                :label="statItem.label"
                :color="statItem.color"
              />
            </div>

            <div class="mt-4 overflow-x-auto rounded-md border border-slate-200 dark:border-slate-700">
              <table class="min-w-full text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60">
                  <tr>
                    <th class="px-3 py-2 text-left font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">Informasi</th>
                    <th class="px-3 py-2 text-left font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">Nilai</th>
                    <th class="px-3 py-2 text-left font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">Keterangan</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="row in buildInformativeTableRows(block)"
                    :key="`${block.key}-${row.info}`"
                    class="border-t border-slate-200 dark:border-slate-700"
                  >
                    <td class="px-3 py-2 font-medium text-slate-700 dark:text-slate-200">{{ row.info }}</td>
                    <td class="px-3 py-2 text-slate-900 dark:text-slate-100">{{ row.value }}</td>
                    <td class="px-3 py-2 text-slate-600 dark:text-slate-300">{{ row.note }}</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <template v-if="isBlockExpanded(block.key) && block.kind === 'documents'">
              <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-2">
                <div>
                  <h4 class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                    {{ resolveCoverageChartHeading(block) }}
                  </h4>
                  <div class="h-80">
                    <BarChart :data="buildDocumentCoverageChartData(block)" :empty-text="CHART_EMPTY_STATE_TEXT" horizontal />
                  </div>
                  <p v-if="!hasDocumentCoverageData(block)" class="mt-3 text-xs text-amber-700 dark:text-amber-300">
                    Belum ada data untuk filter yang dipilih.
                  </p>
                </div>

                <div>
                  <h4 class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                    {{ resolveCoverageListHeading(block) }}
                  </h4>
                  <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                    <div
                      v-for="item in resolveDocumentCoverageItems(block)"
                      :key="`${block.key}-${item.label}`"
                      class="rounded-md border border-slate-200 px-3 py-2 text-xs dark:border-slate-700"
                    >
                      <div class="flex items-center justify-between gap-2">
                        <span class="font-medium text-slate-600 dark:text-slate-300">{{ item.label }}</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-100">{{ item.total }}</span>
                      </div>
                      <p v-if="resolveCoverageItemDetail(item)" class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">
                        {{ resolveCoverageItemDetail(item) }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </template>

            <template v-else-if="isBlockExpanded(block.key)">
              <template v-if="shouldShowActivityByDesaChart(block)">
                <div class="mt-6">
                  <div class="mb-2 grid grid-cols-1 gap-2 lg:grid-cols-2 lg:items-end">
                    <h4 class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                      Kegiatan per Desa
                    </h4>
                    <div v-if="blockSupportsMonthFilter(block)" class="lg:ml-auto lg:w-56">
                      <label class="mb-1 block text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                        Bulan
                      </label>
                      <select
                        v-model="selectedSection1Month"
                        class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-800"
                        @change="onSection1MonthChange"
                      >
                        <option
                          v-for="monthOption in SECTION1_MONTH_OPTIONS"
                          :key="`section1-month-${monthOption.value}`"
                          :value="monthOption.value"
                        >
                          {{ monthOption.label }}
                        </option>
                      </select>
                    </div>
                  </div>
                  <p class="mb-2 text-[11px] text-slate-500 dark:text-slate-400">
                    {{ activityByDesaChartModeLabel() }}
                  </p>
                  <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
                    <div>
                      <h5 class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                        Jumlah Kegiatan per Desa
                      </h5>
                      <div class="h-72">
                        <apexchart
                          type="pie"
                          width="100%"
                          height="100%"
                          :options="buildActivityByDesaKegiatanOptions(block)"
                          :series="buildActivityByDesaKegiatanSeries(block)"
                        />
                      </div>
                      <p
                        v-if="!hasActivityByDesaActivityData(block)"
                        class="mt-3 text-xs text-amber-700 dark:text-amber-300"
                      >
                        Belum ada data untuk filter yang dipilih.
                      </p>
                    </div>

                    <div>
                      <h5 class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                        Jumlah Buku vs Buku Terisi
                      </h5>
                      <div class="h-72">
                        <apexchart
                          type="bar"
                          width="100%"
                          height="100%"
                          :options="buildActivityByDesaBookCoverageOptions(block)"
                          :series="buildActivityByDesaBookCoverageSeries(block)"
                        />
                      </div>
                      <p
                        v-if="!hasActivityByDesaBookCoverageData(block)"
                        class="mt-3 text-xs text-amber-700 dark:text-amber-300"
                      >
                        Belum ada data untuk filter yang dipilih.
                      </p>
                    </div>
                  </div>
                </div>
              </template>

              <div v-else class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-2">
                <div>
                  <h4 class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                    Kegiatan Bulanan
                  </h4>
                  <div class="h-72">
                    <apexchart
                      type="bar"
                      width="100%"
                      height="100%"
                      :options="buildActivityMonthlyMultiAxisOptions(block)"
                      :series="buildActivityMonthlyMultiAxisSeries(block)"
                    />
                  </div>
                  <p v-if="!hasActivityMonthlyData(block)" class="mt-3 text-xs text-amber-700 dark:text-amber-300">
                    Belum ada data untuk filter yang dipilih.
                  </p>
                </div>

                <div>
                  <h4 class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                    Distribusi Level
                  </h4>
                  <div class="h-72">
                    <BarChart :data="buildActivityLevelChartData(block)" :empty-text="CHART_EMPTY_STATE_TEXT" />
                  </div>
                </div>
              </div>
            </template>
          </CardBox>

          <CardBox
            v-if="hasSekretarisSections && section.blocks.length === 0"
            class="border border-dashed border-slate-300 dark:border-slate-600"
          >
            <p class="text-xs text-slate-500 dark:text-slate-300">
              Belum ada data untuk pokja yang dipilih.
            </p>
          </CardBox>
        </div>
      </div>
    </template>

    <template v-else-if="showLegacyFallback">
      <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">
        <CardBoxWidget :icon="mdiClipboardList" :number="activityStats.total" label="Total Kegiatan" color="text-blue-500" />
        <CardBoxWidget :icon="mdiChartTimelineVariant" :number="activityStats.this_month" label="Kegiatan Bulan Ini" color="text-indigo-500" />
      </div>

      <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">
        <CardBoxWidget :icon="mdiBookOpenVariant" :number="documentStats.total_buku_tracked" label="Total Buku" color="text-cyan-500" />
        <CardBoxWidget :icon="mdiFileDocumentCheck" :number="documentStats.buku_terisi" label="Buku Terisi" color="text-emerald-500" />
        <CardBoxWidget :icon="mdiFileDocumentMinus" :number="documentStats.buku_belum_terisi" label="Buku Kosong" color="text-rose-500" />
        <CardBoxWidget :icon="mdiChartBar" :number="documentStats.total_entri_buku" label="Total Entri Buku" color="text-violet-500" />
      </div>

      <div class="mb-6 grid grid-cols-1 gap-6 xl:grid-cols-2">
        <CardBox>
          <h3 class="mb-3 text-sm font-semibold text-slate-700 dark:text-slate-100">Cakupan per Buku</h3>
          <div class="h-96">
            <BarChart :data="legacyCoveragePerBukuChartData" :empty-text="CHART_EMPTY_STATE_TEXT" horizontal />
          </div>
        </CardBox>
        <CardBox>
          <h3 class="mb-3 text-sm font-semibold text-slate-700 dark:text-slate-100">Cakupan per Lampiran</h3>
          <div class="h-96">
            <BarChart :data="legacyCoveragePerLampiranChartData" :empty-text="CHART_EMPTY_STATE_TEXT" />
          </div>
          <p v-if="!hasLegacyLampiranCoverageData" class="mt-4 text-xs text-amber-700 dark:text-amber-300">
            Belum ada data terhitung pada cakupan per lampiran untuk wilayah ini.
          </p>
          <div class="mt-4 grid grid-cols-1 gap-2 sm:grid-cols-2">
            <div
              v-for="item in legacyLampiranCoverageItems"
              :key="`lampiran-${item.label}`"
              class="flex items-center justify-between rounded-md border border-slate-200 px-3 py-2 text-xs dark:border-slate-700"
            >
              <span class="font-medium text-slate-600 dark:text-slate-300">{{ item.label }}</span>
              <span class="font-semibold text-slate-800 dark:text-slate-100">{{ item.total }}</span>
            </div>
          </div>
        </CardBox>
      </div>

      <div class="mb-6">
        <CardBox>
          <h3 class="mb-3 text-sm font-semibold text-slate-700 dark:text-slate-100">Distribusi Level Data Dokumen</h3>
          <div class="h-72">
            <BarChart :data="legacyLevelDistributionChartData" :empty-text="CHART_EMPTY_STATE_TEXT" />
          </div>
          <p v-if="!hasLegacyLevelDistributionData" class="mt-4 text-xs text-amber-700 dark:text-amber-300">
            Belum ada data terhitung pada distribusi level dokumen untuk wilayah ini.
          </p>
          <div class="mt-4 grid grid-cols-1 gap-2 sm:grid-cols-2">
            <div
              v-for="item in legacyLevelDistributionItems"
              :key="`level-${item.label}`"
              class="flex items-center justify-between rounded-md border border-slate-200 px-3 py-2 text-xs dark:border-slate-700"
            >
              <span class="font-medium text-slate-600 dark:text-slate-300">{{ item.label }}</span>
              <span class="font-semibold text-slate-800 dark:text-slate-100">{{ item.total }}</span>
            </div>
          </div>
        </CardBox>
      </div>
    </template>

    <CardBox v-else class="border border-dashed border-slate-300 dark:border-slate-600">
      <p class="text-sm text-slate-600 dark:text-slate-300">
        Belum ada blok dashboard yang bisa ditampilkan untuk akses akun ini.
      </p>
    </CardBox>

  </SectionMain>
</template>
