<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { mdiArchiveEdit } from '@mdi/js'

const props = defineProps({
  document: {
    type: Object,
    required: true,
  },
})

const form = useForm({
  title: props.document.title ?? '',
  description: props.document.description ?? '',
  is_published: !!props.document.is_published,
  document_file: null,
})

const onFileChange = (event) => {
  form.document_file = event.target.files?.[0] ?? null
}

const submit = () => {
  form
    .transform((data) => ({
      ...data,
      _method: 'put',
    }))
    .post(`/super-admin/arsip/${props.document.id}`)
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiArchiveEdit" title="Edit Dokumen Arsip" main />

    <CardBox class="max-w-3xl">
      <form class="space-y-5" @submit.prevent="submit">
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Judul</label>
          <input
            v-model="form.title"
            type="text"
            class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
            required
          >
          <p v-if="form.errors.title" class="mt-1 text-xs text-rose-600">{{ form.errors.title }}</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi (opsional)</label>
          <textarea
            v-model="form.description"
            rows="3"
            class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
          />
          <p v-if="form.errors.description" class="mt-1 text-xs text-rose-600">{{ form.errors.description }}</p>
        </div>

        <div class="rounded-md border border-dashed border-gray-300 bg-gray-50 px-3 py-2 text-xs text-gray-600 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-300">
          File saat ini: {{ document.original_name }}
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Ganti File (opsional)</label>
          <input
            type="file"
            accept=".pdf,.doc,.docx,.xls,.xlsx"
            class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 file:mr-3 file:rounded file:border-0 file:bg-emerald-100 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-emerald-700 hover:file:bg-emerald-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
            @change="onFileChange"
          >
          <p v-if="form.errors.document_file" class="mt-1 text-xs text-rose-600">{{ form.errors.document_file }}</p>
        </div>

        <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
          <input v-model="form.is_published" type="checkbox" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
          <span>Publikasikan dokumen ini untuk pengguna umum</span>
        </label>

        <div class="flex items-center justify-end gap-2">
          <Link
            href="/super-admin/arsip"
            class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
          >
            Batal
          </Link>
          <button
            type="submit"
            class="inline-flex rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60"
            :disabled="form.processing"
          >
            Update
          </button>
        </div>
      </form>
    </CardBox>
  </SectionMain>
</template>
