<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link } from '@inertiajs/vue3'
import { mdiAccountGroup } from '@mdi/js'

const props = defineProps({
  dataIndustriRumahTangga: {
    type: Object,
    required: true,
  },
})
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiAccountGroup" title="Detail Data Industri Rumah Tangga Desa" main />

    <CardBox class="max-w-4xl space-y-4">
      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Kategori Jenis Industri Rumah Tangga</p>
        <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.dataIndustriRumahTangga.kategori_jenis_industri }}</p>
      </div>

      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Komoditi yang Diusahakan</p>
        <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.dataIndustriRumahTangga.komoditi }}</p>
      </div>

      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Jumlah Komoditi yang Dikelola</p>
        <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.dataIndustriRumahTangga.jumlah_komoditi }}</p>
      </div>

      <div class="flex items-center justify-end gap-2">
        <Link href="/desa/data-industri-rumah-tangga" class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
          Kembali
        </Link>
        <Link :href="`/desa/data-industri-rumah-tangga/${props.dataIndustriRumahTangga.id}/edit`" class="inline-flex rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
          Edit
        </Link>
      </div>
    </CardBox>
  </SectionMain>
</template>


