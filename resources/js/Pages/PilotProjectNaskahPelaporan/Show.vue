<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { formatDateForDisplay } from '@/utils/dateFormatter'
import { Link } from '@inertiajs/vue3'
import { mdiFileDocumentOutline } from '@mdi/js'
import { computed } from 'vue'

const props = defineProps({
  scopeLabel: {
    type: String,
    required: true,
  },
  scopePrefix: {
    type: String,
    required: true,
  },
  attachmentCategories: {
    type: Object,
    default: () => ({}),
  },
  report: {
    type: Object,
    required: true,
  },
})

const groupedAttachments = computed(() => {
  const base = {
    '6a_photo': [],
    '6b_photo': [],
    '6d_document': [],
    '6e_photo': [],
  }

  for (const attachment of props.report.attachments ?? []) {
    if (!base[attachment.category]) {
      continue
    }

    base[attachment.category].push(attachment)
  }

  return base
})

const formatDate = (value) => formatDateForDisplay(value)
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton
      :icon="mdiFileDocumentOutline"
      :title="`Detail Naskah Pelaporan ${scopeLabel}`"
      main
    />

    <CardBox>
      <div class="mb-5 flex items-center justify-between gap-3">
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ report.judul_laporan }}</h3>
        <Link
          :href="`${scopePrefix}/${report.id}/edit`"
          class="inline-flex rounded-md border border-amber-200 px-4 py-2 text-sm font-medium text-amber-700 hover:bg-amber-50 dark:border-amber-900/50 dark:text-amber-300 dark:hover:bg-amber-900/20"
        >
          Edit
        </Link>
      </div>

      <div class="rounded-md border border-gray-200 p-3 dark:border-slate-700">
        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Head Surat</p>
        <dl class="mt-2 grid gap-2 text-sm text-gray-900 dark:text-gray-100 md:grid-cols-2">
          <div><dt class="text-xs text-gray-500 dark:text-gray-400">Kepada</dt><dd>{{ report.surat_kepada || '-' }}</dd></div>
          <div><dt class="text-xs text-gray-500 dark:text-gray-400">Dari</dt><dd>{{ report.surat_dari || '-' }}</dd></div>
          <div><dt class="text-xs text-gray-500 dark:text-gray-400">Tembusan</dt><dd>{{ report.surat_tembusan || '-' }}</dd></div>
          <div><dt class="text-xs text-gray-500 dark:text-gray-400">Tanggal</dt><dd>{{ formatDate(report.surat_tanggal) }}</dd></div>
          <div><dt class="text-xs text-gray-500 dark:text-gray-400">Nomor</dt><dd>{{ report.surat_nomor || '-' }}</dd></div>
          <div><dt class="text-xs text-gray-500 dark:text-gray-400">Sifat</dt><dd>{{ report.surat_sifat || '-' }}</dd></div>
          <div><dt class="text-xs text-gray-500 dark:text-gray-400">Lampiran</dt><dd>{{ report.surat_lampiran || '-' }}</dd></div>
          <div><dt class="text-xs text-gray-500 dark:text-gray-400">Hal</dt><dd class="whitespace-pre-line">{{ report.surat_hal || '-' }}</dd></div>
        </dl>
      </div>

      <div class="grid gap-4 md:grid-cols-2">
        <div class="rounded-md border border-gray-200 p-3 dark:border-slate-700">
          <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">I. Dasar Pelaksanaan</p>
          <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 whitespace-pre-line">{{ report.dasar_pelaksanaan }}</p>
        </div>
        <div class="rounded-md border border-gray-200 p-3 dark:border-slate-700">
          <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">II. Pendahuluan</p>
          <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 whitespace-pre-line">{{ report.pendahuluan }}</p>
        </div>
      </div>

      <div class="mt-4 rounded-md border border-gray-200 p-3 dark:border-slate-700">
        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">IV. Pelaksanaan</p>
        <ol class="mt-2 list-decimal space-y-2 pl-5 text-sm text-gray-900 dark:text-gray-100">
          <li class="whitespace-pre-line">{{ report.pelaksanaan_1 }}</li>
          <li class="whitespace-pre-line">{{ report.pelaksanaan_2 }}</li>
          <li class="whitespace-pre-line">{{ report.pelaksanaan_3 }}</li>
          <li class="whitespace-pre-line">{{ report.pelaksanaan_4 }}</li>
          <li class="whitespace-pre-line">{{ report.pelaksanaan_5 }}</li>
        </ol>
      </div>

      <div class="mt-4 rounded-md border border-gray-200 p-3 dark:border-slate-700">
        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">V. Penutup</p>
        <p class="mt-1 whitespace-pre-line text-sm text-gray-900 dark:text-gray-100">{{ report.penutup }}</p>
      </div>

      <div class="mt-4 rounded-md border border-gray-200 p-3 dark:border-slate-700">
        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">VI. Lampiran</p>
        <div class="mt-3 grid gap-3 md:grid-cols-2">
          <div v-for="(files, category) in groupedAttachments" :key="category" class="rounded-md border border-gray-100 p-3 dark:border-slate-800">
            <p class="mb-2 text-xs font-semibold text-gray-700 dark:text-gray-300">{{ attachmentCategories[category] || category }}</p>
            <p v-if="files.length === 0" class="text-xs text-gray-500 dark:text-gray-400">Belum ada file.</p>
            <ul v-else class="space-y-1 text-xs">
              <li v-for="file in files" :key="file.id">
                <a :href="file.file_url" target="_blank" rel="noopener" class="text-sky-700 hover:underline dark:text-sky-300">{{ file.original_name }}</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </CardBox>
  </SectionMain>
</template>
