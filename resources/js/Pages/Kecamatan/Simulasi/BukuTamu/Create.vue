<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import FormField from '@/admin-one/components/FormField.vue'
import FormControl from '@/admin-one/components/FormControl.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import BaseButton from '@/admin-one/components/BaseButton.vue'
import { mdiBookOpenVariant } from '@mdi/js'
import { useForm, Link } from '@inertiajs/vue3'

const form = useForm({
  visit_date: '',
  description: '',
  file: null,
})

const submit = () => {
  form.post('/kecamatan/simulasi/buku-tamu', {
    onSuccess: () => form.reset(),
  })
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiBookOpenVariant" title="Tambah Buku Tamu Simulasi" main>
      <Link
        href="/kecamatan/simulasi/buku-tamu"
        class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700"
      >
        Kembali ke Daftar
      </Link>
    </SectionTitleLineWithButton>

    <CardBox is-form @submit.prevent="submit">
      <FormField label="Tanggal Kunjungan" :help="form.errors.visit_date">
        <FormControl v-model="form.visit_date" type="date" required />
      </FormField>

      <FormField label="Keterangan" :help="form.errors.description">
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
