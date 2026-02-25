<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { mdiAccountGroup } from '@mdi/js'

const props = defineProps({
  dataPemanfaatanTanahPekaranganHatinyaPkk: {
    type: Object,
    required: true,
  },
  kategoriPemanfaatanLahanOptions: {
    type: Array,
    required: true,
  },
})

const form = useForm({
  kategori_pemanfaatan_lahan: props.dataPemanfaatanTanahPekaranganHatinyaPkk.kategori_pemanfaatan_lahan,
  komoditi: props.dataPemanfaatanTanahPekaranganHatinyaPkk.komoditi,
  jumlah_komoditi: props.dataPemanfaatanTanahPekaranganHatinyaPkk.jumlah_komoditi,
})

const submit = () => {
  form.put(`/desa/data-pemanfaatan-tanah-pekarangan-hatinya-pkk/${props.dataPemanfaatanTanahPekaranganHatinyaPkk.id}`)
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiAccountGroup" title="Edit Buku HATINYA PKK Desa" main />

    <CardBox class="max-w-4xl">
      <form class="space-y-5" @submit.prevent="submit">
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Kategori Jenis Pemanfaatan Lahan</label>
          <select v-model="form.kategori_pemanfaatan_lahan" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <option v-for="kategori in props.kategoriPemanfaatanLahanOptions" :key="kategori" :value="kategori">{{ kategori }}</option>
          </select>
          <p v-if="form.errors.kategori_pemanfaatan_lahan" class="mt-1 text-xs text-rose-600">{{ form.errors.kategori_pemanfaatan_lahan }}</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Komoditi Dibudidayakan</label>
          <input v-model="form.komoditi" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
          <p v-if="form.errors.komoditi" class="mt-1 text-xs text-rose-600">{{ form.errors.komoditi }}</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah Komoditi Dibudidayakan</label>
          <input v-model="form.jumlah_komoditi" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
          <p v-if="form.errors.jumlah_komoditi" class="mt-1 text-xs text-rose-600">{{ form.errors.jumlah_komoditi }}</p>
        </div>

        <div class="flex items-center justify-end gap-2">
          <Link href="/desa/data-pemanfaatan-tanah-pekarangan-hatinya-pkk" class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
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

