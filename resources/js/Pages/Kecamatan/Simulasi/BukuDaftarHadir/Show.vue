<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import FormField from '@/admin-one/components/FormField.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import BaseIcon from '@/admin-one/components/BaseIcon.vue'
import { mdiAccountCheckOutline, mdiFileDownloadOutline } from '@mdi/js'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
})

const formatDate = (dateString) => {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'long',
    year: 'numeric',
  })
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiAccountCheckOutline" title="Detail Daftar Hadir Simulasi" main>
      <Link
        href="/kecamatan/simulasi/buku-daftar-hadir"
        class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700"
      >
        Kembali ke Daftar
      </Link>
    </SectionTitleLineWithButton>

    <CardBox>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <FormField label="Tanggal Kegiatan">
          <div class="font-medium">{{ formatDate(item.attendance_date) }}</div>
        </FormField>

        <FormField label="Nama Acara / Kegiatan">
          <div class="font-medium">{{ item.title }}</div>
        </FormField>

        <FormField label="Nama Peserta">
          <div class="font-medium">{{ item.attendee_name }}</div>
        </FormField>

        <FormField label="Instansi">
          <div class="font-medium">{{ item.institution || '-' }}</div>
        </FormField>

        <FormField label="Keterangan" class="md:col-span-2">
          <div class="p-3 bg-gray-50 rounded-md dark:bg-slate-800 whitespace-pre-wrap">{{ item.description || '-' }}</div>
        </FormField>

        <FormField v-if="item.file_url" label="Lampiran Berkas" class="md:col-span-2">
          <a
            :href="item.file_url"
            target="_blank"
            class="inline-flex items-center gap-2 text-emerald-600 hover:underline"
          >
            <BaseIcon :path="mdiFileDownloadOutline" size="20" />
            {{ item.original_name || 'Unduh Berkas' }}
          </a>
        </FormField>
      </div>

      <hr class="-mx-6 my-6 border-t border-gray-100 dark:border-slate-800">

      <div class="flex items-center justify-end gap-2">
        <Link
          :href="`/kecamatan/simulasi/buku-daftar-hadir/${item.id}/edit`"
          class="inline-flex items-center rounded-md bg-warning-600 px-4 py-2 text-sm font-medium text-white hover:bg-warning-700 bg-yellow-600 hover:bg-yellow-700"
        >
          Edit Data
        </Link>
      </div>
    </CardBox>
  </SectionMain>
</template>
