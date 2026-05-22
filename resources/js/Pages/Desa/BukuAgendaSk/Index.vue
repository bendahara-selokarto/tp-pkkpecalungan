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

const permissions = computed(() => page.props.auth?.user?.permissions ?? [])
const canCreate = computed(() => permissions.value.includes('buku_agenda_sk.create'))
const canUpdate = computed(() => permissions.value.includes('buku_agenda_sk.update'))
const canDelete = computed(() => permissions.value.includes('buku_agenda_sk.delete'))
const canPrint = computed(() => permissions.value.includes('buku_agenda_sk.print'))

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
    router.delete(`/desa/buku-agenda-sk/${itemToDelete.value.id}`, {
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
    <SectionTitleLineWithButton :icon="mdiNotebookEditOutline" title="Buku Agenda SK" main>
      <BaseButtons>
        <BaseButton
          v-if="canPrint"
          :href="route('desa.buku-agenda-sk.report')"
          :icon="mdiPrinter"
          label="Cetak PDF"
          color="info"
          target="_blank"
        />
        <BaseButton
          v-if="canCreate"
          :href="route('desa.buku-agenda-sk.create')"
          :icon="mdiPlus"
          label="Tambah Data"
          color="emerald"
        />
      </BaseButtons>
    </SectionTitleLineWithButton>

    <CardBox class="mb-6" has-table>
      <div class="p-4 border-b border-gray-100 dark:border-slate-800">
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Daftar Agenda SK</h3>
        <p class="text-sm text-gray-500">Tahun Anggaran: {{ filters.tahun_anggaran }}</p>
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
                    :href="route('desa.buku-agenda-sk.show', item.id)"
                    title="Detail"
                  />
                  <BaseButton
                    v-if="canUpdate"
                    color="warning"
                    :icon="mdiPencil"
                    small
                    :href="route('desa.buku-agenda-sk.edit', item.id)"
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
