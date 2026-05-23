<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { formatDateForDisplay } from '@/utils/dateFormatter'
import { Link } from '@inertiajs/vue3'
import { mdiCamera } from '@mdi/js'

const props = defineProps({
  fotoKegiatan: {
    type: Object,
    required: true,
  },
})

const formatDate = (value) => formatDateForDisplay(value)
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiCamera" title="Detail Foto Kegiatan" main />

    <CardBox class="max-w-4xl">
      <div class="grid gap-8 md:grid-cols-2">
        <div>
          <img v-if="fotoKegiatan.image_url" :src="fotoKegiatan.image_url" class="w-full rounded-lg shadow-md" alt="Foto Kegiatan">
          <div v-else class="flex aspect-video w-full items-center justify-center rounded-lg bg-gray-100 text-gray-400 dark:bg-slate-800">
            No Image
          </div>
        </div>

        <div class="space-y-4">
          <div>
            <h4 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Judul Kegiatan</h4>
            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ fotoKegiatan.title }}</p>
          </div>

          <div>
            <h4 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Tanggal</h4>
            <p class="text-gray-900 dark:text-gray-100">{{ formatDate(fotoKegiatan.activity_date) }}</p>
          </div>

          <div>
            <h4 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Keterangan</h4>
            <p class="whitespace-pre-wrap text-gray-900 dark:text-gray-100">{{ fotoKegiatan.description || '-' }}</p>
          </div>

          <div class="flex items-center gap-2 pt-4">
            <Link
              href="/kecamatan/foto-kegiatan"
              class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
            >
              Kembali
            </Link>
            <Link
              :href="`/kecamatan/foto-kegiatan/${fotoKegiatan.id}/edit`"
              class="inline-flex rounded-md bg-amber-600 px-4 py-2 text-sm font-medium text-white hover:bg-amber-700"
            >
              Edit
            </Link>
          </div>
        </div>
      </div>
    </CardBox>
  </SectionMain>
</template>
