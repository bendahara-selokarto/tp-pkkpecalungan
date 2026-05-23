<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { formatDateForDisplay } from '@/utils/dateFormatter'
import { Link } from '@inertiajs/vue3'
import { mdiAccountVoice } from '@mdi/js'

const props = defineProps({
  bukuKonsultasi: {
    type: Object,
    required: true,
  },
})

const formatDate = (value) => formatDateForDisplay(value)
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiAccountVoice" title="Detail Catatan Konsultasi" main />

    <CardBox class="max-w-4xl">
      <div class="space-y-6">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
          <div>
            <h4 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Hari / Tanggal</h4>
            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ formatDate(bukuKonsultasi.activity_date) }}</p>
          </div>
          <div>
            <h4 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Level</h4>
            <p class="capitalize text-gray-900 dark:text-gray-100">{{ bukuKonsultasi.level }}</p>
          </div>
        </div>

        <div>
          <h4 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Uraian Kegiatan</h4>
          <div class="mt-1 whitespace-pre-wrap rounded-md bg-gray-50 p-4 text-gray-900 dark:bg-slate-800 dark:text-gray-100">
            {{ bukuKonsultasi.description }}
          </div>
        </div>

        <div>
          <h4 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Disposisi</h4>
          <div class="mt-1 whitespace-pre-wrap rounded-md bg-gray-50 p-4 text-gray-900 dark:bg-slate-800 dark:text-gray-100">
            {{ bukuKonsultasi.disposition || '-' }}
          </div>
        </div>

        <div class="flex items-center gap-2 pt-4">
          <Link
            href="/kecamatan/buku-konsultasi"
            class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
          >
            Kembali
          </Link>
          <Link
            :href="`/kecamatan/buku-konsultasi/${bukuKonsultasi.id}/edit`"
            class="inline-flex rounded-md bg-amber-600 px-4 py-2 text-sm font-medium text-white hover:bg-amber-700"
          >
            Edit
          </Link>
        </div>
      </div>
    </CardBox>
  </SectionMain>
</template>
