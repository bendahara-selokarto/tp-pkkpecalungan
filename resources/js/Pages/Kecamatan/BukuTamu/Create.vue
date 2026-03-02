<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { mdiNotebookPlusOutline } from '@mdi/js'

const form = useForm({
  visit_date: '',
  guest_name: '',
  purpose: '',
  institution: '',
  description: '',
})

const submit = () => {
  form.post('/kecamatan/buku-tamu')
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiNotebookPlusOutline" title="Tambah Buku Tamu Kecamatan" main />

    <CardBox class="max-w-3xl">
      <form class="space-y-5" @submit.prevent="submit">
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Kunjungan</label>
          <input
            v-model="form.visit_date"
            type="date"
            class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
            required
          >
          <p v-if="form.errors.visit_date" class="mt-1 text-xs text-rose-600">{{ form.errors.visit_date }}</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Tamu</label>
          <input
            v-model="form.guest_name"
            type="text"
            class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
            required
          >
          <p v-if="form.errors.guest_name" class="mt-1 text-xs text-rose-600">{{ form.errors.guest_name }}</p>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Keperluan</label>
            <input
              v-model="form.purpose"
              type="text"
              class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
              required
            >
            <p v-if="form.errors.purpose" class="mt-1 text-xs text-rose-600">{{ form.errors.purpose }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Instansi/Unit</label>
            <input
              v-model="form.institution"
              type="text"
              class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
            >
            <p v-if="form.errors.institution" class="mt-1 text-xs text-rose-600">{{ form.errors.institution }}</p>
          </div>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Keterangan</label>
          <textarea
            v-model="form.description"
            rows="4"
            class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
          />
          <p v-if="form.errors.description" class="mt-1 text-xs text-rose-600">{{ form.errors.description }}</p>
        </div>

        <div class="flex items-center justify-end gap-2">
          <Link
            href="/kecamatan/buku-tamu"
            class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
          >
            Batal
          </Link>
          <button
            type="submit"
            class="inline-flex rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60"
            :disabled="form.processing"
          >
            Simpan
          </button>
        </div>
      </form>
    </CardBox>
  </SectionMain>
</template>

