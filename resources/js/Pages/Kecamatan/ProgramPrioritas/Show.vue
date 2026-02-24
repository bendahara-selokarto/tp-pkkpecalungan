<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link } from '@inertiajs/vue3'
import { mdiClipboardList } from '@mdi/js'

const props = defineProps({
  programPrioritas: {
    type: Object,
    required: true,
  },
})

const JADWAL_BULAN_OPTIONS = Array.from({ length: 12 }, (_, index) => {
  const month = index + 1
  return { key: `jadwal_bulan_${month}`, label: String(month) }
})

const formatJadwal = (item) => {
  const jadwal = JADWAL_BULAN_OPTIONS
    .filter(({ key }) => item[key])
    .map(({ label }) => label)

  if (jadwal.length === 0) {
    if (item.jadwal_i) jadwal.push('1', '2', '3')
    if (item.jadwal_ii) jadwal.push('4', '5', '6')
    if (item.jadwal_iii) jadwal.push('7', '8', '9')
    if (item.jadwal_iv) jadwal.push('10', '11', '12')
  }

  return jadwal.join(', ') || '-'
}

const formatDana = (item) => {
  const dana = []
  if (item.sumber_dana_pusat) dana.push('Pusat')
  if (item.sumber_dana_apbd) dana.push('APB')
  if (item.sumber_dana_swd) dana.push('SWL')
  if (item.sumber_dana_bant) dana.push('Ban')
  return dana.join(', ') || '-'
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiClipboardList" title="Detail Program Prioritas Kecamatan" main />

    <CardBox class="max-w-4xl space-y-4">
      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Program</p>
          <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ props.programPrioritas.program }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Prioritas Program</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.programPrioritas.prioritas_program }}</p>
        </div>
      </div>

      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Kegiatan</p>
        <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.programPrioritas.kegiatan }}</p>
      </div>

      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Sasaran Target</p>
        <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.programPrioritas.sasaran_target }}</p>
      </div>

      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Jadwal Waktu</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ formatJadwal(props.programPrioritas) }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Sumber Dana</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ formatDana(props.programPrioritas) }}</p>
        </div>
      </div>

      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Keterangan</p>
        <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.programPrioritas.keterangan || '-' }}</p>
      </div>

      <div class="flex items-center justify-end gap-2">
        <Link href="/kecamatan/program-prioritas" class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
          Kembali
        </Link>
        <Link :href="`/kecamatan/program-prioritas/${props.programPrioritas.id}/edit`" class="inline-flex rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
          Edit
        </Link>
      </div>
    </CardBox>
  </SectionMain>
</template>
