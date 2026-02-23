<script setup>
import { computed, ref, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import CardBox from '@/admin-one/components/CardBox.vue'
import CardBoxWidget from '@/admin-one/components/CardBoxWidget.vue'
import BarChart from '@/admin-one/components/Charts/BarChart.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import {
  mdiBookOpenVariant,
  mdiChartBar,
  mdiChartTimelineVariant,
  mdiCheckCircle,
  mdiClipboardList,
  mdiFileDocumentCheck,
  mdiFileDocumentMinus,
  mdiPencilCircle,
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
      published: 0,
      draft: 0,
      activity: {
        total: 0,
        this_month: 0,
        published: 0,
        draft: 0,
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

const MODE_OPTIONS = [
  { value: 'all', label: 'All' },
  { value: 'by-level', label: 'By Level' },
  { value: 'by-sub-level', label: 'By Sub-Level' },
]

const LEVEL_OPTIONS = [
  { value: 'all', label: 'All Level' },
  { value: 'desa', label: 'Desa' },
  { value: 'kecamatan', label: 'Kecamatan' },
]

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

watch(
  () => page.url,
  (url) => {
    const params = parseQuery(url)
    selectedMode.value = resolveOptionValue(params.get('mode'), MODE_OPTIONS, 'all')
    selectedLevel.value = resolveOptionValue(params.get('level'), LEVEL_OPTIONS, 'all')
    selectedSubLevel.value = normalizeToken(params.get('sub_level'), 'all')
  },
)

const isByLevelMode = computed(() => selectedMode.value === 'by-level')
const isBySubLevelMode = computed(() => selectedMode.value === 'by-sub-level')

const applyFilters = () => {
  router.get('/dashboard', {
    mode: selectedMode.value,
    level: isByLevelMode.value ? selectedLevel.value : 'all',
    sub_level: isBySubLevelMode.value ? selectedSubLevel.value : 'all',
  }, {
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

const dynamicBlocks = computed(() =>
  Array.isArray(props.dashboardBlocks)
    ? props.dashboardBlocks.filter((block) => block && typeof block === 'object')
    : [],
)

const hasDynamicBlocks = computed(() => dynamicBlocks.value.length > 0)

const humanizeLabel = (value) => String(value ?? '')
  .replace(/[-_]+/g, ' ')
  .trim()
  .replace(/\b\w/g, (char) => char.toUpperCase())

const toNumber = (value) => Number(value ?? 0)

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

const resolveBlockModeLabel = (mode) => (mode === 'read-only' ? 'RO' : 'RW')

const resolveBlockModeClass = (mode) => (mode === 'read-only'
  ? 'border-amber-300 bg-amber-50 text-amber-700 dark:border-amber-800 dark:bg-amber-950/40 dark:text-amber-300'
  : 'border-emerald-300 bg-emerald-50 text-emerald-700 dark:border-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300')

const sourceModulesLabel = (block) => {
  const modules = block?.sources?.source_modules
  if (!Array.isArray(modules) || modules.length === 0) {
    return '-'
  }

  return modules.map((moduleSlug) => humanizeLabel(moduleSlug)).join(', ')
}

const filterContextLabel = (block) => {
  const context = block?.sources?.filter_context ?? {}

  return `Mode: ${humanizeLabel(context.mode ?? 'all')} | Level: ${humanizeLabel(context.level ?? 'all')} | Sub-Level: ${humanizeLabel(context.sub_level ?? 'all')}`
}

const buildBlockStats = (block) => {
  const groupLabel = String(block?.group_label ?? 'Dashboard').trim()

  if (block?.kind === 'activity') {
    const stats = block?.stats ?? {}

    return [
      {
        key: 'activity-total',
        icon: mdiClipboardList,
        label: `Total Aktivitas ${groupLabel}`,
        number: toNumber(stats.total),
        color: 'text-blue-500',
      },
      {
        key: 'activity-this-month',
        icon: mdiChartTimelineVariant,
        label: `Aktivitas Bulan Ini ${groupLabel}`,
        number: toNumber(stats.this_month),
        color: 'text-indigo-500',
      },
      {
        key: 'activity-published',
        icon: mdiCheckCircle,
        label: `Aktivitas Terpublikasi ${groupLabel}`,
        number: toNumber(stats.published),
        color: 'text-emerald-500',
      },
      {
        key: 'activity-draft',
        icon: mdiPencilCircle,
        label: `Aktivitas Draft ${groupLabel}`,
        number: toNumber(stats.draft),
        color: 'text-amber-500',
      },
    ]
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

const buildActivityMonthlyChartData = (block) => {
  const labels = block?.charts?.monthly?.labels ?? []
  const values = (block?.charts?.monthly?.values ?? []).map((value) => toNumber(value))

  return buildSingleDataset(labels, values, '#0ea5e9')
}

const buildActivityStatusChartData = (block) => {
  const labels = block?.charts?.status?.labels ?? ['Draft', 'Published']
  const values = (block?.charts?.status?.values ?? [0, 0]).map((value) => toNumber(value))

  return buildSingleDataset(labels, values, ['#f59e0b', '#10b981'])
}

const buildActivityLevelChartData = (block) => {
  const labels = block?.charts?.level?.labels ?? ['Desa', 'Kecamatan']
  const values = (block?.charts?.level?.values ?? [0, 0]).map((value) => toNumber(value))

  return buildSingleDataset(labels, values, ['#06b6d4', '#6366f1'])
}

const hasAnyChartData = (chartData) =>
  (chartData?.datasets?.[0]?.data ?? []).some((value) => toNumber(value) > 0)

// Legacy fallback while dynamic blocks are still rolling out.
const activityStats = computed(() => props.dashboardStats.activity ?? {
  total: props.dashboardStats.total ?? 0,
  this_month: props.dashboardStats.this_month ?? 0,
  published: props.dashboardStats.published ?? 0,
  draft: props.dashboardStats.draft ?? 0,
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
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiChartTimelineVariant" title="Dashboard" main />

    <CardBox class="mb-6">
      <div class="grid grid-cols-1 gap-4 lg:grid-cols-4">
        <div>
          <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
            Mode
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
            Level
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
            Sub-Level
          </label>
          <input
            v-model="selectedSubLevel"
            :disabled="!isBySubLevelMode"
            type="text"
            class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm disabled:cursor-not-allowed disabled:opacity-60 dark:border-slate-600 dark:bg-slate-800"
            placeholder="all atau kode sub-level"
            @keyup.enter="onSubLevelApply"
            @blur="onSubLevelApply"
          >
        </div>

        <div class="flex items-end">
          <button
            type="button"
            class="w-full rounded-md bg-emerald-600 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-700"
            @click="applyFilters"
          >
            Terapkan Filter
          </button>
        </div>
      </div>
      <p class="mt-3 text-xs text-slate-500 dark:text-slate-300">
        Filter tersimpan di URL agar tampilan dashboard mudah dibagikan dan direproduksi.
      </p>
    </CardBox>

    <template v-if="hasDynamicBlocks">
      <div class="space-y-6">
        <CardBox v-for="block in dynamicBlocks" :key="block.key">
          <div class="flex flex-wrap items-center justify-between gap-3">
            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-100">{{ block.title }}</h3>
            <span
              class="inline-flex items-center rounded border px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide"
              :class="resolveBlockModeClass(block.mode)"
            >
              {{ resolveBlockModeLabel(block.mode) }}
            </span>
          </div>

          <p class="mt-2 text-xs text-slate-600 dark:text-slate-300">
            Sumber: {{ sourceModulesLabel(block) }}
          </p>
          <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
            Cakupan: {{ block.sources?.source_area_type ?? '-' }} | {{ filterContextLabel(block) }}
          </p>

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

          <template v-if="block.kind === 'documents'">
            <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-2">
              <div>
                <h4 class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                  Cakupan Per Modul
                </h4>
                <div class="h-80">
                  <BarChart :data="buildDocumentCoverageChartData(block)" horizontal />
                </div>
                <p v-if="!hasDocumentCoverageData(block)" class="mt-3 text-xs text-amber-700 dark:text-amber-300">
                  Belum ada data untuk filter dan hak akses aktif.
                </p>
              </div>

              <div>
                <h4 class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                  Nilai Per Modul
                </h4>
                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                  <div
                    v-for="item in resolveDocumentCoverageItems(block)"
                    :key="`${block.key}-${item.label}`"
                    class="flex items-center justify-between rounded-md border border-slate-200 px-3 py-2 text-xs dark:border-slate-700"
                  >
                    <span class="font-medium text-slate-600 dark:text-slate-300">{{ item.label }}</span>
                    <span class="font-semibold text-slate-800 dark:text-slate-100">{{ item.total }}</span>
                  </div>
                </div>
              </div>
            </div>
          </template>

          <template v-else>
            <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-3">
              <div>
                <h4 class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                  Aktivitas Bulanan
                </h4>
                <div class="h-72">
                  <BarChart :data="buildActivityMonthlyChartData(block)" />
                </div>
                <p v-if="!hasAnyChartData(buildActivityMonthlyChartData(block))" class="mt-3 text-xs text-amber-700 dark:text-amber-300">
                  Belum ada aktivitas terhitung untuk periode ini.
                </p>
              </div>

              <div>
                <h4 class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                  Status Aktivitas
                </h4>
                <div class="h-72">
                  <BarChart :data="buildActivityStatusChartData(block)" />
                </div>
              </div>

              <div>
                <h4 class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                  Distribusi Level
                </h4>
                <div class="h-72">
                  <BarChart :data="buildActivityLevelChartData(block)" />
                </div>
              </div>
            </div>
          </template>
        </CardBox>
      </div>
    </template>

    <template v-else>
      <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">
        <CardBoxWidget :icon="mdiClipboardList" :number="activityStats.total" label="Total Aktivitas" color="text-blue-500" />
        <CardBoxWidget :icon="mdiChartTimelineVariant" :number="activityStats.this_month" label="Bulan Ini" color="text-indigo-500" />
        <CardBoxWidget :icon="mdiCheckCircle" :number="activityStats.published" label="Terpublikasi" color="text-emerald-500" />
        <CardBoxWidget :icon="mdiPencilCircle" :number="activityStats.draft" label="Draft" color="text-amber-500" />
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
            <BarChart :data="legacyCoveragePerBukuChartData" horizontal />
          </div>
        </CardBox>
        <CardBox>
          <h3 class="mb-3 text-sm font-semibold text-slate-700 dark:text-slate-100">Cakupan per Lampiran</h3>
          <div class="h-96">
            <BarChart :data="legacyCoveragePerLampiranChartData" />
          </div>
          <p v-if="!hasLegacyLampiranCoverageData" class="mt-4 text-xs text-amber-700 dark:text-amber-300">
            Belum ada data terhitung pada cakupan per lampiran untuk scope ini.
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
            <BarChart :data="legacyLevelDistributionChartData" />
          </div>
          <p v-if="!hasLegacyLevelDistributionData" class="mt-4 text-xs text-amber-700 dark:text-amber-300">
            Belum ada data terhitung pada distribusi level dokumen untuk scope ini.
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

    <CardBox>
      <p class="text-sm text-gray-600 dark:text-gray-300">
        Aplikasi Sistem Administrasi Tim Penggerak PKK masih dalam mode pengembangan, kritik dan saran masih sangat diperlukan.
      </p>
    </CardBox>
  </SectionMain>
</template>
