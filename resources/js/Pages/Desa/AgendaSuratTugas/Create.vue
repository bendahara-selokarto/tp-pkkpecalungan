<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import FormField from '@/admin-one/components/FormField.vue'
import FormControl from '@/admin-one/components/FormControl.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import BaseButton from '@/admin-one/components/BaseButton.vue'
import BaseButtons from '@/admin-one/components/BaseButtons.vue'
import { mdiCardAccountDetailsOutline } from '@mdi/js'
import { useForm, Link } from '@inertiajs/vue3'

const form = useForm({
  nomor_surat: '',
  tanggal_surat: '',
  kepada: '',
  perihal: '',
  lampiran: '',
  tembusan: '',
  file: null,
})

const submit = () => {
  form.post('/desa/agenda-surat-tugas', {
    onSuccess: () => form.reset(),
  })
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiCardAccountDetailsOutline" title="Tambah Agenda Surat Tugas" main>
      <Link
        href="/desa/agenda-surat-tugas"
        class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700"
      >
        Kembali ke Daftar
      </Link>
    </SectionTitleLineWithButton>

    <CardBox is-form @submit.prevent="submit">
      <FormField label="Nomor dan Kode Surat" :help="form.errors.nomor_surat">
        <FormControl v-model="form.nomor_surat" placeholder="Contoh: 01/ST/PKK/V/2026" required />
      </FormField>

      <FormField label="Tanggal Surat" :help="form.errors.tanggal_surat">
        <FormControl v-model="form.tanggal_surat" type="date" required />
      </FormField>

      <FormField label="Kepada" :help="form.errors.kepada">
        <FormControl v-model="form.kepada" placeholder="Nama penerima tugas" required />
      </FormField>

      <FormField label="Perihal" :help="form.errors.perihal">
        <FormControl v-model="form.perihal" type="textarea" placeholder="Isi perihal tugas" required />
      </FormField>

      <FormField label="Lampiran (Teks)" :help="form.errors.lampiran">
        <FormControl v-model="form.lampiran" placeholder="Contoh: 1 (satu) berkas" />
      </FormField>

      <FormField label="Tembusan" :help="form.errors.tembusan">
        <FormControl v-model="form.tembusan" type="textarea" placeholder="Daftar tembusan (jika ada)" />
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
