<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { formatDateForDisplay } from '@/utils/dateFormatter'
import { Link } from '@inertiajs/vue3'
import { mdiNotebookEditOutline } from '@mdi/js'

const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
})

const formatDate = (value) => formatDateForDisplay(value)
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiNotebookEditOutline" title="Detail Buku Notulen Rapat Desa" main />

    <CardBox class="max-w-3xl space-y-4">
      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Tanggal Rapat</p>
        <p class="text-sm text-gray-700 dark:text-gray-300">{{ formatDate(props.item.entry_date) }}</p>
      </div>

      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Judul Rapat</p>
        <p class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ props.item.title }}</p>
      </div>

      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Nama Pihak/Petugas</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.item.person_name || '-' }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Instansi/Unit</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.item.institution || '-' }}</p>
        </div>
      </div>

      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Uraian Notulen</p>
        <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ props.item.description || '-' }}</p>
      </div>

      <div class="flex items-center justify-end gap-2">
        <Link
          href="/desa/buku-notulen-rapat"
          class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
        >
          Kembali
        </Link>
        <Link
          :href="`/desa/buku-notulen-rapat/${props.item.id}/edit`"
          class="inline-flex rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700"
        >
          Edit
        </Link>
      </div>
    </CardBox>
  </SectionMain>
</template>
