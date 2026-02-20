<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link } from '@inertiajs/vue3'
import { mdiAccountGroup } from '@mdi/js'

const props = defineProps({
  dataPelatihanKader: {
    type: Object,
    required: true,
  },
})
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiAccountGroup" title="Detail Data Pelatihan Kader Desa" main />

    <CardBox class="max-w-5xl">
      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">No Registrasi</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.dataPelatihanKader.nomor_registrasi }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Nama Lengkap Kader</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.dataPelatihanKader.nama_lengkap_kader }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Tanggal/Bulan/Tahun atau Sejak Tahun Masuk TP PKK</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.dataPelatihanKader.tanggal_masuk_tp_pkk }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Jabatan/Fungsi dalam TP PKK</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.dataPelatihanKader.jabatan_fungsi }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">No Urut Pelatihan</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.dataPelatihanKader.nomor_urut_pelatihan }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Judul Pelatihan</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.dataPelatihanKader.judul_pelatihan }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Jenis Kriteria Kaderisasi</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.dataPelatihanKader.jenis_kriteria_kaderisasi }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Tahun Penyelenggaraan</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.dataPelatihanKader.tahun_penyelenggaraan }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Institusi Penyelenggara</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.dataPelatihanKader.institusi_penyelenggara }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Status Sertifikat</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.dataPelatihanKader.status_sertifikat }}</p>
        </div>
      </div>

      <div class="mt-6 flex items-center justify-end gap-2">
        <Link href="/desa/data-pelatihan-kader" class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
          Kembali
        </Link>
        <Link :href="`/desa/data-pelatihan-kader/${props.dataPelatihanKader.id}/edit`" class="inline-flex rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
          Edit
        </Link>
      </div>
    </CardBox>
  </SectionMain>
</template>
