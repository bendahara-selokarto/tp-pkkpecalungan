<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import ConfirmActionModal from '@/admin-one/components/ConfirmActionModal.vue'
import PaginationBar from '@/admin-one/components/PaginationBar.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import BaseButton from '@/admin-one/components/BaseButton.vue'
import BaseButtons from '@/admin-one/components/BaseButtons.vue'
import BaseIcon from '@/admin-one/components/BaseIcon.vue'
import { mdiNotebookEditOutline, mdiPlus, mdiEye, mdiPencil, mdiTrashCan, mdiFileDownloadOutline, mdiPrinter } from '@mdi/js'
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
const deleteConfirmationMessage = 'Apakah Anda yakin ingin menghapus data agenda SK ini?'

const moduleMode = computed(() => page.props.auth?.user?.moduleModes?.['buku-agenda-sk'])
const canCreate = computed(() => moduleMode.value === 'read-write')
const canUpdate = computed(() => moduleMode.value === 'read-write')
const canDelete = computed(() => moduleMode.value === 'read-write')
const canPrint = computed(() => !!moduleMode.value)

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
    router.delete(`/kecamatan/buku-agenda-sk/${itemToDelete.value.id}`, {
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
    <SectionTitleLineWithButton :icon="mdiNotebookEditOutline" title="Buku Agenda SK" main />

    <CardBox class="mb-6" has-table>
      <div class="p-4 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between gap-4">
        <div>
          <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Daftar Agenda SK</h3>
          <p class="text-sm text-gray-500">Tahun Anggaran: {{ filters.tahun_anggaran }}</p>
        </div>
        <div class="flex items-center gap-2">
          <a
            href="/kecamatan/buku-agenda-sk/report/pdf"
            target="_blank"
            rel="noopener"
            class="inline-flex items-center rounded-md border border-sky-300 px-4 py-2 text-sm font-medium text-sky-700 hover:bg-sky-50 dark:border-sky-900/50 dark:text-sky-300 dark:hover:bg-sky-900/20"
          >
            <BaseIcon :path="mdiPrinter" size="18" class="mr-2" />
            Cetak PDF
          </a>
          <Link
            href="/kecamatan/buku-agenda-sk/create"
            class="inline-flex items-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700"
          >
            <BaseIcon :path="mdiPlus" size="18" class="mr-1" />
            Tambah Data
          </Link>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full min-w-[1000px] text-sm text-left">
          <thead class="border-b border-gray-200 dark:border-slate-700">
            <tr class="text-left text-gray-600 dark:text-gray-300">
              <th class="px-3 py-3 font-semibold">No SK</th>
              <th class="px-3 py-3 font-semibold">Tanggal SK</th>
              <th class="px-3 py-3 font-semibold">Kepada</th>
              <th class="px-3 py-3 font-semibold">Perihal</th>
              <th class="px-3 py-3 font-semibold">Tembusan</th>
              <th class="px-3 py-3 font-semibold">File</th>
              <th class="px-3 py-3 font-semibold w-52 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="item in items.data"
              :key="item.id"
              class="border-b border-gray-100 align-top dark:border-slate-800"
            >
              <td class="px-3 py-3 text-gray-900 font-medium dark:text-gray-100">{{ item.nomor_sk }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ formatDate(item.tanggal_sk) }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ item.kepada }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ item.perihal }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ item.tembusan || '-' }}</td>
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
                    :href="`/kecamatan/buku-agenda-sk/${item.id}`"
                    title="Detail"
                  />
                  <BaseButton
                    color="warning"
                    :icon="mdiPencil"
                    small
                    :href="`/kecamatan/buku-agenda-sk/${item.id}/edit`"
                    title="Edit"
                  />
                  <BaseButton
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
              <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                Data agenda SK belum tersedia.
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
