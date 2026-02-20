<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link } from '@inertiajs/vue3'
import { mdiAccountGroup } from '@mdi/js'

const props = defineProps({
  simulasiPenyuluhan: {
    type: Object,
    required: true,
  },
})

const totalKader = (item) => Number(item.jumlah_kader_l || 0) + Number(item.jumlah_kader_p || 0)
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiAccountGroup" title="Detail Data Isian Kelompok Simulasi dan Penyuluhan (Kecamatan)" main />

    <CardBox class="max-w-4xl space-y-4">
      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Nama Kegiatan</p>
        <p class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ props.simulasiPenyuluhan.nama_kegiatan }}</p>
      </div>

      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Jenis Simulasi dan Penyuluhan</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.simulasiPenyuluhan.jenis_simulasi_penyuluhan }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Jumlah Kelompok</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.simulasiPenyuluhan.jumlah_kelompok }}</p>
        </div>
      </div>

      <div class="grid gap-4 md:grid-cols-3">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Jumlah Sosialisasi</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.simulasiPenyuluhan.jumlah_sosialisasi }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Jumlah Kader L</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.simulasiPenyuluhan.jumlah_kader_l }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Jumlah Kader P</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.simulasiPenyuluhan.jumlah_kader_p }}</p>
        </div>
      </div>

      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Total Kader</p>
        <p class="text-sm text-gray-700 dark:text-gray-300">{{ totalKader(props.simulasiPenyuluhan) }}</p>
      </div>

      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Keterangan</p>
        <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.simulasiPenyuluhan.keterangan || '-' }}</p>
      </div>

      <div class="flex items-center justify-end gap-2">
        <Link href="/kecamatan/simulasi-penyuluhan" class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
          Kembali
        </Link>
        <Link :href="`/kecamatan/simulasi-penyuluhan/${props.simulasiPenyuluhan.id}/edit`" class="inline-flex rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
          Edit
        </Link>
      </div>
    </CardBox>
  </SectionMain>
</template>


