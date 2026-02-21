<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link } from '@inertiajs/vue3'
import { mdiFileDocumentOutline } from '@mdi/js'

defineProps({
  scopeLabel: {
    type: String,
    required: true,
  },
  scopePrefix: {
    type: String,
    required: true,
  },
  report: {
    type: Object,
    required: true,
  },
})
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton
      :icon="mdiFileDocumentOutline"
      :title="`Detail Laporan Pilot Project ${scopeLabel}`"
      main
    />

    <CardBox>
      <div class="mb-5 flex items-center justify-between gap-3">
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ report.judul_laporan }}</h3>
        <Link
          :href="`${scopePrefix}/${report.id}/edit`"
          class="inline-flex rounded-md border border-amber-200 px-4 py-2 text-sm font-medium text-amber-700 hover:bg-amber-50 dark:border-amber-900/50 dark:text-amber-300 dark:hover:bg-amber-900/20"
        >
          Edit
        </Link>
      </div>

      <div class="grid gap-4 md:grid-cols-2">
        <div class="rounded-md border border-gray-200 p-3 dark:border-slate-700">
          <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Periode</p>
          <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ report.tahun_awal }} - {{ report.tahun_akhir }}</p>
        </div>
        <div class="rounded-md border border-gray-200 p-3 dark:border-slate-700">
          <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Jumlah Indikator</p>
          <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ report.values.length }}</p>
        </div>
      </div>

      <div class="mt-6 overflow-x-auto">
        <table class="w-full min-w-[960px] text-sm">
          <thead class="border-b border-gray-200 dark:border-slate-700">
            <tr class="text-left text-gray-600 dark:text-gray-300">
              <th class="px-3 py-3 font-semibold">Section</th>
              <th class="px-3 py-3 font-semibold">Cluster</th>
              <th class="px-3 py-3 font-semibold">Indicator</th>
              <th class="px-3 py-3 font-semibold">Label</th>
              <th class="px-3 py-3 font-semibold text-center">Year</th>
              <th class="px-3 py-3 font-semibold text-center">Sem</th>
              <th class="px-3 py-3 font-semibold text-right">Value</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="valueRow in report.values"
              :key="valueRow.id"
              class="border-b border-gray-100 align-top dark:border-slate-800"
            >
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ valueRow.section }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ valueRow.cluster_code }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ valueRow.indicator_code }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ valueRow.indicator_label }}</td>
              <td class="px-3 py-3 text-center text-gray-700 dark:text-gray-300">{{ valueRow.year }}</td>
              <td class="px-3 py-3 text-center text-gray-700 dark:text-gray-300">{{ valueRow.semester }}</td>
              <td class="px-3 py-3 text-right text-gray-700 dark:text-gray-300">{{ valueRow.value }}</td>
            </tr>
            <tr v-if="report.values.length === 0">
              <td colspan="7" class="px-3 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                Belum ada nilai indikator.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </CardBox>
  </SectionMain>
</template>
