<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import FormField from '@/admin-one/components/FormField.vue'
import FormControl from '@/admin-one/components/FormControl.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import BaseButton from '@/admin-one/components/BaseButton.vue'
import BaseIcon from '@/admin-one/components/BaseIcon.vue'
import { mdiBookOpenVariant, mdiFileDownloadOutline } from '@mdi/js'
import { useForm, Link } from '@inertiajs/vue3'

const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
})

const form = useForm({
  _method: 'PUT',
  visit_date: props.item.visit_date,
  description: props.item.description,
  file: null,
})

const submit = () => {
  form.post(`/kecamatan/simulasi/buku-tamu/${props.item.id}`)
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiBookOpenVariant" title="Edit Buku Tamu Simulasi" main>
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

      <FormField label="Ganti Berkas (Opsional)" :help="form.errors.file">
        <div class="space-y-2">
          <FormControl
            type="file"
            @input="form.file = $event.target.files[0]"
            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
          />
          <div v-if="item.file_url" class="text-sm text-gray-500 flex items-center gap-1">
            <span>Berkas saat ini:</span>
            <a :href="item.file_url" target="_blank" class="text-emerald-600 hover:underline inline-flex items-center gap-1">
              <BaseIcon :path="mdiFileDownloadOutline" size="14" />
              Lihat Berkas
            </a>
          </div>
        </div>
      </FormField>

      <hr class="-mx-6 my-6 border-t border-gray-100 dark:border-slate-800">

      <div class="flex items-center justify-end gap-2">
        <Link
          href="/kecamatan/simulasi/buku-tamu"
          class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
        >
          Batal
        </Link>
        <BaseButton
          type="submit"
          color="info"
          label="Update"
          :loading="form.processing"
        />
      </div>
    </CardBox>
  </SectionMain>
</template>
