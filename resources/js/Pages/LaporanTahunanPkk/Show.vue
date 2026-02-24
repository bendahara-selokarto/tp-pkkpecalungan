<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { formatDateForDisplay } from '@/utils/dateFormatter'
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
  bidangLabels: {
    type: Object,
    default: () => ({}),
  },
  groupedEntries: {
    type: Object,
    default: () => ({}),
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
      :title="`Detail Laporan Tahunan ${scopeLabel}`"
      main
    />

    <CardBox>
      <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">
          {{ report.judul_laporan }} - {{ report.tahun_laporan }}
        </h3>
        <div class="flex items-center gap-2">
          <a
            :href="`${scopePrefix}/${report.id}/print/docx`"
            class="inline-flex rounded-md border border-sky-200 px-4 py-2 text-sm font-medium text-sky-700 hover:bg-sky-50 dark:border-sky-900/50 dark:text-sky-300 dark:hover:bg-sky-900/20"
          >
            Cetak .docx
          </a>
          <Link
            :href="`${scopePrefix}/${report.id}/edit`"
            class="inline-flex rounded-md border border-amber-200 px-4 py-2 text-sm font-medium text-amber-700 hover:bg-amber-50 dark:border-amber-900/50 dark:text-amber-300 dark:hover:bg-amber-900/20"
          >
            Edit
          </Link>
        </div>
      </div>

      <div class="grid gap-4 md:grid-cols-2">
        <div class="rounded-md border border-gray-200 p-3 dark:border-slate-700">
          <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Disusun Oleh</p>
          <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ report.disusun_oleh || '-' }}</p>
        </div>
        <div class="rounded-md border border-gray-200 p-3 dark:border-slate-700">
          <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Penanda Tangan</p>
          <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
            {{ report.jabatan_penanda_tangan || '-' }}<br>
            {{ report.nama_penanda_tangan || '-' }}
          </p>
        </div>
      </div>

      <div class="mt-4 rounded-md border border-gray-200 p-3 dark:border-slate-700">
        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Pendahuluan</p>
        <p class="mt-1 whitespace-pre-line text-sm text-gray-900 dark:text-gray-100">{{ report.pendahuluan || '-' }}</p>
      </div>

      <div class="mt-4 rounded-md border border-gray-200 p-3 dark:border-slate-700">
        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Kegiatan Secara Umum</p>
        <div class="mt-3 space-y-4">
          <div v-for="(entries, bidang) in groupedEntries" :key="bidang" class="rounded-md border border-gray-100 p-3 dark:border-slate-800">
            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ bidangLabels[bidang] || bidang }}</p>
            <div v-if="entries.length === 0" class="mt-2 text-xs text-gray-500 dark:text-gray-400">
              Belum ada data kegiatan.
            </div>
            <ol v-else class="mt-2 space-y-1 text-sm text-gray-800 dark:text-gray-100">
              <li v-for="(entry, idx) in entries" :key="`${bidang}-${idx}`">
                {{ idx + 1 }}. {{ formatDateForDisplay(entry.activity_date) }} : {{ entry.description }}
              </li>
            </ol>
          </div>
        </div>
      </div>

      <div class="mt-4 rounded-md border border-gray-200 p-3 dark:border-slate-700">
        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Keberhasilan</p>
        <p class="mt-1 whitespace-pre-line text-sm text-gray-900 dark:text-gray-100">{{ report.keberhasilan || '-' }}</p>
      </div>

      <div class="mt-4 rounded-md border border-gray-200 p-3 dark:border-slate-700">
        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Hambatan</p>
        <p class="mt-1 whitespace-pre-line text-sm text-gray-900 dark:text-gray-100">{{ report.hambatan || '-' }}</p>
      </div>

      <div class="mt-4 rounded-md border border-gray-200 p-3 dark:border-slate-700">
        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Kesimpulan</p>
        <p class="mt-1 whitespace-pre-line text-sm text-gray-900 dark:text-gray-100">{{ report.kesimpulan || '-' }}</p>
      </div>

      <div class="mt-4 rounded-md border border-gray-200 p-3 dark:border-slate-700">
        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Penutup</p>
        <p class="mt-1 whitespace-pre-line text-sm text-gray-900 dark:text-gray-100">{{ report.penutup || '-' }}</p>
      </div>
    </CardBox>
  </SectionMain>
</template>

