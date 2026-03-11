<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link } from '@inertiajs/vue3'
import { mdiAccountGroup } from '@mdi/js'

const props = defineProps({
  pelatihanKader: {
    type: Object,
    required: true,
  },
})

const kategoriLabel = (value) => {
  if (value === 'lp3') {
    return 'LP3 PKK'
  }

  if (value === 'tpk_3_pkk') {
    return 'TPK 3 PKK'
  }

  if (value === 'damas_pkk') {
    return 'Damas PKK'
  }

  return value || '-'
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiAccountGroup" title="Detail Pelatihan Kader Pokja II Kecamatan" main />

    <CardBox class="max-w-3xl space-y-4">
      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Kategori Pelatihan</p>
        <p class="text-sm text-gray-700 dark:text-gray-300">{{ kategoriLabel(props.pelatihanKader.kategori_pelatihan) }}</p>
      </div>
      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Jumlah Kader</p>
        <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.pelatihanKader.jumlah_kader }}</p>
      </div>
      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Keterangan</p>
        <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.pelatihanKader.keterangan || '-' }}</p>
      </div>
      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Tahun Anggaran</p>
        <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.pelatihanKader.tahun_anggaran }}</p>
      </div>

      <div class="flex items-center justify-end gap-2">
        <Link href="/kecamatan/pelatihan-kader-pokja-ii" class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
          Kembali
        </Link>
        <Link :href="`/kecamatan/pelatihan-kader-pokja-ii/${props.pelatihanKader.id}/edit`" class="inline-flex rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
          Edit
        </Link>
      </div>
    </CardBox>
  </SectionMain>
</template>
