<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { mdiAccountGroup } from '@mdi/js'

const props = defineProps({
  bkbKegiatan: {
    type: Object,
    required: true,
  },
})

const form = useForm({
  jumlah_kelompok: props.bkbKegiatan.jumlah_kelompok ?? '',
  jumlah_ibu_peserta: props.bkbKegiatan.jumlah_ibu_peserta ?? '',
  jumlah_ape_set: props.bkbKegiatan.jumlah_ape_set ?? '',
  jumlah_kelompok_simulasi: props.bkbKegiatan.jumlah_kelompok_simulasi ?? '',
  keterangan: props.bkbKegiatan.keterangan ?? '',
})

const submit = () => {
  form.put(`/desa/bkb-kegiatan/${props.bkbKegiatan.id}`)
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiAccountGroup" title="Edit Data Kegiatan BKB Desa" main />

    <CardBox class="max-w-4xl">
      <form class="space-y-5" @submit.prevent="submit">
        <div class="grid gap-5 md:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah Kelompok</label>
            <input v-model="form.jumlah_kelompok" type="number" min="0" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.jumlah_kelompok" class="mt-1 text-xs text-rose-600">{{ form.errors.jumlah_kelompok }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah Ibu Peserta</label>
            <input v-model="form.jumlah_ibu_peserta" type="number" min="0" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.jumlah_ibu_peserta" class="mt-1 text-xs text-rose-600">{{ form.errors.jumlah_ibu_peserta }}</p>
          </div>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah APE (Set)</label>
            <input v-model="form.jumlah_ape_set" type="number" min="0" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.jumlah_ape_set" class="mt-1 text-xs text-rose-600">{{ form.errors.jumlah_ape_set }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah Kelompok Simulasi</label>
            <input v-model="form.jumlah_kelompok_simulasi" type="number" min="0" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.jumlah_kelompok_simulasi" class="mt-1 text-xs text-rose-600">{{ form.errors.jumlah_kelompok_simulasi }}</p>
          </div>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Keterangan</label>
          <textarea v-model="form.keterangan" rows="3" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"></textarea>
          <p v-if="form.errors.keterangan" class="mt-1 text-xs text-rose-600">{{ form.errors.keterangan }}</p>
        </div>

        <div class="flex items-center justify-end gap-2">
          <Link href="/desa/bkb-kegiatan" class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
            Batal
          </Link>
          <button type="submit" class="inline-flex rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60" :disabled="form.processing">
            Simpan Perubahan
          </button>
        </div>
      </form>
    </CardBox>
  </SectionMain>
</template>
