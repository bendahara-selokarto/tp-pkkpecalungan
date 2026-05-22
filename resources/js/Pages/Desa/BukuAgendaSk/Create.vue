<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import FormField from '@/admin-one/components/FormField.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import BaseButton from '@/admin-one/components/BaseButton.vue'
import BaseButtons from '@/admin-one/components/BaseButtons.vue'
import { mdiNotebookPlusOutline } from '@mdi/js'
import { Link, useForm } from '@inertiajs/vue3'

const form = useForm({
  nomor_sk: '',
  tanggal_sk: '',
  kepada: '',
  perihal: '',
  tembusan: '',
  file: null,
})

const onFileChange = (event) => {
  form.file = event.target.files?.[0] ?? null
}

const submit = () => {
  form.post('/desa/buku-agenda-sk', {
    preserveScroll: true,
  })
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiNotebookPlusOutline" title="Tambah Agenda SK" main />

    <CardBox is-form @submit.prevent="submit">
      <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <FormField label="Nomor SK" :error="form.errors.nomor_sk">
          <input
            v-model="form.nomor_sk"
            type="text"
            placeholder="Contoh: 01/SK/PKK/2026"
            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
            required
          >
        </FormField>

        <FormField label="Tanggal SK" :error="form.errors.tanggal_sk">
          <input
            v-model="form.tanggal_sk"
            type="date"
            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
            required
          >
        </FormField>
      </div>

      <FormField label="Kepada" :error="form.errors.kepada">
        <input
          v-model="form.kepada"
          type="text"
          placeholder="Nama atau Jabatan Penerima"
          class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
          required
        >
      </FormField>

      <FormField label="Perihal / Tentang" :error="form.errors.perihal">
        <textarea
          v-model="form.perihal"
          rows="3"
          placeholder="Isi singkat perihal SK"
          class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
          required
        />
      </FormField>

      <FormField label="Tembusan" :error="form.errors.tembusan" help="Gunakan baris baru untuk memisahkan tembusan">
        <textarea
          v-model="form.tembusan"
          rows="3"
          placeholder="Contoh:&#10;1. Kepala Desa&#10;2. Camat"
          class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
        />
      </FormField>

      <FormField label="Unggah Berkas SK (PDF/Gambar)" :error="form.errors.file" help="Format: pdf, jpg, jpeg, png. Maks: 10MB">
        <input
          type="file"
          accept=".pdf,.jpg,.jpeg,.png"
          class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
          required
          @change="onFileChange"
        >
      </FormField>

      <template #footer>
        <div class="flex items-center justify-end gap-3">
          <BaseButtons>
            <BaseButton
              type="submit"
              color="emerald"
              label="Simpan"
              :loading="form.processing"
            />
          </BaseButtons>
          <Link
            :href="route('desa.buku-agenda-sk.index')"
            class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
          >
            Batal
          </Link>
        </div>
      </template>
    </CardBox>
  </SectionMain>
</template>
