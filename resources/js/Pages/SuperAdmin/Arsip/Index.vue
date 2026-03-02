<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import ConfirmActionModal from '@/admin-one/components/ConfirmActionModal.vue'
import PaginationBar from '@/admin-one/components/PaginationBar.vue'
import ResponsiveDataTable from '@/admin-one/components/ResponsiveDataTable.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link, router } from '@inertiajs/vue3'
import { mdiArchiveEdit } from '@mdi/js'
import { computed, ref } from 'vue'

const props = defineProps({
  documents: {
    type: Object,
    required: true,
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
  pagination: {
    type: Object,
    default: () => ({
      perPageOptions: [10, 25, 50],
    }),
  },
})

const isDeleteModalActive = ref(false)
const deletingId = ref(null)
const isResponsiveTableV2Enabled = computed(() => import.meta.env.VITE_UI_RESPONSIVE_TABLE_V2 !== 'false')
const perPage = computed(() => props.filters.per_page ?? 10)
const arsipManagementTableColumns = [
  { key: 'title', label: 'Judul', mobileLabel: 'Judul Dokumen' },
  { key: 'file', label: 'File', mobileLabel: 'Nama File' },
  { key: 'type', label: 'Tipe', mobileLabel: 'Tipe Arsip' },
  { key: 'creator_name', label: 'Pengunggah', mobileLabel: 'Pengunggah' },
  { key: 'size', label: 'Ukuran', mobileLabel: 'Ukuran File' },
  { key: 'download_count', label: 'Diunduh', mobileLabel: 'Jumlah Unduh' },
  { key: 'updated_at', label: 'Diperbarui', mobileLabel: 'Terakhir Diperbarui' },
  { key: 'actions', label: 'Aksi', mobileLabel: 'Aksi', headerClass: 'w-44' },
]

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

const openDeleteModal = (id) => {
  deletingId.value = id
  isDeleteModalActive.value = true
}

const closeDeleteModal = () => {
  isDeleteModalActive.value = false
  deletingId.value = null
}

const confirmDelete = () => {
  if (deletingId.value === null) {
    return
  }

  router.delete(`/super-admin/arsip/${deletingId.value}`, {
    onFinish: closeDeleteModal,
  })
}

const updatePerPage = (event) => {
  const selectedPerPage = Number(event.target.value)

  router.get('/super-admin/arsip', {
    ...props.filters,
    page: 1,
    per_page: selectedPerPage,
  }, {
    preserveScroll: true,
    preserveState: true,
    replace: true,
  })
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiArchiveEdit" title="Management Arsip" main />

    <CardBox>
      <div class="mb-4 flex flex-wrap items-center justify-between gap-4">
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Daftar Dokumen Arsip</h3>
        <div class="flex flex-wrap items-center gap-2">
          <label class="inline-flex min-h-[44px] items-center gap-2 text-xs text-gray-600 dark:text-gray-300">
            Per halaman
            <select
              :value="perPage"
              class="min-h-[44px] rounded-md border border-gray-300 px-3 py-2 text-xs dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
              @change="updatePerPage"
            >
              <option v-for="option in pagination.perPageOptions" :key="`per-page-${option}`" :value="option">
                {{ option }}
              </option>
            </select>
          </label>
          <Link
            href="/super-admin/arsip/create"
            class="inline-flex min-h-[44px] items-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700"
          >
            + Tambah Dokumen
          </Link>
        </div>
      </div>

      <ResponsiveDataTable
        v-if="isResponsiveTableV2Enabled"
        :columns="arsipManagementTableColumns"
        :rows="documents.data"
        row-key="id"
        min-width-class="min-w-[960px]"
        empty-text="Belum ada dokumen arsip yang dikelola."
      >
        <template #cell-title="{ row }">
          <div class="text-left">
            <p class="font-medium text-gray-900 dark:text-gray-100">{{ row.title }}</p>
            <p v-if="row.description" class="mt-1 text-xs text-gray-500 dark:text-gray-400">
              {{ row.description }}
            </p>
          </div>
        </template>
        <template #cell-file="{ row }">
          <p class="text-left text-gray-700 dark:text-gray-300">{{ row.original_name }}</p>
        </template>
        <template #cell-type="{ row }">
          <span
            class="inline-flex min-h-[44px] items-center rounded border px-3 py-2 text-xs font-semibold"
            :class="row.is_global
              ? 'border-emerald-200 text-emerald-700 dark:border-emerald-900/50 dark:text-emerald-300'
              : 'border-amber-200 text-amber-700 dark:border-amber-900/50 dark:text-amber-300'"
          >
            {{ row.is_global ? 'Global' : 'Pribadi' }}
          </span>
        </template>
        <template #cell-creator_name="{ row }">
          {{ row.creator_name || '-' }}
        </template>
        <template #cell-size="{ row }">
          {{ formatBytes(row.size_bytes) }}
        </template>
        <template #cell-download_count="{ row }">
          {{ row.download_count }}
        </template>
        <template #cell-updated_at="{ row }">
          {{ formatDateTime(row.updated_at) }}
        </template>
        <template #cell-actions="{ row }">
          <div class="flex flex-wrap items-center justify-end gap-2 lg:justify-start">
            <Link
              :href="`/super-admin/arsip/${row.id}/edit`"
              class="inline-flex min-h-[44px] items-center rounded-md border border-amber-200 px-4 py-2 text-xs font-semibold text-amber-700 hover:bg-amber-50 dark:border-amber-900/50 dark:text-amber-300 dark:hover:bg-amber-900/20"
            >
              Edit
            </Link>
            <button
              type="button"
              class="inline-flex min-h-[44px] items-center rounded-md border border-rose-200 px-4 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-50 dark:border-rose-900/50 dark:text-rose-300 dark:hover:bg-rose-900/20"
              @click="openDeleteModal(row.id)"
            >
              Hapus
            </button>
          </div>
        </template>
      </ResponsiveDataTable>

      <div v-else class="overflow-x-auto">
        <table class="w-full min-w-[960px] text-sm">
          <thead class="border-b border-gray-200 dark:border-slate-700">
            <tr class="text-left text-gray-600 dark:text-gray-300">
              <th class="px-3 py-3 font-semibold">Judul</th>
              <th class="px-3 py-3 font-semibold">File</th>
              <th class="px-3 py-3 font-semibold">Tipe</th>
              <th class="px-3 py-3 font-semibold">Pengunggah</th>
              <th class="px-3 py-3 font-semibold">Ukuran</th>
              <th class="px-3 py-3 font-semibold">Diunduh</th>
              <th class="px-3 py-3 font-semibold">Diperbarui</th>
              <th class="w-44 px-3 py-3 font-semibold">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="document in documents.data"
              :key="document.id"
              class="border-b border-gray-100 align-top dark:border-slate-800"
            >
              <td class="px-3 py-3 text-gray-900 dark:text-gray-100">
                <p class="font-medium">{{ document.title }}</p>
                <p v-if="document.description" class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                  {{ document.description }}
                </p>
              </td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ document.original_name }}</td>
              <td class="px-3 py-3">
                <span
                  class="inline-flex min-h-[44px] items-center rounded border px-3 py-2 text-xs font-semibold"
                  :class="document.is_global
                    ? 'border-emerald-200 text-emerald-700 dark:border-emerald-900/50 dark:text-emerald-300'
                    : 'border-amber-200 text-amber-700 dark:border-amber-900/50 dark:text-amber-300'"
                >
                  {{ document.is_global ? 'Global' : 'Pribadi' }}
                </span>
              </td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ document.creator_name || '-' }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ formatBytes(document.size_bytes) }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ document.download_count }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ formatDateTime(document.updated_at) }}</td>
              <td class="px-3 py-3">
                <div class="flex items-center gap-2">
                  <Link
                    :href="`/super-admin/arsip/${document.id}/edit`"
                    class="inline-flex min-h-[44px] items-center rounded-md border border-amber-200 px-4 py-2 text-xs font-semibold text-amber-700 hover:bg-amber-50 dark:border-amber-900/50 dark:text-amber-300 dark:hover:bg-amber-900/20"
                  >
                    Edit
                  </Link>
                  <button
                    type="button"
                    class="inline-flex min-h-[44px] items-center rounded-md border border-rose-200 px-4 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-50 dark:border-rose-900/50 dark:text-rose-300 dark:hover:bg-rose-900/20"
                    @click="openDeleteModal(document.id)"
                  >
                    Hapus
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="documents.data.length === 0">
              <td colspan="8" class="px-3 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                Belum ada dokumen arsip yang dikelola.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <PaginationBar :links="documents.links" :from="documents.from" :to="documents.to" :total="documents.total" />
    </CardBox>

    <ConfirmActionModal
      v-model="isDeleteModalActive"
      title="Konfirmasi Hapus"
      message="Dokumen arsip yang dihapus tidak dapat dikembalikan. Lanjutkan penghapusan?"
      confirm-label="Ya, Hapus"
      @confirm="confirmDelete"
      @cancel="closeDeleteModal"
    />
  </SectionMain>
</template>
