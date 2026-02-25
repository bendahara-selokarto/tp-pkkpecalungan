<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { formatDateForDisplay } from '@/utils/dateFormatter'
import { Link } from '@inertiajs/vue3'
import { mdiAccountGroup } from '@mdi/js'

const props = defineProps({
  anggotaTimPenggerak: {
    type: Object,
    required: true,
  },
})

const formatJenisKelamin = (value) => (value === 'L' ? 'Laki-laki' : 'Perempuan')
const formatStatusPerkawinan = (value) => (value === 'kawin' ? 'Kawin' : 'Tidak Kawin')
const formatDate = (value) => formatDateForDisplay(value)
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiAccountGroup" title="Detail Anggota Tim Penggerak PKK Desa" main />

    <CardBox class="max-w-4xl space-y-4">
      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Nama</p>
        <p class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ props.anggotaTimPenggerak.nama }}</p>
      </div>

      <div class="grid gap-4 md:grid-cols-3">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Jabatan</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.anggotaTimPenggerak.jabatan }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Jenis Kelamin</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ formatJenisKelamin(props.anggotaTimPenggerak.jenis_kelamin) }}</p>
        </div>
      </div>

      <div class="grid gap-4 md:grid-cols-3">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Tempat Lahir</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.anggotaTimPenggerak.tempat_lahir }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Tanggal Lahir</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ formatDate(props.anggotaTimPenggerak.tanggal_lahir) }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Umur</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.anggotaTimPenggerak.umur ?? '-' }} tahun</p>
        </div>
      </div>

      <div class="grid gap-4 md:grid-cols-3">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Status Perkawinan</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ formatStatusPerkawinan(props.anggotaTimPenggerak.status_perkawinan) }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Pendidikan</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.anggotaTimPenggerak.pendidikan }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Pekerjaan</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.anggotaTimPenggerak.pekerjaan }}</p>
        </div>
      </div>

      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Alamat</p>
        <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.anggotaTimPenggerak.alamat }}</p>
      </div>

      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Keterangan</p>
        <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.anggotaTimPenggerak.keterangan || '-' }}</p>
      </div>

      <div class="flex items-center justify-end gap-2">
        <Link href="/desa/anggota-tim-penggerak" class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
          Kembali
        </Link>
        <Link :href="`/desa/anggota-tim-penggerak/${props.anggotaTimPenggerak.id}/edit`" class="inline-flex rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
          Edit
        </Link>
      </div>
    </CardBox>
  </SectionMain>
</template>


