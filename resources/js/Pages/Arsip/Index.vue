<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { mdiArchive } from '@mdi/js'

defineProps({
  documents: {
    type: Array,
    default: () => [],
  },
})

const formatBytes = (sizeInBytes) => {
  const size = Number(sizeInBytes)
  if (!Number.isFinite(size) || size < 1024) {
    return `${Math.max(0, Math.round(size))} B`
  }

  const kiloBytes = size / 1024
  if (kiloBytes < 1024) {
    return `${kiloBytes.toFixed(1)} KB`
  }

  const megaBytes = kiloBytes / 1024
  return `${megaBytes.toFixed(1)} MB`
}

const formatDateTime = (isoDateTime) => {
  if (!isoDateTime) {
    return '-'
  }

  const parsedDate = new Date(isoDateTime)
  if (Number.isNaN(parsedDate.getTime())) {
    return '-'
  }

  return new Intl.DateTimeFormat('id-ID', {
    dateStyle: 'medium',
    timeStyle: 'short',
  }).format(parsedDate)
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiArchive" title="Arsip" main />

    <CardBox>
      <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Dokumen Referensi</h3>
      <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
        Gunakan arsip ini untuk mengunduh dokumen statis referensi yang dibutuhkan saat bekerja.
      </p>

      <div class="mt-5 overflow-x-auto">
        <table class="w-full min-w-[760px] text-sm">
          <thead class="border-b border-gray-200 dark:border-slate-700">
            <tr class="text-left text-gray-600 dark:text-gray-300">
              <th class="px-3 py-3 font-semibold">Nama Dokumen</th>
              <th class="px-3 py-3 font-semibold">Jenis</th>
              <th class="px-3 py-3 font-semibold">Ukuran</th>
              <th class="px-3 py-3 font-semibold">Pembaruan Terakhir</th>
              <th class="w-36 px-3 py-3 font-semibold">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="document in documents"
              :key="document.name"
              class="border-b border-gray-100 align-top dark:border-slate-800"
            >
              <td class="px-3 py-3 text-gray-900 dark:text-gray-100">{{ document.name }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ document.extension }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ formatBytes(document.size_bytes) }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ formatDateTime(document.updated_at) }}</td>
              <td class="px-3 py-3">
                <a
                  :href="document.download_url"
                  class="inline-flex items-center rounded-md border border-emerald-200 px-3 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-50 dark:border-emerald-900/50 dark:text-emerald-300 dark:hover:bg-emerald-900/20"
                >
                  Unduh
                </a>
              </td>
            </tr>
            <tr v-if="documents.length === 0">
              <td colspan="5" class="px-3 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                Belum ada dokumen arsip yang tersedia.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </CardBox>
  </SectionMain>
</template>
