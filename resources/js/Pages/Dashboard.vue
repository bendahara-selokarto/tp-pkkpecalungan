<script setup>
import { computed, ref, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import CardBox from '@/admin-one/components/CardBox.vue'
import BarChart from '@/admin-one/components/Charts/BarChart.vue'
import BaseButton from '@/admin-one/components/BaseButton.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import {
  mdiChartTimelineVariant,
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

const visibleDashboardSections = computed(() => {
  const sections = dashboardSections.value

  if (!Array.isArray(sections) || sections.length === 0) {
    return []
  }

  // Product decision: keep dashboard in single-section mode to reduce cognitive load.
  const firstNonEmptySection = sections.find((section) =>
    Array.isArray(section?.blocks) && section.blocks.length > 0)

  return [firstNonEmptySection ?? sections[0]]
})
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
const formatSummaryValue = (value) => toNumber(value).toLocaleString('id-ID')
const summaryToneClasses = (tone) => {
  if (tone === 'info') {
    return {
      box: 'border border-cyan-200 dark:border-cyan-900/50',
      label: 'text-[11px] uppercase tracking-wide text-cyan-700 dark:text-cyan-300',
      value: 'mt-1 text-lg font-semibold text-cyan-700 dark:text-cyan-300',
    }
  }

  if (tone === 'success') {
    return {
      box: 'border border-emerald-200 dark:border-emerald-900/50',
      label: 'text-[11px] uppercase tracking-wide text-emerald-700 dark:text-emerald-300',
      value: 'mt-1 text-lg font-semibold text-emerald-700 dark:text-emerald-300',
    }
  }

  if (tone === 'warning') {
    return {
      box: 'border border-amber-200 dark:border-amber-900/50',
      label: 'text-[11px] uppercase tracking-wide text-amber-700 dark:text-amber-300',
      value: 'mt-1 text-lg font-semibold text-amber-700 dark:text-amber-300',
    }
  }

  if (tone === 'muted') {
    return {
      box: 'border border-slate-300 dark:border-slate-700',
      label: 'text-[11px] uppercase tracking-wide text-slate-600 dark:text-slate-300',
      value: 'mt-1 text-lg font-semibold text-slate-700 dark:text-slate-200',
    }
  }

  return {
    box: 'border border-slate-200 dark:border-slate-700',
    label: 'text-[11px] uppercase tracking-wide text-slate-500 dark:text-slate-400',
    value: 'mt-1 text-lg font-semibold text-slate-800 dark:text-slate-100',
  }
}
const countActiveCoverageItems = (block) => {
  const values = block?.charts?.coverage_per_module?.values
  if (!Array.isArray(values)) {
    return 0
  }

  return values.filter((value) => toNumber(value) > 0).length
}
const buildDashboardSummaryTiles = (block) => {
  const totalBooks = toNumber(block?.kind === 'documents' ? block?.stats?.total_buku_tracked : block?.stats?.books_total)
  const filledBooks = toNumber(block?.kind === 'documents' ? block?.stats?.buku_terisi : block?.stats?.books_filled)
  const unfilledBooks = Math.max(totalBooks - filledBooks, 0)

  if (block?.kind === 'activity') {
    return [
      { key: 'total-kegiatan', label: 'Total Kegiatan', value: formatSummaryValue(block?.stats?.total), tone: 'default' },
      { key: 'kegiatan-bulan-ini', label: 'Bulan Ini', value: formatSummaryValue(block?.stats?.this_month), tone: 'default' },
      { key: 'jumlah-buku', label: 'Jumlah Buku', value: formatSummaryValue(totalBooks), tone: 'info' },
      { key: 'buku-terisi', label: 'Buku Terisi', value: formatSummaryValue(filledBooks), tone: 'success' },
      { key: 'buku-belum-terisi', label: 'Buku Belum Terisi', value: formatSummaryValue(unfilledBooks), tone: 'warning' },
    ]
  }

  return [
    { key: 'total-buku', label: 'Total Buku', value: formatSummaryValue(totalBooks), tone: 'default' },
    { key: 'total-entri', label: 'Total Entri', value: formatSummaryValue(block?.stats?.total_entri_buku), tone: 'default' },
    { key: 'modul-aktif', label: 'Modul Aktif', value: formatSummaryValue(countActiveCoverageItems(block)), tone: 'info' },
    { key: 'buku-terisi', label: 'Buku Terisi', value: formatSummaryValue(filledBooks), tone: 'success' },
    { key: 'buku-belum-terisi', label: 'Buku Belum Terisi', value: formatSummaryValue(unfilledBooks), tone: 'muted' },
  ]
}

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

const resolveBookComparisonMetrics = (block) => {
  if (block?.kind === 'documents') {
    return {
      totalBooks: toNumber(block?.stats?.total_buku_tracked),
      filledBooks: toNumber(block?.stats?.buku_terisi),
    }
  }

  const chartValues = Array.isArray(block?.charts?.book_comparison?.values)
    ? block.charts.book_comparison.values
    : []
  if (chartValues.length >= 2) {
    return {
      totalBooks: toNumber(chartValues[0] ?? 0),
      filledBooks: toNumber(chartValues[1] ?? 0),
    }
  }

  return {
    totalBooks: toNumber(block?.stats?.books_total),
    filledBooks: toNumber(block?.stats?.books_filled),
  }
}
const buildBookComparisonChartData = (block) => {
  const { totalBooks, filledBooks } = resolveBookComparisonMetrics(block)

  return buildSingleDataset(
    ['Jumlah Buku', 'Buku Terisi'],
    [totalBooks, filledBooks],
    ['#7e22ce', '#16a34a'],
  )
}
const hasBookComparisonData = (block) => {
  const { totalBooks, filledBooks } = resolveBookComparisonMetrics(block)

  return totalBooks > 0 || filledBooks > 0
}

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

const legacyBookComparisonChartData = computed(() => buildSingleDataset(
  ['Jumlah Buku', 'Buku Terisi'],
  [toNumber(documentStats.value.total_buku_tracked), toNumber(documentStats.value.buku_terisi)],
  ['#7e22ce', '#16a34a'],
))

const hasLegacyLampiranCoverageData = computed(() =>
  (documentCharts.value.coverage_per_lampiran?.values ?? []).some((value) => toNumber(value) > 0),
)

const hasLegacyBookComparisonData = computed(() =>
  toNumber(documentStats.value.total_buku_tracked) > 0 || toNumber(documentStats.value.buku_terisi) > 0,
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

    <template v-if="hasDynamicBlocks">
      <div class="space-y-8">
        <div v-for="section in visibleDashboardSections" :key="section.key" class="space-y-4">
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
                  class="inline-flex min-h-[44px] items-center rounded-md border border-slate-300 px-4 py-2 text-[11px] font-semibold text-slate-600 hover:bg-slate-100 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-700"
                  @click="toggleBlockExpanded(block.key)"
                >
                  {{ isBlockExpanded(block.key) ? 'Sembunyikan Grafik' : 'Tampilkan Grafik' }}
                </button>
              </div>
            </div>

            <div class="mt-4 grid gap-3 md:grid-cols-3 xl:grid-cols-5">
              <div
                v-for="tile in buildDashboardSummaryTiles(block)"
                :key="`${block.key}-${tile.key}`"
                class="rounded px-3 py-2"
                :class="summaryToneClasses(tile.tone).box"
              >
                <p :class="summaryToneClasses(tile.tone).label">{{ tile.label }}</p>
                <p :class="summaryToneClasses(tile.tone).value">{{ tile.value }}</p>
              </div>
            </div>

            <template v-if="isBlockExpanded(block.key) && block.kind === 'documents'">
              <div class="mt-6">
                <div>
                  <h4 class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                    Jumlah Buku vs Buku Terisi
                  </h4>
                  <div class="h-80">
                    <BarChart :data="buildBookComparisonChartData(block)" :empty-text="CHART_EMPTY_STATE_TEXT" />
                  </div>
                  <p v-if="!hasBookComparisonData(block)" class="mt-3 text-xs text-amber-700 dark:text-amber-300">
                    Belum ada data untuk filter yang dipilih.
                  </p>
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
                        class="min-h-[44px] w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-800"
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
                    Jumlah Buku vs Buku Terisi
                  </h4>
                  <div class="h-72">
                    <BarChart :data="buildBookComparisonChartData(block)" :empty-text="CHART_EMPTY_STATE_TEXT" />
                  </div>
                  <p v-if="!hasBookComparisonData(block)" class="mt-3 text-xs text-amber-700 dark:text-amber-300">
                    Belum ada data untuk filter yang dipilih.
                  </p>
                </div>
              </div>
            </template>
          </CardBox>

          <p
            v-if="hasSekretarisSections && section.blocks.length === 0"
            class="text-xs text-slate-500 dark:text-slate-300"
          >
            Belum ada data untuk pokja yang dipilih.
          </p>
        </div>
      </div>
    </template>

    <template v-else-if="showLegacyFallback">
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
        </CardBox>
      </div>

      <div class="mb-6">
        <CardBox>
          <h3 class="mb-3 text-sm font-semibold text-slate-700 dark:text-slate-100">Jumlah Buku vs Buku Terisi</h3>
          <div class="h-72">
            <BarChart :data="legacyBookComparisonChartData" :empty-text="CHART_EMPTY_STATE_TEXT" />
          </div>
          <p v-if="!hasLegacyBookComparisonData" class="mt-4 text-xs text-amber-700 dark:text-amber-300">
            Belum ada data buku terhitung untuk wilayah ini.
          </p>
        </CardBox>
      </div>
    </template>

    <p v-else class="text-sm text-slate-600 dark:text-slate-300">
      Belum ada blok dashboard yang bisa ditampilkan untuk akses akun ini.
    </p>

  </SectionMain>
</template>
