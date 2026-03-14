<script setup>
import { computed, defineAsyncComponent, ref, watch } from 'vue'
import { Deferred, router, usePage, useRemember } from '@inertiajs/vue3'
import CardBox from '@/admin-one/components/CardBox.vue'
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
  dashboardContext: {
    type: Object,
    default: () => ({
      tahun_anggaran: '0',
    }),
  },
})

const BarChart = defineAsyncComponent(() => import('@/admin-one/components/Charts/BarChart.vue'))
const ApexChart = defineAsyncComponent(() => import('@/admin-one/components/Charts/ApexChart.vue'))

const page = usePage()
const authUser = computed(() => page.props?.auth?.user ?? null)
const activeBudgetYearLabel = computed(() => {
  const contextYear = props.dashboardContext?.tahun_anggaran
  if (typeof contextYear === 'string' && contextYear.trim() !== '' && contextYear !== '0') {
    return contextYear
  }

  const userYear = authUser.value?.active_budget_year
  return userYear ? String(userYear) : '-'
})
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
const isDesaScopeUser = computed(() => authUser.value?.scope === 'desa')
const isKecamatanPokjaUser = computed(() =>
  authUser.value?.scope === 'kecamatan'
  && authRoles.value.some((role) => role.startsWith('kecamatan-pokja-')),
)
const isKecamatanSekretarisUser = computed(() =>
  authRoles.value.includes('kecamatan-sekretaris'),
)
const isSekretarisUser = computed(() =>
  authRoles.value.includes('desa-sekretaris')
  || authRoles.value.includes('kecamatan-sekretaris')
)
const useMonthOnlyFilters = computed(() =>
  isDesaScopeUser.value || isKecamatanPokjaUser.value,
)
const useMonthAndLevelFilters = computed(() =>
  isKecamatanSekretarisUser.value,
)
const showModeFilter = computed(() =>
  !useMonthOnlyFilters.value && !useMonthAndLevelFilters.value,
)
const showLevelFilter = computed(() =>
  useMonthAndLevelFilters.value || showModeFilter.value,
)
const showSubLevelFilter = computed(() => showModeFilter.value)
const filterGridClassName = computed(() => {
  if (useMonthOnlyFilters.value) {
    return 'md:grid-cols-2 xl:grid-cols-2'
  }

  if (useMonthAndLevelFilters.value) {
    return 'md:grid-cols-2 xl:grid-cols-3'
  }

  return 'md:grid-cols-2 xl:grid-cols-5'
})
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
const CHART_BOOK_TOTAL_COLOR = '#1d4ed8'
const CHART_BOOK_FILLED_COLOR = '#7e22ce'

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

