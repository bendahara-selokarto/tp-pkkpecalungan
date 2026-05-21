<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import FormField from '@/admin-one/components/FormField.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { mdiNotebookEditOutline } from '@mdi/js'

const form = useForm({
  title: '',
  file: null,
})

const onFileChange = (event) => {
  form.file = event.target.files?.[0] ?? null
}

const submit = () => {
  form.post('/desa/buku-ekspedisi', {
    preserveScroll: true,
  })
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiNotebookEditOutline" title="Tambah Buku Ekspedisi" main />

    <CardBox is-form @submit.prevent="submit">
      <FormField label="Judul/Keterangan" :error="form.errors.title" help="Contoh: Ekspedisi Surat Keluar Bulan Mei 2026">
        <input
          v-model="form.title"
          type="text"
          class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
          required
        >
      </FormField>

      <FormField label="Berkas Scan (PDF/Gambar)" :error="form.errors.file" help="Format: pdf, jpg, jpeg, png. Maks: 10MB">
        <input
          type="file"
          accept=".pdf,.jpg,.jpeg,.png"
          class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
          required
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
            {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
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
