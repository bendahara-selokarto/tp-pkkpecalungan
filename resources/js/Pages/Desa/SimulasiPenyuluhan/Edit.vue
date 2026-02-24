<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { mdiAccountGroup } from '@mdi/js'

const props = defineProps({
  simulasiPenyuluhan: {
    type: Object,
    required: true,
  },
})

const form = useForm({
  nama_kegiatan: props.simulasiPenyuluhan.nama_kegiatan,
  jenis_simulasi_penyuluhan: props.simulasiPenyuluhan.jenis_simulasi_penyuluhan,
  jumlah_kelompok: props.simulasiPenyuluhan.jumlah_kelompok,
  jumlah_sosialisasi: props.simulasiPenyuluhan.jumlah_sosialisasi,
  jumlah_kader_l: props.simulasiPenyuluhan.jumlah_kader_l,
  jumlah_kader_p: props.simulasiPenyuluhan.jumlah_kader_p,
  keterangan: props.simulasiPenyuluhan.keterangan ?? '',
})

const submit = () => {
  form.put(`/desa/simulasi-penyuluhan/${props.simulasiPenyuluhan.id}`)
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiAccountGroup" title="Edit Kelompok Simulasi dan Penyuluhan (Desa)" main />

    <CardBox class="max-w-4xl">
      <form class="space-y-5" @submit.prevent="submit">
        <div class="grid gap-5 md:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Kegiatan</label>
            <input v-model="form.nama_kegiatan" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.nama_kegiatan" class="mt-1 text-xs text-rose-600">{{ form.errors.nama_kegiatan }}</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Jenis Simulasi dan Penyuluhan</label>
            <input v-model="form.jenis_simulasi_penyuluhan" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.jenis_simulasi_penyuluhan" class="mt-1 text-xs text-rose-600">{{ form.errors.jenis_simulasi_penyuluhan }}</p>
          </div>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah Kelompok</label>
            <input v-model="form.jumlah_kelompok" type="number" min="0" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.jumlah_kelompok" class="mt-1 text-xs text-rose-600">{{ form.errors.jumlah_kelompok }}</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah Sosialisasi</label>
            <input v-model="form.jumlah_sosialisasi" type="number" min="0" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.jumlah_sosialisasi" class="mt-1 text-xs text-rose-600">{{ form.errors.jumlah_sosialisasi }}</p>
          </div>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah Kader L</label>
            <input v-model="form.jumlah_kader_l" type="number" min="0" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.jumlah_kader_l" class="mt-1 text-xs text-rose-600">{{ form.errors.jumlah_kader_l }}</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah Kader P</label>
            <input v-model="form.jumlah_kader_p" type="number" min="0" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.jumlah_kader_p" class="mt-1 text-xs text-rose-600">{{ form.errors.jumlah_kader_p }}</p>
          </div>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Keterangan</label>
          <textarea v-model="form.keterangan" rows="3" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"></textarea>
          <p v-if="form.errors.keterangan" class="mt-1 text-xs text-rose-600">{{ form.errors.keterangan }}</p>
        </div>

        <div class="flex items-center justify-end gap-2">
          <Link href="/desa/simulasi-penyuluhan" class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
            Batal
          </Link>
          <button type="submit" class="inline-flex rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60" :disabled="form.processing">
            Update
          </button>
        </div>
      </form>
    </CardBox>
  </SectionMain>
</template>

