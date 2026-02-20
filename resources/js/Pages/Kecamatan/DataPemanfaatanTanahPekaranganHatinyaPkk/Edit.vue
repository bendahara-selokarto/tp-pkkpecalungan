<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { mdiAccountGroup } from '@mdi/js'

const props = defineProps({
  DataPemanfaatanTanahPekaranganHatinyaPkk: {
    type: Object,
    required: true,
  },
  kategoriPemanfaatanOptions: {
    type: Array,
    required: true,
  },
})

const form = useForm({
  kategori_pemanfaatan: props.DataPemanfaatanTanahPekaranganHatinyaPkk.kategori_pemanfaatan,
  jumlah_kk_memanfaatkan: props.DataPemanfaatanTanahPekaranganHatinyaPkk.jumlah_kk_memanfaatkan,
  keterangan: props.DataPemanfaatanTanahPekaranganHatinyaPkk.keterangan ?? '',
})

const submit = () => {
  form.put(`/kecamatan/data-pemanfaatan-tanah-pekarangan-hatinya-pkk/${props.DataPemanfaatanTanahPekaranganHatinyaPkk.id}`)
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiAccountGroup" title="Edit Data Pemanfaatan Tanah Pekarangan/HATINYA PKK Kecamatan" main />

    <CardBox class="max-w-4xl">
      <form class="space-y-5" @submit.prevent="submit">
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Kategori Keluarga</label>
          <select v-model="form.kategori_pemanfaatan" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <option v-for="kategori in props.kategoriPemanfaatanOptions" :key="kategori" :value="kategori">{{ kategori }}</option>
          </select>
          <p v-if="form.errors.kategori_pemanfaatan" class="mt-1 text-xs text-rose-600">{{ form.errors.kategori_pemanfaatan }}</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah Keluarga</label>
          <input v-model="form.jumlah_kk_memanfaatkan" type="number" min="0" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
          <p v-if="form.errors.jumlah_kk_memanfaatkan" class="mt-1 text-xs text-rose-600">{{ form.errors.jumlah_kk_memanfaatkan }}</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Keterangan</label>
          <textarea v-model="form.keterangan" rows="3" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" />
          <p v-if="form.errors.keterangan" class="mt-1 text-xs text-rose-600">{{ form.errors.keterangan }}</p>
        </div>

        <div class="flex items-center justify-end gap-2">
          <Link href="/kecamatan/data-pemanfaatan-tanah-pekarangan-hatinya-pkk" class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
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


