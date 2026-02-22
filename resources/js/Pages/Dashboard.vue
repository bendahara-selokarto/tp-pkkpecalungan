<script setup>
import { computed } from 'vue'
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

const humanizeLabel = (value) => String(value ?? '')
  .replace(/[-_]+/g, ' ')
  .trim()
  .replace(/\b\w/g, (char) => char.toUpperCase())

const coveragePerBukuItems = computed(() => {
  const rawItems = documentCharts.value.coverage_per_buku?.items
  if (Array.isArray(rawItems) && rawItems.length > 0) {
    return rawItems.map((item, index) => ({
      label: String(item?.label ?? humanizeLabel(item?.slug ?? `Buku ${index + 1}`)),
      total: Number(item?.total ?? 0),
    }))
  }

  const labels = documentCharts.value.coverage_per_buku?.labels ?? []
  const values = documentCharts.value.coverage_per_buku?.values ?? []

  return labels.map((label, index) => ({
    label: humanizeLabel(label),
    total: Number(values[index] ?? 0),
  }))
})

const coveragePerBukuChartData = computed(() => ({
  labels: coveragePerBukuItems.value.map((item) => item.label),
  datasets: [
    {
      data: coveragePerBukuItems.value.map((item) => item.total),
      backgroundColor: '#10b981',
      borderRadius: 6,
    },
  ],
}))

const coveragePerLampiranChartData = computed(() => ({
  labels: documentCharts.value.coverage_per_lampiran?.labels ?? [],
  datasets: [
    {
      data: documentCharts.value.coverage_per_lampiran?.values ?? [],
      backgroundColor: '#0ea5e9',
      borderRadius: 6,
    },
  ],
}))

const levelDistributionChartData = computed(() => ({
  labels: documentCharts.value.level_distribution?.labels ?? ['Desa', 'Kecamatan'],
  datasets: [
    {
      data: documentCharts.value.level_distribution?.values ?? [0, 0],
      backgroundColor: ['#f59e0b', '#6366f1'],
      borderRadius: 6,
    },
  ],
}))

const lampiranCoverageItems = computed(() => {
  const rawItems = documentCharts.value.coverage_per_lampiran?.items
  if (Array.isArray(rawItems) && rawItems.length > 0) {
    return rawItems.map((item) => ({
      label: String(item?.lampiran_group ?? '-'),
      total: Number(item?.total ?? 0),
    }))
  }

  const labels = documentCharts.value.coverage_per_lampiran?.labels ?? []
  const values = documentCharts.value.coverage_per_lampiran?.values ?? []

  return labels.map((label, index) => ({
    label: String(label),
    total: Number(values[index] ?? 0),
  }))
})

const levelDistributionItems = computed(() => {
  const labels = documentCharts.value.level_distribution?.labels ?? ['Desa', 'Kecamatan']
  const values = documentCharts.value.level_distribution?.values ?? [0, 0]

  return labels.map((label, index) => ({
    label: String(label),
    total: Number(values[index] ?? 0),
  }))
})

const hasLampiranCoverageData = computed(() =>
  lampiranCoverageItems.value.some((item) => item.total > 0)
)

const hasLevelDistributionData = computed(() =>
  levelDistributionItems.value.some((item) => item.total > 0)
)
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiChartTimelineVariant" title="Dashboard" main />

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
          <BarChart :data="coveragePerBukuChartData" horizontal />
        </div>
      </CardBox>
      <CardBox>
        <h3 class="mb-3 text-sm font-semibold text-slate-700 dark:text-slate-100">Cakupan per Lampiran</h3>
        <div class="h-96">
          <BarChart :data="coveragePerLampiranChartData" />
        </div>
        <p v-if="!hasLampiranCoverageData" class="mt-4 text-xs text-amber-700 dark:text-amber-300">
          Belum ada data terhitung pada cakupan per lampiran untuk scope ini.
        </p>
        <div class="mt-4 grid grid-cols-1 gap-2 sm:grid-cols-2">
          <div
            v-for="item in lampiranCoverageItems"
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
          <BarChart :data="levelDistributionChartData" />
        </div>
        <p v-if="!hasLevelDistributionData" class="mt-4 text-xs text-amber-700 dark:text-amber-300">
          Belum ada data terhitung pada distribusi level dokumen untuk scope ini.
        </p>
        <div class="mt-4 grid grid-cols-1 gap-2 sm:grid-cols-2">
          <div
            v-for="item in levelDistributionItems"
            :key="`level-${item.label}`"
            class="flex items-center justify-between rounded-md border border-slate-200 px-3 py-2 text-xs dark:border-slate-700"
          >
            <span class="font-medium text-slate-600 dark:text-slate-300">{{ item.label }}</span>
            <span class="font-semibold text-slate-800 dark:text-slate-100">{{ item.total }}</span>
          </div>
        </div>
      </CardBox>
    </div>

    <CardBox>
      <p class="text-sm text-gray-600 dark:text-gray-300">
        Aplikasi Sistem Administrasi Tim Penggerak PKK masih dalam mode pengembangan, kritik dan saran masih sangat diperlukan.
      </p>
    </CardBox>
  </SectionMain>
</template>
