<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link } from '@inertiajs/vue3'
import { mdiAccountGroup } from '@mdi/js'

const props = defineProps({
  bkr: {
    type: Object,
    required: true,
  },
})
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiAccountGroup" title="Detail BKR Desa" main />

    <CardBox class="max-w-4xl space-y-4">
      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Desa</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.bkr.desa }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Nama BKR</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.bkr.nama_bkr }}</p>
        </div>
      </div>

      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">No/Tgl SK</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.bkr.no_tgl_sk }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Nama Ketua Kelompok</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.bkr.nama_ketua_kelompok }}</p>
        </div>
      </div>

      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Jumlah Anggota</p>
        <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.bkr.jumlah_anggota }}</p>
      </div>

      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Kegiatan</p>
        <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.bkr.kegiatan }}</p>
      </div>

      <div class="flex items-center justify-end gap-2">
        <Link href="/desa/bkr" class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
          Kembali
        </Link>
        <Link :href="`/desa/bkr/${props.bkr.id}/edit`" class="inline-flex rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
          Edit
        </Link>
      </div>
    </CardBox>
  </SectionMain>
</template>

