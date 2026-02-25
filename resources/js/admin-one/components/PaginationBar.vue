<script setup>
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  links: {
    type: Array,
    required: true,
  },
  from: {
    type: Number,
    default: null,
  },
  to: {
    type: Number,
    default: null,
  },
  total: {
    type: Number,
    default: null,
  },
})

const shouldRender = computed(() => Array.isArray(props.links) && props.links.length > 0)
</script>

<template>
  <div v-if="shouldRender" class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <p class="text-xs text-gray-600 dark:text-gray-300">
      Menampilkan {{ from ?? 0 }}-{{ to ?? 0 }} dari {{ total ?? 0 }} data
    </p>

    <div class="flex flex-wrap items-center gap-2">
      <template v-for="(link, index) in links" :key="`page-${index}`">
        <span
          v-if="!link.url"
          class="rounded-md border border-gray-200 px-3 py-1.5 text-xs text-gray-400 dark:border-slate-700 dark:text-gray-500"
          v-html="link.label"
        />
        <Link
          v-else
          :href="link.url"
          class="rounded-md border px-3 py-1.5 text-xs"
          :class="link.active
            ? 'border-emerald-600 bg-emerald-600 text-white'
            : 'border-gray-200 text-gray-700 hover:bg-gray-100 dark:border-slate-700 dark:text-gray-300 dark:hover:bg-slate-800'"
          v-html="link.label"
        />
      </template>
    </div>
  </div>
</template>
