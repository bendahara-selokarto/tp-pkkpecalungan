<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link } from '@inertiajs/vue3'
import { mdiClipboardList } from '@mdi/js'

const props = defineProps({
  dataKegiatanWarga: {
    type: Object,
    required: true,
  },
})
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiClipboardList" title="Detail Data Kegiatan Warga Desa" main />

    <CardBox class="max-w-4xl space-y-4">
      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Kegiatan</p>
        <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.dataKegiatanWarga.kegiatan }}</p>
      </div>

      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Aktivitas (Y/T)</p>
        <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.dataKegiatanWarga.aktivitas_label }}</p>
      </div>

      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Keterangan (Jenis kegiatan yang diikuti)</p>
        <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.dataKegiatanWarga.keterangan || '-' }}</p>
      </div>

      <div class="flex items-center justify-end gap-2">
        <Link href="/desa/data-kegiatan-warga" class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
          Kembali
        </Link>
        <Link :href="`/desa/data-kegiatan-warga/${props.dataKegiatanWarga.id}/edit`" class="inline-flex rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
          Edit
        </Link>
      </div>
    </CardBox>
  </SectionMain>
</template>
