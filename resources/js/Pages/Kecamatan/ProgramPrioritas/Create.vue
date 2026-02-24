<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { mdiClipboardList } from '@mdi/js'

const JADWAL_BULAN_OPTIONS = Array.from({ length: 12 }, (_, index) => {
  const month = index + 1
  return { key: `jadwal_bulan_${month}`, label: String(month) }
})

const form = useForm({
  program: '',
  prioritas_program: '',
  kegiatan: '',
  sasaran_target: '',
  ...Object.fromEntries(JADWAL_BULAN_OPTIONS.map(({ key }) => [key, false])),
  sumber_dana_pusat: false,
  sumber_dana_apbd: false,
  sumber_dana_swd: false,
  sumber_dana_bant: false,
  keterangan: '',
})

const submit = () => {
  form.post('/kecamatan/program-prioritas')
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiClipboardList" title="Tambah Program Prioritas Kecamatan" main />

    <CardBox class="max-w-4xl">
      <form class="space-y-5" @submit.prevent="submit">
        <div class="grid gap-5 md:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Program</label>
            <input v-model="form.program" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.program" class="mt-1 text-xs text-rose-600">{{ form.errors.program }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Prioritas Program</label>
            <input v-model="form.prioritas_program" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.prioritas_program" class="mt-1 text-xs text-rose-600">{{ form.errors.prioritas_program }}</p>
          </div>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Kegiatan</label>
          <textarea v-model="form.kegiatan" rows="3" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required />
          <p v-if="form.errors.kegiatan" class="mt-1 text-xs text-rose-600">{{ form.errors.kegiatan }}</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Sasaran Target</label>
          <textarea v-model="form.sasaran_target" rows="2" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required />
          <p v-if="form.errors.sasaran_target" class="mt-1 text-xs text-rose-600">{{ form.errors.sasaran_target }}</p>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
          <div>
            <p class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Jadwal Waktu</p>
            <div class="grid grid-cols-4 gap-2 rounded-md border border-gray-200 p-3 dark:border-slate-700">
              <label
                v-for="option in JADWAL_BULAN_OPTIONS"
                :key="option.key"
                class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300"
              >
                <input v-model="form[option.key]" type="checkbox">
                {{ option.label }}
              </label>
            </div>
            <p v-if="form.errors.jadwal_bulan_1" class="mt-1 text-xs text-rose-600">{{ form.errors.jadwal_bulan_1 }}</p>
          </div>

          <div>
            <p class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Sumber Dana</p>
            <div class="grid grid-cols-2 gap-2 rounded-md border border-gray-200 p-3 dark:border-slate-700">
              <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300"><input v-model="form.sumber_dana_pusat" type="checkbox"> Pusat</label>
              <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300"><input v-model="form.sumber_dana_apbd" type="checkbox"> APB</label>
              <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300"><input v-model="form.sumber_dana_swd" type="checkbox"> SWL</label>
              <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300"><input v-model="form.sumber_dana_bant" type="checkbox"> Ban</label>
            </div>
            <p v-if="form.errors.sumber_dana_pusat" class="mt-1 text-xs text-rose-600">{{ form.errors.sumber_dana_pusat }}</p>
          </div>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Keterangan</label>
          <textarea v-model="form.keterangan" rows="2" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" />
          <p v-if="form.errors.keterangan" class="mt-1 text-xs text-rose-600">{{ form.errors.keterangan }}</p>
        </div>

        <div class="flex items-center justify-end gap-2">
          <Link href="/kecamatan/program-prioritas" class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
            Batal
          </Link>
          <button type="submit" class="inline-flex rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60" :disabled="form.processing">
            Simpan
          </button>
        </div>
      </form>
    </CardBox>
  </SectionMain>
</template>
