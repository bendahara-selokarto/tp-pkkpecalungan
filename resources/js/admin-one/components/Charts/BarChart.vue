<script setup>
import { computed } from 'vue'

const props = defineProps({
  data: {
    type: Object,
    required: true,
  },
  horizontal: {
    type: Boolean,
    default: false,
  },
  emptyText: {
    type: String,
    default: 'Belum ada data untuk filter yang dipilih.',
  },
})

const CHART_AXIS_LABEL_COLOR = '#64748b'
const CHART_AXIS_LABEL_FONT_SIZE = '11px'
const CHART_GRID_BORDER_COLOR = '#e2e8f0'
const CHART_GRID_STROKE_DASH = 4

const labels = computed(() => {
  const rawLabels = props.data?.labels

  if (!Array.isArray(rawLabels)) {
    return []
  }

  return rawLabels.map((label) => String(label))
})

const values = computed(() => {
  const rawData = props.data?.datasets?.[0]?.data

  if (!Array.isArray(rawData)) {
    return []
  }

  return rawData.map((value) => Number(value) || 0)
})

const colors = computed(() => {
  const background = props.data?.datasets?.[0]?.backgroundColor

  if (Array.isArray(background)) {
    return background.map((value) => String(value))
  }

  if (typeof background === 'string') {
    return values.value.map(() => background)
  }

  return values.value.map(() => '#10b981')
})

const chartSeries = computed(() => [{
  name: 'Total',
  data: values.value,
}])

const labelStyles = computed(() => ({
  colors: labels.value.map(() => CHART_AXIS_LABEL_COLOR),
  fontSize: CHART_AXIS_LABEL_FONT_SIZE,
}))

const chartOptions = computed(() => ({
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
      horizontal: props.horizontal,
      distributed: true,
      borderRadius: 4,
      borderRadiusApplication: 'end',
      columnWidth: props.horizontal ? '70%' : '55%',
      barHeight: props.horizontal ? '65%' : undefined,
    },
  },
  colors: colors.value,
  dataLabels: {
    enabled: false,
  },
  legend: {
    show: false,
  },
  stroke: {
    show: false,
  },
  fill: {
    opacity: 1,
  },
  grid: {
    borderColor: CHART_GRID_BORDER_COLOR,
    strokeDashArray: CHART_GRID_STROKE_DASH,
    xaxis: {
      lines: {
        show: false,
      },
    },
  },
  tooltip: {
    theme: 'light',
  },
  xaxis: {
    categories: labels.value,
    labels: {
      style: labelStyles.value,
    },
    axisBorder: {
      show: false,
    },
    axisTicks: {
      show: false,
    },
  },
  yaxis: {
    labels: {
      style: labelStyles.value,
    },
  },
  noData: {
    text: props.emptyText,
    align: 'center',
    verticalAlign: 'middle',
  },
}))
</script>

<template>
  <div class="h-full w-full">
    <apexchart type="bar" width="100%" height="100%" :options="chartOptions" :series="chartSeries" />
  </div>
</template>
