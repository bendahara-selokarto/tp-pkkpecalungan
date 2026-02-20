<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { mdiTrophy } from '@mdi/js'

const props = defineProps({
  prestasiLomba: {
    type: Object,
    required: true,
  },
})

const form = useForm({
  tahun: props.prestasiLomba.tahun,
  jenis_lomba: props.prestasiLomba.jenis_lomba,
  lokasi: props.prestasiLomba.lokasi,
  prestasi_kecamatan: props.prestasiLomba.prestasi_kecamatan,
  prestasi_kabupaten: props.prestasiLomba.prestasi_kabupaten,
  prestasi_provinsi: props.prestasiLomba.prestasi_provinsi,
  prestasi_nasional: props.prestasiLomba.prestasi_nasional,
  keterangan: props.prestasiLomba.keterangan ?? '',
})

const submit = () => {
  form.put(`/kecamatan/prestasi-lomba/${props.prestasiLomba.id}`)
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiTrophy" title="Edit Prestasi Lomba Kecamatan" main />

    <CardBox class="max-w-4xl">
      <form class="space-y-5" @submit.prevent="submit">
        <div class="grid gap-5 md:grid-cols-3">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Tahun</label>
            <input v-model="form.tahun" type="number" min="1900" max="2100" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.tahun" class="mt-1 text-xs text-rose-600">{{ form.errors.tahun }}</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Jenis Lomba</label>
            <input v-model="form.jenis_lomba" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.jenis_lomba" class="mt-1 text-xs text-rose-600">{{ form.errors.jenis_lomba }}</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Lokasi</label>
            <input v-model="form.lokasi" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.lokasi" class="mt-1 text-xs text-rose-600">{{ form.errors.lokasi }}</p>
          </div>
        </div>

        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Prestasi/Keberhasilan yang Dicapai</label>
          <div class="grid gap-2 md:grid-cols-4">
            <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300"><input v-model="form.prestasi_kecamatan" type="checkbox"> Kecamatan</label>
            <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300"><input v-model="form.prestasi_kabupaten" type="checkbox"> Kabupaten</label>
            <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300"><input v-model="form.prestasi_provinsi" type="checkbox"> Provinsi</label>
            <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300"><input v-model="form.prestasi_nasional" type="checkbox"> Nasional</label>
          </div>
          <p v-if="form.errors.prestasi_kecamatan" class="mt-1 text-xs text-rose-600">{{ form.errors.prestasi_kecamatan }}</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Keterangan</label>
          <textarea v-model="form.keterangan" rows="3" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"></textarea>
          <p v-if="form.errors.keterangan" class="mt-1 text-xs text-rose-600">{{ form.errors.keterangan }}</p>
        </div>

        <div class="flex items-center justify-end gap-2">
          <Link href="/kecamatan/prestasi-lomba" class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
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

