<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link } from '@inertiajs/vue3'
import { mdiTrophy } from '@mdi/js'

const props = defineProps({
  prestasiLomba: {
    type: Object,
    required: true,
  },
})

const formatCapaian = (item) => {
  const capaian = []
  if (item.prestasi_kecamatan) capaian.push('Kecamatan')
  if (item.prestasi_kabupaten) capaian.push('Kabupaten')
  if (item.prestasi_provinsi) capaian.push('Provinsi')
  if (item.prestasi_nasional) capaian.push('Nasional')
  return capaian.join(', ') || '-'
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiTrophy" title="Detail Buku Prestasi Desa" main />

    <CardBox class="max-w-4xl space-y-4">
      <div class="grid gap-4 md:grid-cols-3">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Tahun</p>
          <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ props.prestasiLomba.tahun }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Jenis Lomba</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.prestasiLomba.jenis_lomba }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Lokasi</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.prestasiLomba.lokasi }}</p>
        </div>
      </div>

      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Prestasi/Keberhasilan</p>
        <p class="text-sm text-gray-700 dark:text-gray-300">{{ formatCapaian(props.prestasiLomba) }}</p>
      </div>

      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Keterangan</p>
        <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.prestasiLomba.keterangan || '-' }}</p>
      </div>

      <div class="flex items-center justify-end gap-2">
        <Link href="/desa/prestasi-lomba" class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
          Kembali
        </Link>
        <Link :href="`/desa/prestasi-lomba/${props.prestasiLomba.id}/edit`" class="inline-flex rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
          Edit
        </Link>
      </div>
    </CardBox>
  </SectionMain>
</template>

