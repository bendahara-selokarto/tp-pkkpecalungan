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
})

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
  colors: labels.value.map(() => '#475569'),
  fontSize: '11px',
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
    borderColor: '#e2e8f0',
    strokeDashArray: 4,
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
    text: 'Belum ada data',
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
