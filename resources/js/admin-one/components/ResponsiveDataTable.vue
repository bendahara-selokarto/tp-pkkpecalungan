<script setup>
import { computed } from 'vue'

const props = defineProps({
  columns: {
    type: Array,
    required: true,
  },
  rows: {
    type: Array,
    default: () => [],
  },
  rowKey: {
    type: [String, Function],
    default: 'id',
  },
  minWidthClass: {
    type: String,
    default: 'min-w-[760px]',
  },
  emptyText: {
    type: String,
    default: 'Belum ada data.',
  },
})

const resolvedColumns = computed(() =>
  (Array.isArray(props.columns) ? props.columns : [])
    .filter((column) => column && typeof column === 'object')
    .map((column, index) => ({
      key: String(column.key ?? `column-${index}`),
      label: String(column.label ?? ''),
      mobileLabel: String(column.mobileLabel ?? column.label ?? ''),
      headerClass: String(column.headerClass ?? ''),
      cellClass: String(column.cellClass ?? ''),
    })))

const resolveRowKey = (row, index) => {
  if (typeof props.rowKey === 'function') {
    return props.rowKey(row, index)
  }

  if (typeof props.rowKey === 'string' && props.rowKey.length > 0) {
    return row?.[props.rowKey] ?? `${index}`
  }

  return `${index}`
}
</script>

<template>
  <div class="space-y-3 lg:hidden">
    <div
      v-for="(row, index) in rows"
      :key="resolveRowKey(row, index)"
      class="rounded-lg border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-900/40"
    >
      <dl class="space-y-2">
        <div
          v-for="column in resolvedColumns"
          :key="`${resolveRowKey(row, index)}-${column.key}`"
          class="flex items-start justify-between gap-3"
        >
          <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
            {{ column.mobileLabel }}
          </dt>
          <dd :class="column.cellClass" class="text-sm text-right text-slate-700 dark:text-slate-100">
            <slot :name="`cell-${column.key}`" :row="row" :value="row?.[column.key]" :column="column">
              {{ row?.[column.key] ?? '-' }}
            </slot>
          </dd>
        </div>
      </dl>
    </div>
    <div
      v-if="rows.length === 0"
      class="rounded-lg border border-dashed border-slate-300 px-4 py-5 text-center text-sm text-slate-500 dark:border-slate-600 dark:text-slate-300"
    >
      {{ emptyText }}
    </div>
  </div>

  <div class="hidden overflow-x-auto lg:block">
    <table class="w-full text-sm" :class="minWidthClass">
      <thead class="border-b border-gray-200 dark:border-slate-700">
        <tr class="text-left text-gray-600 dark:text-gray-300">
          <th
            v-for="column in resolvedColumns"
            :key="`head-${column.key}`"
            class="px-3 py-3 font-semibold"
            :class="column.headerClass"
          >
            {{ column.label }}
          </th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="(row, index) in rows"
          :key="`row-${resolveRowKey(row, index)}`"
          class="border-b border-gray-100 align-top dark:border-slate-800"
        >
          <td
            v-for="column in resolvedColumns"
            :key="`cell-${resolveRowKey(row, index)}-${column.key}`"
            class="px-3 py-3 text-gray-700 dark:text-gray-300"
            :class="column.cellClass"
          >
            <slot :name="`cell-${column.key}`" :row="row" :value="row?.[column.key]" :column="column">
              {{ row?.[column.key] ?? '-' }}
            </slot>
          </td>
        </tr>
        <tr v-if="rows.length === 0">
          <td
            :colspan="resolvedColumns.length"
            class="px-3 py-6 text-center text-sm text-gray-500 dark:text-gray-400"
          >
            {{ emptyText }}
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
