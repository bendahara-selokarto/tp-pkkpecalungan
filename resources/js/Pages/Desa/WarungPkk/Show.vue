<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link } from '@inertiajs/vue3'
import { mdiStore } from '@mdi/js'

const props = defineProps({
  warungPkk: {
    type: Object,
    required: true,
  },
})
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiStore" title="Detail Data Aset (Sarana) Desa/Kelurahan (Desa)" main />

    <CardBox class="max-w-4xl space-y-4">
      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Nama Aset/Sarana</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.warungPkk.nama_warung_pkk }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Nama Pengelola</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.warungPkk.nama_pengelola }}</p>
        </div>
      </div>

      <div class="grid gap-4 md:grid-cols-3">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Komoditi</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.warungPkk.komoditi }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Kategori</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.warungPkk.kategori }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Volume</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.warungPkk.volume }}</p>
        </div>
      </div>

      <div class="flex items-center justify-end gap-2">
        <Link href="/desa/warung-pkk" class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
          Kembali
        </Link>
        <Link :href="`/desa/warung-pkk/${props.warungPkk.id}/edit`" class="inline-flex rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
          Edit
        </Link>
      </div>
    </CardBox>
  </SectionMain>
</template>

