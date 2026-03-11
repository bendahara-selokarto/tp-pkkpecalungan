<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { mdiAccountGroup } from '@mdi/js'

const props = defineProps({
  pelatihanKader: {
    type: Object,
    required: true,
  },
})

const kategoriOptions = [
  { value: 'lp3', label: 'LP3 PKK' },
  { value: 'tpk_3_pkk', label: 'TPK 3 PKK' },
  { value: 'damas_pkk', label: 'Damas PKK' },
]

const form = useForm({
  kategori_pelatihan: props.pelatihanKader.kategori_pelatihan ?? kategoriOptions[0]?.value ?? '',
  jumlah_kader: props.pelatihanKader.jumlah_kader ?? '',
  keterangan: props.pelatihanKader.keterangan ?? '',
})

const submit = () => {
  form.put(`/kecamatan/pelatihan-kader-pokja-ii/${props.pelatihanKader.id}`)
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiAccountGroup" title="Edit Pelatihan Kader Pokja II Kecamatan" main />

    <CardBox class="max-w-3xl">
      <form class="space-y-5" @submit.prevent="submit">
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Kategori Pelatihan</label>
          <select v-model="form.kategori_pelatihan" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <option v-for="option in kategoriOptions" :key="option.value" :value="option.value">
              {{ option.label }}
            </option>
          </select>
          <p v-if="form.errors.kategori_pelatihan" class="mt-1 text-xs text-rose-600">{{ form.errors.kategori_pelatihan }}</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah Kader</label>
          <input v-model="form.jumlah_kader" type="number" min="0" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
          <p v-if="form.errors.jumlah_kader" class="mt-1 text-xs text-rose-600">{{ form.errors.jumlah_kader }}</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Keterangan</label>
          <textarea v-model="form.keterangan" rows="3" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"></textarea>
          <p v-if="form.errors.keterangan" class="mt-1 text-xs text-rose-600">{{ form.errors.keterangan }}</p>
        </div>

        <div class="flex items-center justify-end gap-2">
          <Link href="/kecamatan/pelatihan-kader-pokja-ii" class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
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
