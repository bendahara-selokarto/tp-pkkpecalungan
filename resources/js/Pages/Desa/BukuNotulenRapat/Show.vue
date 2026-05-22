<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link } from '@inertiajs/vue3'
import { mdiNotebookEditOutline } from '@mdi/js'
import { formatDateForDisplay } from '@/utils/dateFormatter'

const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
})
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiNotebookEditOutline" title="Detail Buku Notulen Rapat Desa" main />

    <CardBox class="max-w-3xl space-y-6">
      <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Tanggal Notulen</p>
          <p class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ formatDateForDisplay(props.item.entry_date) }}</p>
        </div>

        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Judul Rapat</p>
          <p class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ props.item.title }}</p>
        </div>
      </div>

      <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Nama Pihak/Petugas</p>
          <p class="text-base text-gray-900 dark:text-gray-100">{{ props.item.person_name || '-' }}</p>
        </div>

        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Instansi/Unit</p>
          <p class="text-base text-gray-900 dark:text-gray-100">{{ props.item.institution || '-' }}</p>
        </div>
      </div>

      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Uraian Notulen</p>
        <p class="mt-1 text-sm text-gray-700 whitespace-pre-wrap dark:text-gray-300">{{ props.item.description || '-' }}</p>
      </div>

      <div v-if="props.item.file_url">
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Berkas Notulen</p>
        <a
          :href="props.item.file_url"
          target="_blank"
          class="inline-flex mt-1 items-center gap-1 text-emerald-600 hover:underline dark:text-emerald-400 font-medium"
        >
          Lihat/Unduh File
        </a>
      </div>

      <div class="flex items-center justify-end gap-2 border-t pt-4 dark:border-slate-800">
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
