<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { formatDateForDisplay } from '@/utils/dateFormatter'
import { Link } from '@inertiajs/vue3'
import { mdiClipboardList } from '@mdi/js'

const props = defineProps({
  activity: {
    type: Object,
    required: true,
  },
  can: {
    type: Object,
    default: () => ({
      print: false,
    }),
  },
  routes: {
    type: Object,
    default: () => ({
      print: null,
    }),
  },
})

const formatDate = (value) => formatDateForDisplay(value)
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiClipboardList" title="Detail Kegiatan Desa" main />

    <CardBox class="max-w-3xl space-y-4">
      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Nama Bertugas</p>
        <p class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ props.activity.nama_petugas || props.activity.title }}</p>
      </div>

      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Desa</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.activity.area?.name || '-' }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Dibuat Oleh</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.activity.creator?.name || '-' }}</p>
        </div>
      </div>

      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Tanggal</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ formatDate(props.activity.activity_date) }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Jabatan</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.activity.jabatan_petugas || '-' }}</p>
        </div>
      </div>

      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Tempat</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.activity.tempat_kegiatan || '-' }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Status</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.activity.status }}</p>
        </div>
      </div>

      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Uraian</p>
        <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.activity.uraian || props.activity.description || '-' }}</p>
      </div>

      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Tanda Tangan</p>
        <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.activity.tanda_tangan || props.activity.nama_petugas || '-' }}</p>
      </div>

      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Gambar</p>
          <a
            v-if="props.activity.image_url"
            :href="props.activity.image_url"
            target="_blank"
            rel="noopener"
            class="text-sm font-medium text-sky-600 hover:underline dark:text-sky-400"
          >
            Lihat gambar
          </a>
          <p v-else class="text-sm text-gray-700 dark:text-gray-300">-</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Berkas</p>
          <a
            v-if="props.activity.document_url"
            :href="props.activity.document_url"
            target="_blank"
            rel="noopener"
            class="text-sm font-medium text-sky-600 hover:underline dark:text-sky-400"
          >
            Lihat berkas
          </a>
          <p v-else class="text-sm text-gray-700 dark:text-gray-300">-</p>
        </div>
      </div>

      <div class="flex items-center justify-end gap-2">
        <Link href="/kecamatan/desa-activities" class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
          Kembali
        </Link>
        <a
          v-if="props.can.print && props.routes.print"
          :href="props.routes.print"
          target="_blank"
          class="inline-flex rounded-md bg-sky-600 px-4 py-2 text-sm font-medium text-white hover:bg-sky-700"
        >
          Cetak PDF
        </a>
      </div>
    </CardBox>
  </SectionMain>
</template>
