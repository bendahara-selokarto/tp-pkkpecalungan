<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { mdiClipboardList } from '@mdi/js'

const props = defineProps({
  dataKegiatanWarga: {
    type: Object,
    required: true,
  },
  kegiatanOptions: {
    type: Array,
    required: true,
  },
})

const form = useForm({
  kegiatan: props.dataKegiatanWarga.kegiatan,
  aktivitas: props.dataKegiatanWarga.aktivitas,
  keterangan: props.dataKegiatanWarga.keterangan ?? '',
})

const submit = () => {
  form.put(`/kecamatan/data-kegiatan-warga/${props.dataKegiatanWarga.id}`)
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiClipboardList" title="Edit Data Kegiatan Warga Kecamatan" main />

    <CardBox class="max-w-4xl">
      <form class="space-y-5" @submit.prevent="submit">
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Kegiatan</label>
          <select v-model="form.kegiatan" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <option v-for="kegiatan in props.kegiatanOptions" :key="kegiatan" :value="kegiatan">{{ kegiatan }}</option>
          </select>
          <p v-if="form.errors.kegiatan" class="mt-1 text-xs text-rose-600">{{ form.errors.kegiatan }}</p>
        </div>

        <div class="rounded-md border border-gray-200 p-3 dark:border-slate-700">
          <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
            <input v-model="form.aktivitas" type="checkbox">
            Aktivitas (centang untuk Ya, kosongkan untuk Tidak)
          </label>
          <p v-if="form.errors.aktivitas" class="mt-1 text-xs text-rose-600">{{ form.errors.aktivitas }}</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Keterangan (Jenis kegiatan yang diikuti)</label>
          <textarea v-model="form.keterangan" rows="3" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" />
          <p v-if="form.errors.keterangan" class="mt-1 text-xs text-rose-600">{{ form.errors.keterangan }}</p>
        </div>

        <div class="flex items-center justify-end gap-2">
          <Link href="/kecamatan/data-kegiatan-warga" class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
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
