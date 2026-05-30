<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import FormField from '@/admin-one/components/FormField.vue'
import FormControl from '@/admin-one/components/FormControl.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import BaseButton from '@/admin-one/components/BaseButton.vue'
import { mdiCalendarTextOutline } from '@mdi/js'
import { useForm, Link } from '@inertiajs/vue3'

const form = useForm({
  entry_date: '',
  title: '',
  person_name: '',
  institution: '',
  description: '',
  file: null,
})

const submit = () => {
  form.post('/desa/simulasi/buku-notulen', {
    onSuccess: () => form.reset(),
  })
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiCalendarTextOutline" title="Tambah Notulen Simulasi" main>
      <Link
        href="/desa/simulasi/buku-notulen"
        class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700"
      >
        Kembali ke Daftar
      </Link>
    </SectionTitleLineWithButton>

    <CardBox is-form @submit.prevent="submit">
      <FormField label="Tanggal Notulen / Rapat" :help="form.errors.entry_date">
        <FormControl v-model="form.entry_date" type="date" required />
      </FormField>

      <FormField label="Agenda / Nama Kegiatan" :help="form.errors.title">
        <FormControl v-model="form.title" required />
      </FormField>

      <FormField label="Pimpinan Rapat / Narasumber" :help="form.errors.person_name">
        <FormControl v-model="form.person_name" />
      </FormField>

      <FormField label="Tempat / Instansi" :help="form.errors.institution">
        <FormControl v-model="form.institution" />
      </FormField>

      <FormField label="Uraian / Hasil Rapat" :help="form.errors.description">
        <FormControl v-model="form.description" type="textarea" />
      </FormField>

      <FormField label="Unggah Berkas (Opsional)" :help="form.errors.file">
        <FormControl
          type="file"
          @input="form.file = $event.target.files[0]"
          accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
        />
      </FormField>

      <hr class="-mx-6 my-6 border-t border-gray-100 dark:border-slate-800">

      <div class="flex items-center justify-end gap-2">
        <BaseButton
          type="reset"
          color="info"
          outline
          label="Reset"
          @click="form.reset()"
        />
        <BaseButton
          type="submit"
          color="info"
          label="Simpan"
          :loading="form.processing"
        />
      </div>
    </CardBox>
  </SectionMain>
</template>
