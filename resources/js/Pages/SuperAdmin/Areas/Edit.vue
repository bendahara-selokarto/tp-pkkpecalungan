<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { formatScopeLabel } from '@/utils/roleLabelFormatter'
import { Link, useForm } from '@inertiajs/vue3'
import { mdiMapMarker } from '@mdi/js'

const props = defineProps({
  area: {
    type: Object,
    required: true,
  },
})

const form = useForm({
  chairperson_name: props.area.chairperson_name ?? '',
  chairperson_role: props.area.chairperson_role ?? '',
})

const submit = () => {
  form.put(`/super-admin/areas/${props.area.id}`)
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiMapMarker" title="Edit Metadata Wilayah" main />

    <CardBox class="max-w-2xl">
      <div class="mb-6 rounded-lg bg-slate-50 p-4 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800">
        <h4 class="text-sm font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Informasi Wilayah</h4>
        <div class="grid grid-cols-2 gap-4 text-sm">
          <div>
            <span class="text-slate-500">Nama:</span>
            <p class="font-medium text-slate-800 dark:text-slate-100">{{ area.name }}</p>
          </div>
          <div>
            <span class="text-slate-500">Level:</span>
            <p class="font-medium text-slate-800 dark:text-slate-100">{{ formatScopeLabel(area.level) }}</p>
          </div>
          <div v-if="area.parent">
            <span class="text-slate-500">Kecamatan:</span>
            <p class="font-medium text-slate-800 dark:text-slate-100">{{ area.parent.name }}</p>
          </div>
        </div>
      </div>

      <form class="space-y-6" @submit.prevent="submit">
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Ketua TP PKK</label>
          <input
            v-model="form.chairperson_name"
            type="text"
            class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
            placeholder="Contoh: NY. NURUL FAIZAH"
          >
          <p v-if="form.errors.chairperson_name" class="mt-1 text-xs text-rose-600">{{ form.errors.chairperson_name }}</p>
          <p class="mt-1 text-xs text-gray-500 italic">Nama ini akan muncul di footer laporan PDF.</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Jabatan Ketua TP PKK</label>
          <input
            v-model="form.chairperson_role"
            type="text"
            class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
            placeholder="Contoh: KETUA TP PKK KECAMATAN PECALUNGAN"
          >
          <p v-if="form.errors.chairperson_role" class="mt-1 text-xs text-rose-600">{{ form.errors.chairperson_role }}</p>
          <p class="mt-1 text-xs text-gray-500 italic">Jabatan ini akan muncul di atas nama ketua pada footer laporan PDF. Jika kosong, sistem akan menggunakan format standar.</p>
        </div>

        <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100 dark:border-slate-800">
          <Link
            href="/super-admin/areas"
            class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
          >
            Batal
          </Link>
          <button
            type="submit"
            class="inline-flex rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60"
            :disabled="form.processing"
          >
            Simpan Perubahan
          </button>
        </div>
      </form>
    </CardBox>
  </SectionMain>
</template>
