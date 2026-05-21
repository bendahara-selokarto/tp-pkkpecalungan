<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import FormField from '@/admin-one/components/FormField.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { mdiNotebookEditOutline } from '@mdi/js'

const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
})

const form = useForm({
  title: props.item.title,
  file: null,
  _method: 'PUT',
})

const onFileChange = (event) => {
  form.file = event.target.files?.[0] ?? null
}

const submit = () => {
  // Use POST with _method: 'PUT' for file uploads in Laravel
  form.post(`/desa/buku-ekspedisi/${props.item.id}`, {
    preserveScroll: true,
  })
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiNotebookEditOutline" title="Edit Buku Ekspedisi" main />

    <CardBox is-form @submit.prevent="submit">
      <FormField label="Judul/Keterangan" :error="form.errors.title" help="Contoh: Ekspedisi Surat Keluar Bulan Mei 2026">
        <input
          v-model="form.title"
          type="text"
          class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
          required
        >
      </FormField>

      <div class="mb-4">
        <p class="text-xs font-medium text-gray-600 dark:text-gray-400">File saat ini:</p>
        <div class="flex items-center gap-2 mt-1">
          <span class="text-sm text-gray-900 dark:text-gray-100">{{ item.original_name }}</span>
          <a
            :href="`/desa/buku-ekspedisi/${item.id}/download`"
            class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 dark:text-emerald-400"
          >
            Unduh
          </a>
        </div>
      </div>

      <FormField label="Ganti Berkas (Opsional)" :error="form.errors.file" help="Format: pdf, jpg, jpeg, png. Maks: 10MB. Kosongkan jika tidak ingin mengganti.">
        <input
          type="file"
          accept=".pdf,.jpg,.jpeg,.png"
          class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
          @change="onFileChange"
        >
      </FormField>

      <template #footer>
        <div class="flex items-center gap-4">
          <button
            type="submit"
            class="inline-flex min-h-[44px] items-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 disabled:opacity-50"
            :disabled="form.processing"
          >
            {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
          </button>
          <Link
            href="/desa/buku-ekspedisi"
            class="inline-flex min-h-[44px] items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
          >
            Batal
          </Link>
        </div>
      </template>
    </CardBox>
  </SectionMain>
</template>
