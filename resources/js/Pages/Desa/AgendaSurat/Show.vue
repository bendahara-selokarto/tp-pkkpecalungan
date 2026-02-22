<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { formatDateForDisplay } from '@/utils/dateFormatter'
import { Link } from '@inertiajs/vue3'
import { mdiEmailOpenOutline } from '@mdi/js'

const props = defineProps({
  agendaSurat: {
    type: Object,
    required: true,
  },
})

const jenisLabel = (jenis) => (jenis === 'masuk' ? 'Surat Masuk' : 'Surat Keluar')
const formatDate = (value) => formatDateForDisplay(value)
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiEmailOpenOutline" title="Detail Agenda Surat Desa" main />

    <CardBox class="max-w-4xl space-y-4">
      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Jenis Surat</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ jenisLabel(props.agendaSurat.jenis_surat) }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Nomor Surat</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.agendaSurat.nomor_surat }}</p>
        </div>
      </div>

      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Tanggal Surat</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ formatDate(props.agendaSurat.tanggal_surat) }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Tanggal Terima</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ formatDate(props.agendaSurat.tanggal_terima) }}</p>
        </div>
      </div>

      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Asal Surat</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.agendaSurat.asal_surat || '-' }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Dari</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.agendaSurat.dari || '-' }}</p>
        </div>
      </div>

      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Kepada</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.agendaSurat.kepada || '-' }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Perihal</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.agendaSurat.perihal }}</p>
        </div>
      </div>

      <div class="grid gap-4 md:grid-cols-3">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Lampiran</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.agendaSurat.lampiran || '-' }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Diteruskan Kepada</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.agendaSurat.diteruskan_kepada || '-' }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Tembusan</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.agendaSurat.tembusan || '-' }}</p>
        </div>
      </div>

      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Keterangan</p>
        <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.agendaSurat.keterangan || '-' }}</p>
      </div>

      <div class="flex items-center justify-end gap-2">
        <Link href="/desa/agenda-surat" class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
          Kembali
        </Link>
        <Link :href="`/desa/agenda-surat/${props.agendaSurat.id}/edit`" class="inline-flex rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
          Edit
        </Link>
      </div>
    </CardBox>
  </SectionMain>
</template>