const chartFilterControlId = (blockKey, field) => {
  const normalizedBlockKey = String(blockKey ?? 'default')
    .toLowerCase()
    .replace(/[^a-z0-9_-]+/g, '-')
    .replace(/^-+|-+$/g, '')

  return `dashboard-${field}-${normalizedBlockKey === '' ? 'default' : normalizedBlockKey}`
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
const DASHBOARD_PARTIAL_PROPS = Object.freeze([
  'dashboardBlocks',
  'dashboardContext',
])
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

const hasResolvedDashboardBlocks = computed(() => page.props?.dashboardBlocks !== undefined)
const hasDynamicBlocks = computed(() => dynamicBlocks.value.length > 0)
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

const dashboardUiRememberKey = `dashboard:ui-state:${String(page.props?.auth?.user?.id ?? 'guest')}`
const expandedBlockKeys = useRemember({}, dashboardUiRememberKey)
const blockDetailWidgets = ref({})

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

  const nextExpanded = !isBlockExpanded(key)

  expandedBlockKeys.value = {
    ...expandedBlockKeys.value,
    [key]: nextExpanded,
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
  && (
    ['labels', 'values', 'books_total', 'books_filled'].some((metricKey) => {
      const values = block?.charts?.by_desa?.[metricKey]
      return Array.isArray(values) && values.length > 0
    })
    || ['labels', 'values'].some((metricKey) => {
      const values = block?.charts?.monthly?.[metricKey]
      return Array.isArray(values) && values.length > 0
    })
  )
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
const resolveSekretarisLevelFilter = () => {
  if (isKecamatanSekretarisUser.value) {
    return resolveOptionValue(selectedLevel.value, LEVEL_OPTIONS, 'all')
  }

  return sekretarisDefaultLevel.value
}

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

const buildGlobalFilterQuery = () => {
  if (useMonthOnlyFilters.value) {
    return {
      section1_month: normalizedSection1Month.value,
    }
  }

  if (useMonthAndLevelFilters.value) {
    return {
      mode: 'by-level',
      level: resolveSekretarisLevelFilter(),
      sub_level: 'all',
      section1_month: normalizedSection1Month.value,
    }
  }

  return {
    mode: selectedMode.value,
    level: isByLevelMode.value ? selectedLevel.value : 'all',
    sub_level: isBySubLevelMode.value ? selectedSubLevel.value : 'all',
    section1_month: normalizedSection1Month.value,
  }
}

const buildSekretarisFilterQuery = () => ({
  mode: 'by-level',
  level: resolveSekretarisLevelFilter(),
  sub_level: 'all',
  section1_month: normalizedSection1Month.value,
  section2_group: selectedSection2Group.value,
  section3_group: hasSekretarisLowerSection.value ? selectedSection3Group.value : 'all',
})

const visitDashboard = (query) => {
  router.get('/dashboard', query, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
    only: DASHBOARD_PARTIAL_PROPS,
  })
}

const applyFilters = () => {
  visitDashboard(buildGlobalFilterQuery())
}

const applyChartFilters = () => {
  selectedSection1Month.value = resolveOptionValue(selectedSection1Month.value, SECTION1_MONTH_OPTIONS, 'all')
  selectedSubLevel.value = normalizeToken(selectedSubLevel.value, 'all')
  if (!hasMonthFilterAwareBlocks.value) {
    selectedSection1Month.value = 'all'
  }

  if (hasSekretarisSections.value) {
    applySekretarisSectionFilters()
    return
  }

  applyFilters()
}

const applySekretarisSectionFilters = () => {
  visitDashboard(buildSekretarisFilterQuery())
}

const onChartFilterModeChange = () => {
  if (!isByLevelMode.value) {
    selectedLevel.value = 'all'
  }

  if (!isBySubLevelMode.value) {
    selectedSubLevel.value = 'all'
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
    const expectedQuery = buildSekretarisFilterQuery()

    const isSynced = normalizeToken(params.get('mode'), 'all') === expectedQuery.mode
      && normalizeToken(params.get('level'), 'all') === expectedQuery.level
      && normalizeToken(params.get('sub_level'), 'all') === expectedQuery.sub_level
      && normalizeToken(params.get('section1_month'), 'all') === expectedQuery.section1_month
      && normalizeToken(params.get('section2_group'), 'all') === expectedQuery.section2_group
      && normalizeToken(params.get('section3_group'), 'all') === expectedQuery.section3_group

    if (isSynced) {
      return
    }

    visitDashboard(expectedQuery)
  },
  { immediate: true },
)

watch(
  hasMonthFilterAwareBlocks,
  (isMonthFilterRelevant) => {
    if (!hasResolvedDashboardBlocks.value) {
      return
    }

    if (isMonthFilterRelevant || selectedSection1Month.value === 'all') {
      return
    }

    selectedSection1Month.value = 'all'
  },
  { immediate: true },
)

watch(
  [hasSekretarisSections, hasMonthFilterAwareBlocks, hasResolvedDashboardBlocks, () => page.url],
  ([isSekretarisView, isMonthFilterRelevant, isBlocksResolved, url]) => {
    if (!isBlocksResolved || isSekretarisView || isMonthFilterRelevant) {
      return
    }

    const params = parseQuery(url)
    if (normalizeToken(params.get('section1_month'), 'all') === 'all') {
      return
    }

    visitDashboard(buildGlobalFilterQuery())
  },
  { immediate: true },
)

watch(
  visibleDashboardBlocks,
  (blocks) => {
    blocks.forEach((block) => {
      if (isBlockExpanded(block?.key) && isLazyBlockDetailWidgetEnabled(block)) {
        void ensureBlockDetailWidget(block)
      }
    })
  },
  { immediate: true },
)

watch(
  expandedBlockKeys,
  () => {
    visibleDashboardBlocks.value.forEach((block) => {
      if (isBlockExpanded(block?.key) && isLazyBlockDetailWidgetEnabled(block)) {
        void ensureBlockDetailWidget(block)
      }
    })
  },
  { deep: true },
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

const isLazyBlockDetailWidgetEnabled = (block) =>
  normalizeToken(block?.detail?.strategy, '') === 'json'
  && typeof block?.detail?.endpoint === 'string'
  && block.detail.endpoint.trim() !== ''

const resolveBlockDetailWidgetState = (blockKey) => {
  const key = String(blockKey ?? '')
  const state = blockDetailWidgets.value[key]

  if (!state || typeof state !== 'object') {
    return {
      status: 'idle',
      items: [],
      trackedModules: [],
      error: '',
    }
  }

  return {
    status: typeof state.status === 'string' ? state.status : 'idle',
    items: Array.isArray(state.items) ? state.items : [],
    trackedModules: Array.isArray(state.trackedModules) ? state.trackedModules : [],
    error: typeof state.error === 'string' ? state.error : '',
  }
}

const isLazyBlockDetailWidgetLoading = (block) =>
  resolveBlockDetailWidgetState(block?.key).status === 'loading'

const isLazyBlockDetailWidgetReady = (block) =>
  resolveBlockDetailWidgetState(block?.key).status === 'loaded'

const lazyBlockDetailRows = (block) =>
  resolveBlockDetailWidgetState(block?.key).items

const lazyBlockDetailError = (block) =>
  resolveBlockDetailWidgetState(block?.key).error

const formatPerModuleBreakdown = (item) => {
  const modules = item?.per_module
  if (!modules || typeof modules !== 'object') {
    return '-'
  }

  const entries = Object.entries(modules)
    .filter(([, total]) => Number(total ?? 0) > 0)
    .map(([moduleSlug, total]) => `${humanizeLabel(moduleSlug)}: ${Number(total).toLocaleString('id-ID')}`)

  return entries.length > 0 ? entries.join(', ') : '-'
}

const ensureBlockDetailWidget = async (block) => {
  if (!isLazyBlockDetailWidgetEnabled(block)) {
    return
  }

  const key = String(block?.key ?? '')
  if (key === '') {
    return
  }

  const currentState = resolveBlockDetailWidgetState(key)
  if (currentState.status === 'loading' || currentState.status === 'loaded') {
    return
  }

  blockDetailWidgets.value = {
    ...blockDetailWidgets.value,
    [key]: {
      status: 'loading',
      items: currentState.items,
      trackedModules: currentState.trackedModules,
      error: '',
    },
  }

  try {
    const response = await fetch(block.detail.endpoint, {
      headers: {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
      credentials: 'same-origin',
    })

    if (!response.ok) {
      throw new Error(`HTTP ${response.status}`)
    }

    const payload = await response.json()

    blockDetailWidgets.value = {
      ...blockDetailWidgets.value,
      [key]: {
        status: 'loaded',
        items: Array.isArray(payload?.items) ? payload.items : [],
        trackedModules: Array.isArray(payload?.tracked_modules) ? payload.tracked_modules : [],
        error: '',
      },
    }
  } catch (_error) {
    if (typeof window !== 'undefined' && typeof window.__emitUiRuntimeError === 'function') {
      window.__emitUiRuntimeError(_error, `dashboard.block-detail.fetch:${key}`)
    }

    blockDetailWidgets.value = {
      ...blockDetailWidgets.value,
      [key]: {
        status: 'error',
        items: [],
        trackedModules: [],
        error: 'Rincian per desa belum berhasil dimuat.',
      },
    }
  }
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
const sumNumericValues = (values) =>
  Array.isArray(values)
    ? values.reduce((total, value) => total + toNumber(value), 0)
    : 0
const resolveActivitySummaryMetrics = (block) => {
  const metrics = resolveBookComparisonMetrics(block)
  const summary = {
    totalKegiatan: toNumber(block?.stats?.total),
    bulanIni: toNumber(block?.stats?.this_month),
    totalBooks: metrics.totalBooks,
    filledBooks: metrics.filledBooks,
  }

  if (hasByDesaActivityMetrics(block)) {
    const byDesaMetrics = resolveActivityByDesaMetrics(block)
    summary.totalKegiatan = sumNumericValues(byDesaMetrics.syncedActivityValues)

    const byDesaTotalBooks = sumNumericValues(byDesaMetrics.syncedTotalBookValues)
    const byDesaFilledBooks = sumNumericValues(byDesaMetrics.syncedFilledBookValues)
    if (byDesaTotalBooks > 0 || byDesaFilledBooks > 0) {
      summary.totalBooks = byDesaTotalBooks
      summary.filledBooks = byDesaFilledBooks
    }
  } else {
    const monthlyValues = block?.charts?.monthly?.values
    const monthlyTotal = sumNumericValues(monthlyValues)
    if (monthlyTotal > 0 && blockSupportsMonthFilter(block)) {
      summary.totalKegiatan = monthlyTotal
    }
  }

  if (normalizedSection1Month.value !== 'all' && blockSupportsMonthFilter(block)) {
    summary.bulanIni = summary.totalKegiatan
  }

  return summary
}
const buildDashboardSummaryTiles = (block) => {
  const totalBooks = toNumber(block?.kind === 'documents' ? block?.stats?.total_buku_tracked : resolveActivitySummaryMetrics(block).totalBooks)
  const filledBooks = toNumber(block?.kind === 'documents' ? block?.stats?.buku_terisi : resolveActivitySummaryMetrics(block).filledBooks)
  const unfilledBooks = Math.max(totalBooks - filledBooks, 0)

  if (block?.kind === 'activity') {
    const activitySummary = resolveActivitySummaryMetrics(block)

    return [
      { key: 'total-kegiatan', label: 'Total Kegiatan', value: formatSummaryValue(activitySummary.totalKegiatan), tone: 'default' },
      { key: 'kegiatan-bulan-ini', label: 'Bulan Ini', value: formatSummaryValue(activitySummary.bulanIni), tone: 'default' },
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

  return values
}

const buildActivityMonthlyMultiAxisOptions = (block) => {
  const labels = block?.charts?.monthly?.labels ?? []

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
    colors: [CHART_BOOK_TOTAL_COLOR, CHART_BOOK_FILLED_COLOR],
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
    [CHART_BOOK_TOTAL_COLOR, CHART_BOOK_FILLED_COLOR],
  )
}
const hasBookComparisonData = (block) => {
  const { totalBooks, filledBooks } = resolveBookComparisonMetrics(block)

  return totalBooks > 0 || filledBooks > 0
}
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
    <p class="mt-[-8px] mb-4 text-xs text-slate-500 dark:text-slate-400">
      Tahun anggaran aktif: {{ activeBudgetYearLabel }}
    </p>

    <Deferred data="dashboardBlocks">
      <template #fallback>
        <CardBox>
          <div class="space-y-3">
            <div class="h-4 w-40 animate-pulse rounded bg-slate-200 dark:bg-slate-700" />
            <div class="h-3 w-64 animate-pulse rounded bg-slate-100 dark:bg-slate-800" />
            <div class="grid gap-3 md:grid-cols-3 xl:grid-cols-5">
              <div
                v-for="index in 5"
                :key="`dashboard-block-skeleton-${index}`"
                class="h-20 animate-pulse rounded border border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-800/60"
              />
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400">
              Memuat blok dashboard lanjutan.
            </p>
          </div>
        </CardBox>
      </template>

      <template #default>
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

                <div
                  class="mt-4 grid grid-cols-1 gap-3 rounded border border-slate-200 p-3 dark:border-slate-700"
                  :class="filterGridClassName"
                >
                  <div v-if="showModeFilter">
                    <label
                      :for="chartFilterControlId(block.key, 'mode')"
                      class="mb-1 block text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300"
                    >
                      Cara Tampil
                    </label>
                    <select
                      :id="chartFilterControlId(block.key, 'mode')"
                      v-model="selectedMode"
                      aria-label="Cara tampil chart"
                      class="min-h-[44px] w-full rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-800"
                      @change="onChartFilterModeChange"
                    >
                      <option v-for="modeOption in MODE_OPTIONS" :key="`block-${block.key}-${modeOption.value}`" :value="modeOption.value">
                        {{ modeOption.label }}
                      </option>
                    </select>
                  </div>

                  <div v-if="showLevelFilter">
                    <label
                      :for="chartFilterControlId(block.key, 'level')"
                      class="mb-1 block text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300"
                    >
                      Tingkat
                    </label>
                    <select
                      :id="chartFilterControlId(block.key, 'level')"
                      v-model="selectedLevel"
                      aria-label="Tingkat wilayah chart"
                      :disabled="showModeFilter && !isByLevelMode"
                      class="min-h-[44px] w-full rounded-md border border-slate-300 px-3 py-2 text-sm disabled:cursor-not-allowed disabled:opacity-60 dark:border-slate-600 dark:bg-slate-800"
                    >
                      <option v-for="levelOption in LEVEL_OPTIONS" :key="`block-level-${block.key}-${levelOption.value}`" :value="levelOption.value">
                        {{ levelOption.label }}
                      </option>
                    </select>
                  </div>

                  <div v-if="showSubLevelFilter">
                    <label
                      :for="chartFilterControlId(block.key, 'sub-level')"
                      class="mb-1 block text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300"
                    >
                      Wilayah Turunan
                    </label>
                    <select
                      :id="chartFilterControlId(block.key, 'sub-level')"
                      v-if="availableSubLevelOptions.length > 1"
                      v-model="selectedSubLevel"
                      aria-label="Wilayah turunan chart"
                      :disabled="!isBySubLevelMode"
                      class="min-h-[44px] w-full rounded-md border border-slate-300 px-3 py-2 text-sm disabled:cursor-not-allowed disabled:opacity-60 dark:border-slate-600 dark:bg-slate-800"
                    >
                      <option
                        v-for="subLevelOption in availableSubLevelOptions"
                        :key="`block-sub-level-${block.key}-${subLevelOption.value}`"
                        :value="subLevelOption.value"
                      >
                        {{ subLevelOption.label }}
                      </option>
                    </select>
                    <input
                      :id="chartFilterControlId(block.key, 'sub-level')"
                      v-else
                      v-model="selectedSubLevel"
                      aria-label="Wilayah turunan chart"
                      :disabled="!isBySubLevelMode"
                      type="text"
                      class="min-h-[44px] w-full rounded-md border border-slate-300 px-3 py-2 text-sm disabled:cursor-not-allowed disabled:opacity-60 dark:border-slate-600 dark:bg-slate-800"
                      placeholder="Ketik nama wilayah"
                    >
                  </div>

                  <div>
                    <label
                      :for="chartFilterControlId(block.key, 'month')"
                      class="mb-1 block text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300"
                    >
                      Bulan
                    </label>
                    <select
                      :id="chartFilterControlId(block.key, 'month')"
                      v-model="selectedSection1Month"
                      aria-label="Bulan chart"
                      :disabled="!blockSupportsMonthFilter(block)"
                      class="min-h-[44px] w-full rounded-md border border-slate-300 px-3 py-2 text-sm disabled:cursor-not-allowed disabled:opacity-60 dark:border-slate-600 dark:bg-slate-800"
                    >
                      <option
                        v-for="monthOption in SECTION1_MONTH_OPTIONS"
                        :key="`block-month-${block.key}-${monthOption.value}`"
                        :value="monthOption.value"
                      >
                        {{ monthOption.label }}
                      </option>
                    </select>
                  </div>

                  <div class="flex items-end">
                    <button
                      type="button"
                      class="min-h-[44px] w-full rounded-md bg-emerald-600 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-700"
                      @click="applyChartFilters"
                    >
                      Terapkan Filter Chart
                    </button>
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

                  <div v-if="isLazyBlockDetailWidgetEnabled(block)" class="mt-6">
                    <h4 class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                      Rincian Per Modul per Desa
                    </h4>
                    <p class="mb-3 text-[11px] text-slate-500 dark:text-slate-400">
                      Rincian ini dimuat saat blok dibuka agar payload awal dashboard tetap tipis.
                    </p>

                    <p v-if="isLazyBlockDetailWidgetLoading(block)" class="text-xs text-slate-500 dark:text-slate-300">
                      Memuat rincian per modul per desa.
                    </p>
                    <p v-else-if="lazyBlockDetailError(block) !== ''" class="text-xs text-amber-700 dark:text-amber-300">
                      {{ lazyBlockDetailError(block) }}
                    </p>
                    <div v-else-if="isLazyBlockDetailWidgetReady(block) && lazyBlockDetailRows(block).length > 0" class="overflow-x-auto">
                      <table class="w-full min-w-[680px] text-sm">
                        <thead class="border-b border-slate-200 dark:border-slate-700">
                          <tr class="text-left text-slate-600 dark:text-slate-300">
                            <th class="px-3 py-3 font-semibold">Desa</th>
                            <th class="px-3 py-3 font-semibold">Total</th>
                            <th class="px-3 py-3 font-semibold">Rincian Modul</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr
                            v-for="item in lazyBlockDetailRows(block)"
                            :key="`${block.key}-${item.slug}`"
                            class="border-b border-slate-100 align-top dark:border-slate-800"
                          >
                            <td class="px-3 py-3 text-slate-800 dark:text-slate-100">{{ item.label }}</td>
                            <td class="px-3 py-3 text-slate-700 dark:text-slate-300">{{ toNumber(item.total).toLocaleString('id-ID') }}</td>
                            <td class="px-3 py-3 text-slate-700 dark:text-slate-300">{{ formatPerModuleBreakdown(item) }}</td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                    <p v-else class="text-xs text-slate-500 dark:text-slate-300">
                      Belum ada rincian modul per desa untuk filter yang dipilih.
                    </p>
                  </div>
                </template>

                <template v-else-if="isBlockExpanded(block.key)">
                  <template v-if="shouldShowActivityByDesaChart(block)">
                    <div class="mt-6">
                      <div class="mb-2">
                        <h4 class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                          Kegiatan per Desa
                        </h4>
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
                            <ApexChart
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
                            <ApexChart
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
                        <ApexChart
                          type="pie"
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

        <p v-else class="text-sm text-slate-600 dark:text-slate-300">
          Belum ada blok dashboard yang bisa ditampilkan untuk akses akun ini.
        </p>
      </template>
    </Deferred>

  </SectionMain>
</template>
