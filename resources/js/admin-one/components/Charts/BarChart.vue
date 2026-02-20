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

const maxValue = computed(() => {
  if (values.value.length === 0) {
    return 1
  }

  return Math.max(...values.value, 1)
})

const items = computed(() => labels.value.map((label, index) => ({
  label,
  value: values.value[index] ?? 0,
  color: colors.value[index] ?? '#10b981',
})))
</script>

<template>
  <div v-if="horizontal" class="h-full overflow-y-auto pr-1">
    <div class="space-y-3">
      <div
        v-for="item in items"
        :key="item.label"
        class="grid grid-cols-[minmax(0,11rem)_1fr_auto] items-center gap-3"
      >
        <p class="truncate text-xs text-slate-600 dark:text-slate-300">{{ item.label }}</p>
        <div class="h-3 rounded-full bg-slate-200 dark:bg-slate-700">
          <div
            class="h-3 rounded-full transition-all duration-300"
            :style="{
              width: `${(item.value / maxValue) * 100}%`,
              backgroundColor: item.color,
            }"
          />
        </div>
        <p class="text-xs font-semibold text-slate-700 dark:text-slate-200">{{ item.value }}</p>
      </div>
    </div>
  </div>

  <div v-else class="flex h-full items-end gap-3 overflow-x-auto pb-2">
    <div
      v-for="item in items"
      :key="item.label"
      class="flex min-w-14 flex-1 flex-col items-center gap-2"
    >
      <div class="flex h-full w-full min-h-36 items-end rounded-md bg-slate-200 p-1 dark:bg-slate-700">
        <div
          class="w-full rounded-md transition-all duration-300"
          :style="{
            height: `${(item.value / maxValue) * 100}%`,
            backgroundColor: item.color,
          }"
        />
      </div>
      <p class="w-full truncate text-center text-[10px] text-slate-600 dark:text-slate-300">{{ item.label }}</p>
      <p class="text-xs font-semibold text-slate-700 dark:text-slate-200">{{ item.value }}</p>
    </div>
  </div>
</template>
