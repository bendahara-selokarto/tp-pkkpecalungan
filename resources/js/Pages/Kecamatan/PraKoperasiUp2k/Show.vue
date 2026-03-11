<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link } from '@inertiajs/vue3'
import { mdiAccountGroup } from '@mdi/js'

const props = defineProps({
  praKoperasi: {
    type: Object,
    required: true,
  },
})

const tingkatLabel = (value) => {
  if (value === 'pemula') {
    return 'Pemula'
  }

  if (value === 'madya') {
    return 'Madya'
  }

  if (value === 'utama') {
    return 'Utama'
  }

  if (value === 'mandiri') {
    return 'Mandiri'
  }

  return value || '-'
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiAccountGroup" title="Detail Pra Koperasi/UP2K Kecamatan" main />

    <CardBox class="max-w-3xl space-y-4">
      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Tingkat</p>
        <p class="text-sm text-gray-700 dark:text-gray-300">{{ tingkatLabel(props.praKoperasi.tingkat) }}</p>
      </div>
      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Jumlah Kelompok</p>
        <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.praKoperasi.jumlah_kelompok }}</p>
      </div>
      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Jumlah Peserta</p>
        <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.praKoperasi.jumlah_peserta }}</p>
      </div>
      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Keterangan</p>
        <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.praKoperasi.keterangan || '-' }}</p>
      </div>
      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Tahun Anggaran</p>
        <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.praKoperasi.tahun_anggaran }}</p>
      </div>

      <div class="flex items-center justify-end gap-2">
        <Link href="/kecamatan/pra-koperasi-up2k" class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
          Kembali
        </Link>
        <Link :href="`/kecamatan/pra-koperasi-up2k/${props.praKoperasi.id}/edit`" class="inline-flex rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
          Edit
        </Link>
      </div>
    </CardBox>
  </SectionMain>
</template>
