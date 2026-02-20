<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link } from '@inertiajs/vue3'
import { mdiAccountGroup } from '@mdi/js'

const props = defineProps({
  koperasi: {
    type: Object,
    required: true,
  },
})

const statusHukumLabel = () => {
  if (props.koperasi.berbadan_hukum) {
    return 'Berbadan Hukum'
  }

  if (props.koperasi.belum_berbadan_hukum) {
    return 'Belum Berbadan Hukum'
  }

  return '-'
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiAccountGroup" title="Detail Koperasi Desa" main />

    <CardBox class="max-w-4xl space-y-4">
      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Nama Koperasi</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.koperasi.nama_koperasi }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Jenis Usaha</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.koperasi.jenis_usaha }}</p>
        </div>
      </div>

      <div class="grid gap-4 md:grid-cols-3">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Status Hukum</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ statusHukumLabel() }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Jumlah Anggota L</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.koperasi.jumlah_anggota_l }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Jumlah Anggota P</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.koperasi.jumlah_anggota_p }}</p>
        </div>
      </div>

      <div class="flex items-center justify-end gap-2">
        <Link href="/desa/koperasi" class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
          Kembali
        </Link>
        <Link :href="`/desa/koperasi/${props.koperasi.id}/edit`" class="inline-flex rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
          Edit
        </Link>
      </div>
    </CardBox>
  </SectionMain>
</template>
