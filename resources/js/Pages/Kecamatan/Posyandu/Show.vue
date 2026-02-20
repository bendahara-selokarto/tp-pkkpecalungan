<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link } from '@inertiajs/vue3'
import { mdiStore } from '@mdi/js'

const props = defineProps({
  posyandu: {
    type: Object,
    required: true,
  },
})
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiStore" title="Detail Posyandu Kecamatan" main />

    <CardBox class="max-w-6xl space-y-4">
      <div class="grid gap-4 md:grid-cols-3">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Nama Posyandu</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.posyandu.nama_posyandu }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Nama Pengelola</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.posyandu.nama_pengelola }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Nama Sekretaris</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.posyandu.nama_sekretaris }}</p>
        </div>
      </div>

      <div class="grid gap-4 md:grid-cols-4">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Jenis Posyandu</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.posyandu.jenis_posyandu }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Jumlah Kader</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.posyandu.jumlah_kader }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Jenis Kegiatan</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.posyandu.jenis_kegiatan }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Frekuensi</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.posyandu.frekuensi_layanan }}</p>
        </div>
      </div>

      <div class="grid gap-4 md:grid-cols-4">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Pengunjung (L)</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.posyandu.jumlah_pengunjung_l }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Pengunjung (P)</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.posyandu.jumlah_pengunjung_p }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Petugas (L)</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.posyandu.jumlah_petugas_l }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Petugas (P)</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.posyandu.jumlah_petugas_p }}</p>
        </div>
      </div>

      <div class="flex items-center justify-end gap-2">
        <Link href="/kecamatan/posyandu" class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
          Kembali
        </Link>
        <Link :href="`/kecamatan/posyandu/${props.posyandu.id}/edit`" class="inline-flex rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
          Edit
        </Link>
      </div>
    </CardBox>
  </SectionMain>
</template>
