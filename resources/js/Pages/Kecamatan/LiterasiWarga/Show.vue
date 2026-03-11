<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link } from '@inertiajs/vue3'
import { mdiBookOpenVariant } from '@mdi/js'

const props = defineProps({
  literasiWarga: {
    type: Object,
    required: true,
  },
})
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiBookOpenVariant" title="Detail Literasi Warga Kecamatan" main />

    <CardBox class="max-w-3xl space-y-4">
      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Jumlah Warga 3 (Tiga) Buta</p>
        <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.literasiWarga.jumlah_tiga_buta }}</p>
      </div>
      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Keterangan</p>
        <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.literasiWarga.keterangan || '-' }}</p>
      </div>
      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Tahun Anggaran</p>
        <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.literasiWarga.tahun_anggaran }}</p>
      </div>

      <div class="flex items-center justify-end gap-2">
        <Link href="/kecamatan/literasi-warga" class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
          Kembali
        </Link>
        <Link :href="`/kecamatan/literasi-warga/${props.literasiWarga.id}/edit`" class="inline-flex rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
          Edit
        </Link>
      </div>
    </CardBox>
  </SectionMain>
</template>
