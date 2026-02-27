<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { formatDateForDisplay } from '@/utils/dateFormatter'
import { Link } from '@inertiajs/vue3'
import { mdiClipboardList } from '@mdi/js'
import { computed } from 'vue'

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

const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg']

const resolveExtension = (value) => {
  if (!value || typeof value !== 'string') {
    return ''
  }

  const sanitized = value.split('?')[0].split('#')[0]
  const parts = sanitized.split('.')

  if (parts.length < 2) {
    return ''
  }

  return String(parts.pop() || '').toLowerCase()
}

const documentExtension = computed(() => {
  return resolveExtension(props.activity.document_path || props.activity.document_url || '')
})

const isDocumentPdf = computed(() => documentExtension.value === 'pdf')
const isDocumentImage = computed(() => imageExtensions.includes(documentExtension.value))
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiClipboardList" title="Detail Buku Kegiatan Desa" main />

    <CardBox class="max-w-3xl space-y-4">
      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Nama Bertugas</p>
        <p class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ props.activity.nama_petugas || props.activity.title }}</p>
      </div>

      <div class="grid gap-4 md:grid-cols-3">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Tanggal</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ formatDate(props.activity.activity_date) }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Jabatan</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.activity.jabatan_petugas || '-' }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Tempat</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.activity.tempat_kegiatan || '-' }}</p>
        </div>
      </div>

      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Uraian</p>
        <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.activity.uraian || props.activity.description || '-' }}</p>
      </div>

      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Status</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.activity.status }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Tanda Tangan</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.activity.tanda_tangan || props.activity.nama_petugas || '-' }}</p>
        </div>
      </div>

      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Gambar</p>
          <div v-if="props.activity.image_url" class="space-y-2">
            <img
              :src="props.activity.image_url"
              alt="Foto kegiatan"
              class="h-48 w-full rounded-md border border-gray-200 object-cover dark:border-slate-700"
            >
            <a
              :href="props.activity.image_url"
              target="_blank"
              rel="noopener"
              class="text-sm font-medium text-sky-600 hover:underline dark:text-sky-400"
            >
              Buka gambar penuh
            </a>
          </div>
          <p v-else class="text-sm text-gray-700 dark:text-gray-300">Belum ada gambar.</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Berkas</p>
          <div v-if="props.activity.document_url" class="space-y-2">
            <iframe
              v-if="isDocumentPdf"
              :src="props.activity.document_url"
              class="h-48 w-full rounded-md border border-gray-200 dark:border-slate-700"
              title="Preview berkas kegiatan"
            />
            <img
              v-else-if="isDocumentImage"
              :src="props.activity.document_url"
              alt="Berkas kegiatan"
              class="h-48 w-full rounded-md border border-gray-200 object-cover dark:border-slate-700"
            >
            <p v-else class="text-sm text-gray-700 dark:text-gray-300">Preview belum tersedia untuk format berkas ini.</p>
            <a
              :href="props.activity.document_url"
              target="_blank"
              rel="noopener"
              class="text-sm font-medium text-sky-600 hover:underline dark:text-sky-400"
            >
              Buka berkas
            </a>
          </div>
          <p v-else class="text-sm text-gray-700 dark:text-gray-300">Belum ada berkas.</p>
        </div>
      </div>

      <div class="flex items-center justify-end gap-2">
        <Link href="/desa/activities" class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
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
        <Link :href="`/desa/activities/${props.activity.id}/edit`" class="inline-flex rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
          Edit
        </Link>
      </div>
    </CardBox>
  </SectionMain>
</template>
