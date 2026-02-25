<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { mdiAccountGroup } from '@mdi/js'

const props = defineProps({
  paar: {
    type: Object,
    required: true,
  },
  indicatorOptions: {
    type: Array,
    required: true,
  },
})

const form = useForm({
  indikator: props.paar.indikator,
  jumlah: props.paar.jumlah,
  keterangan: props.paar.keterangan || '',
})

const submit = () => {
  form.put(`/desa/paar/${props.paar.id}`)
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiAccountGroup" title="Edit Buku PAAR" main />

    <CardBox class="max-w-4xl">
      <form class="space-y-5" @submit.prevent="submit">
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Indikator</label>
          <select v-model="form.indikator" disabled class="w-full rounded-md border-gray-300 bg-gray-100 text-sm shadow-sm dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
            <option v-for="option in indicatorOptions" :key="option.value" :value="option.value">
              {{ option.label }}
            </option>
          </select>
          <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Indikator terkunci mengikuti data yang dipilih.</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah</label>
          <input v-model="form.jumlah" type="number" min="0" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
          <p v-if="form.errors.jumlah" class="mt-1 text-xs text-rose-600">{{ form.errors.jumlah }}</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Keterangan</label>
          <textarea v-model="form.keterangan" rows="4" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"></textarea>
          <p v-if="form.errors.keterangan" class="mt-1 text-xs text-rose-600">{{ form.errors.keterangan }}</p>
        </div>

        <div class="flex items-center justify-end gap-2">
          <Link href="/desa/paar" class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
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
