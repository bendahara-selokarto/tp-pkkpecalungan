<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import BaseButton from '@/admin-one/components/BaseButton.vue'
import BaseButtons from '@/admin-one/components/BaseButtons.vue'
import BaseIcon from '@/admin-one/components/BaseIcon.vue'
import { mdiCardAccountDetailsOutline, mdiPencil, mdiFileDownloadOutline } from '@mdi/js'
import { Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
})

const page = usePage()
const moduleMode = computed(() => page.props.auth?.user?.moduleModes?.['agenda-surat-tugas'])
const canUpdate = computed(() => moduleMode.value === 'read-write')

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
    <SectionTitleLineWithButton :icon="mdiCardAccountDetailsOutline" title="Detail Agenda Surat Tugas" main>
      <Link
        href="/kecamatan/agenda-surat-tugas"
        class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700"
      >
        Kembali ke Daftar
      </Link>
    </SectionTitleLineWithButton>

    <CardBox>
      <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Nomor dan Kode Surat</h4>
            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ item.nomor_surat }}</p>
          </div>
          <div>
            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Tanggal Surat</h4>
            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ formatDate(item.tanggal_surat) }}</p>
          </div>
        </div>

        <div>
          <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Kepada</h4>
          <p class="text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ item.kepada }}</p>
        </div>

        <div>
          <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Perihal</h4>
          <p class="text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ item.perihal }}</p>
        </div>

        <div>
          <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Lampiran</h4>
          <p class="text-gray-900 dark:text-gray-100">{{ item.lampiran || '-' }}</p>
        </div>

        <div>
          <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Tembusan</h4>
          <p class="text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ item.tembusan || '-' }}</p>
        </div>

        <div v-if="item.file_url">
          <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Berkas Lampiran</h4>
          <a
            :href="item.file_url"
            target="_blank"
            class="inline-flex items-center gap-2 text-emerald-600 hover:underline font-medium dark:text-emerald-400"
          >
            <BaseIcon :path="mdiFileDownloadOutline" size="20" />
            Unduh Berkas
          </a>
        </div>

        <div class="pt-4 border-t border-gray-100 dark:border-slate-800 flex justify-between items-center">
          <div class="text-xs text-gray-500">
            Tahun Anggaran: {{ item.tahun_anggaran }}
          </div>
          <BaseButtons>
            <BaseButton
              v-if="canUpdate"
              color="warning"
              :icon="mdiPencil"
              label="Edit Data"
              :href="`/kecamatan/agenda-surat-tugas/${item.id}/edit`"
            />
          </BaseButtons>
        </div>
      </div>
    </CardBox>
  </SectionMain>
</template>
