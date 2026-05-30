<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import ConfirmActionModal from '@/admin-one/components/ConfirmActionModal.vue'
import PaginationBar from '@/admin-one/components/PaginationBar.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import BaseButton from '@/admin-one/components/BaseButton.vue'
import BaseButtons from '@/admin-one/components/BaseButtons.vue'
import BaseIcon from '@/admin-one/components/BaseIcon.vue'
import { mdiCalendarTextOutline, mdiPlus, mdiEye, mdiPencil, mdiTrashCan, mdiFileDownloadOutline } from '@mdi/js'
import { Link, router, usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const props = defineProps({
  items: {
    type: Object,
    required: true,
  },
  filters: {
    type: Object,
    required: true,
  },
})

const page = usePage()
const isDeleteModalActive = ref(false)
const itemToDelete = ref(null)
const deleteConfirmationMessage = 'Apakah Anda yakin ingin menghapus data buku notulen simulasi ini?'

const moduleMode = computed(() => page.props.auth?.user?.moduleModes?.['buku-notulen-simulasi'])
const canCreate = computed(() => moduleMode.value === 'read-write')
const canUpdate = computed(() => moduleMode.value === 'read-write')
const canDelete = computed(() => moduleMode.value === 'read-write')

const formatDate = (dateString) => {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'long',
    year: 'numeric',
  })
}

const showDeleteModal = (item) => {
  itemToDelete.value = item
  isDeleteModalActive.value = true
}

const cancelDelete = () => {
  isDeleteModalActive.value = false
  itemToDelete.value = null
}

const confirmDelete = () => {
  if (itemToDelete.value) {
    router.delete(`/desa/simulasi/buku-notulen/${itemToDelete.value.id}`, {
      onSuccess: () => {
        isDeleteModalActive.value = false
        itemToDelete.value = null
      },
    })
  }
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiCalendarTextOutline" title="Buku Notulen Simulasi" main />

    <CardBox class="mb-6" has-table>
      <div class="p-4 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between gap-4">
        <div>
          <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Daftar Notulen Simulasi</h3>
          <p class="text-sm text-gray-500">Tahun Anggaran: {{ filters.tahun_anggaran }}</p>
        </div>
        <div class="flex items-center gap-2">
          <Link
            v-if="canCreate"
            href="/desa/simulasi/buku-notulen/create"
            class="inline-flex items-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700"
          >
            <BaseIcon :path="mdiPlus" size="18" class="mr-1" />
            Tambah Data
          </Link>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full min-w-[800px] text-sm text-left">
          <thead class="border-b border-gray-200 dark:border-slate-700">
            <tr class="text-left text-gray-600 dark:text-gray-300">
              <th class="px-3 py-3 font-semibold">Tanggal</th>
              <th class="px-3 py-3 font-semibold">Agenda / Nama Kegiatan</th>
              <th class="px-3 py-3 font-semibold">Pimpinan Rapat / Narasumber</th>
              <th class="px-3 py-3 font-semibold">Tempat / Instansi</th>
              <th class="px-3 py-3 font-semibold">Lampiran</th>
              <th class="px-3 py-3 font-semibold w-32 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="item in items.data"
              :key="item.id"
              class="border-b border-gray-100 align-top dark:border-slate-800"
            >
              <td class="px-3 py-3 text-gray-900 font-medium dark:text-gray-100">{{ formatDate(item.entry_date) }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ item.title }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ item.person_name || '-' }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ item.institution || '-' }}</td>
              <td class="px-3 py-3">
                <a
                  v-if="item.file_url"
                  :href="item.file_url"
                  target="_blank"
                  class="inline-flex items-center gap-1 text-emerald-600 hover:underline dark:text-emerald-400"
                >
                  <BaseIcon :path="mdiFileDownloadOutline" size="16" />
                  Unduh
                </a>
                <span v-else class="text-gray-400">-</span>
              </td>
              <td class="px-3 py-3">
                <BaseButtons type="justify-center" no-wrap>
                  <BaseButton
                    color="info"
                    :icon="mdiEye"
                    small
                    :href="`/desa/simulasi/buku-notulen/${item.id}`"
                    title="Detail"
                  />
                  <BaseButton
                    v-if="canUpdate"
                    color="warning"
                    :icon="mdiPencil"
                    small
                    :href="`/desa/simulasi/buku-notulen/${item.id}/edit`"
                    title="Edit"
                  />
                  <BaseButton
                    v-if="canDelete"
                    color="danger"
                    :icon="mdiTrashCan"
                    small
                    title="Hapus"
                    @click="showDeleteModal(item)"
                  />
                </BaseButtons>
              </td>
            </tr>
            <tr v-if="items.data.length === 0">
              <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                Data belum tersedia.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="p-4 border-t border-gray-100 dark:border-slate-800">
        <PaginationBar :links="items.links" />
      </div>
    </CardBox>

    <ConfirmActionModal
      v-model="isDeleteModalActive"
      title="Konfirmasi Hapus"
      :message="deleteConfirmationMessage"
      confirm-label="Ya, Hapus"
      @confirm="confirmDelete"
      @cancel="cancelDelete"
    />
  </SectionMain>
</template>
