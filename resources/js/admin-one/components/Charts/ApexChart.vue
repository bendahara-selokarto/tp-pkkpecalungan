<script setup>
import { nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue'

const props = defineProps({
  options: {
    type: Object,
    default: () => ({}),
  },
  type: {
    type: String,
    default: 'line',
  },
  series: {
    type: Array,
    default: () => [],
  },
  width: {
    type: [String, Number],
    default: '100%',
  },
  height: {
    type: [String, Number],
    default: 'auto',
  },
})

const root = ref(null)
const chartClassCache = new Map()
let chart = null
let renderSequence = 0

const chartLoaders = {
  bar: () => import('apexcharts/bar'),
  pie: () => import('apexcharts/pie'),
  fallback: () => import('apexcharts/core'),
}

const resolveChartClass = async (chartType) => {
  const normalizedType = String(chartType || '').toLowerCase()
  const cacheKey = Object.hasOwn(chartLoaders, normalizedType) ? normalizedType : 'fallback'

  if (chartClassCache.has(cacheKey)) {
    return chartClassCache.get(cacheKey)
  }

  const module = await chartLoaders[cacheKey]()
  chartClassCache.set(cacheKey, module.default)

  return module.default
}

const destroyChart = () => {
  if (!chart) {
    return
  }

  chart.destroy()
  chart = null
}

// Indonesian locale for ApexCharts. only strings that differ from the
// library defaults are defined here; the rest fall back to English.
// see https://apexcharts.com/docs/localization/
const INDONESIAN_LOCALE = {
  name: 'id',
  options: {
    months: [
      'Januari',
      'Februari',
      'Maret',
      'April',
      'Mei',
      'Juni',
      'Juli',
      'Agustus',
      'September',
      'Oktober',
      'November',
      'Desember',
    ],
    shortMonths: [
      'Jan',
      'Feb',
      'Mar',
      'Apr',
      'Mei',
      'Jun',
      'Jul',
      'Agt',
      'Sep',
      'Okt',
      'Nov',
      'Des',
    ],
    days: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
    shortDays: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
    toolbar: {
      exportToSVG: 'Ekspor ke SVG',
      exportToPNG: 'Ekspor ke PNG',
      export: 'Ekspor',
      selection: 'Pilihan',
      zoomIn: 'Perbesar',
      zoomOut: 'Perkecil',
      pan: 'Geser',
      reset: 'Atur ulang zoom',
    },
    noData: {
      text: 'Tidak ada data untuk ditampilkan',
    },
  },
};

const buildChartConfig = () => ({
  ...(props.options ?? {}),
  chart: {
    ...(props.options?.chart ?? {}),
    type: props.type || props.options?.chart?.type || 'line',
    width: props.width,
    height: props.height,
    // always register indonesian locale and make it the default
    locales: [INDONESIAN_LOCALE],
    defaultLocale: 'id',
  },
  series: props.series ?? [],
})

const renderChart = async () => {
  const sequence = ++renderSequence

  await nextTick()
  if (!root.value) {
    return
  }

  const ApexCharts = await resolveChartClass(props.type)
  if (sequence !== renderSequence || !root.value) {
    return
  }

  destroyChart()
  chart = new ApexCharts(root.value, buildChartConfig())
  await chart.render()
}

const syncChart = async () => {
  if (!chart) {
    await renderChart()
    return
  }

  await chart.updateOptions(props.options ?? {}, false, true, true)
  await chart.updateSeries(props.series ?? [], true)
}

watch(
  () => [props.type, props.width, props.height],
  () => {
    void renderChart()
  },
)

watch(
  () => props.options,
  () => {
    void syncChart()
  },
  { deep: true },
)

watch(
  () => props.series,
  () => {
    void syncChart()
  },
  { deep: true },
)

onMounted(() => {
  void renderChart()
})

onBeforeUnmount(() => {
  destroyChart()
})
</script>

<template>
  <div ref="root" class="vue-apexcharts" />
</template>
