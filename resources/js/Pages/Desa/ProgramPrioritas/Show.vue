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

const formatJadwal = (item) => {
  const jadwal = []
  if (item.jadwal_i) jadwal.push('I')
  if (item.jadwal_ii) jadwal.push('II')
  if (item.jadwal_iii) jadwal.push('III')
  if (item.jadwal_iv) jadwal.push('IV')
  return jadwal.join(', ') || '-'
}

const formatDana = (item) => {
  const dana = []
  if (item.sumber_dana_pusat) dana.push('Pusat')
  if (item.sumber_dana_apbd) dana.push('APBD')
  if (item.sumber_dana_swd) dana.push('SWD')
  if (item.sumber_dana_bant) dana.push('Bant')
  return dana.join(', ') || '-'
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiClipboardList" title="Detail Program Prioritas Desa" main />

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
        <Link href="/desa/program-prioritas" class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
          Kembali
        </Link>
        <Link :href="`/desa/program-prioritas/${props.programPrioritas.id}/edit`" class="inline-flex rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
          Edit
        </Link>
      </div>
    </CardBox>
  </SectionMain>
</template>
