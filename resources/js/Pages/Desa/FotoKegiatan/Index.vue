<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import ConfirmActionModal from '@/admin-one/components/ConfirmActionModal.vue'
import PaginationBar from '@/admin-one/components/PaginationBar.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { formatDateForDisplay } from '@/utils/dateFormatter'
import { Link, router } from '@inertiajs/vue3'
import { mdiCamera } from '@mdi/js'
import { ref } from 'vue'

const props = defineProps({
  fotoKegiatans: {
    type: Object,
    required: true,
  },
})

const deleteConfirmationMessage = 'Apakah Anda yakin ingin menghapus foto kegiatan ini?'
const isDeleteModalActive = ref(false)
const deletingId = ref(null)

const hapusFoto = (id) => {
  deletingId.value = id
  isDeleteModalActive.value = true
}

const confirmDelete = () => {
  if (deletingId.value === null) {
    return
  }

  router.delete(`/desa/foto-kegiatan/${deletingId.value}`, {
    onFinish: () => {
      isDeleteModalActive.value = false
      deletingId.value = null
    },
  })
}

const cancelDelete = () => {
  isDeleteModalActive.value = false
  deletingId.value = null
}

const formatDate = (value) => formatDateForDisplay(value)
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiCamera" title="Foto Kegiatan Desa" main />

    <CardBox>
      <div class="mb-4 flex items-center justify-between gap-4">
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Daftar Foto Kegiatan</h3>
        <Link
          href="/desa/foto-kegiatan/create"
          class="inline-flex items-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700"
        >
          + Unggah Foto
        </Link>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full min-w-[700px] text-sm">
          <thead class="border-b border-gray-200 dark:border-slate-700">
            <tr class="text-left text-gray-600 dark:text-gray-300">
              <th class="px-3 py-3 font-semibold">Foto</th>
              <th class="px-3 py-3 font-semibold">Judul Kegiatan</th>
              <th class="px-3 py-3 font-semibold">Tanggal</th>
              <th class="px-3 py-3 font-semibold w-44">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="item in fotoKegiatans.data"
              :key="item.id"
              class="border-b border-gray-100 align-top dark:border-slate-800"
            >
              <td class="px-3 py-3">
                <img v-if="item.image_url" :src="item.image_url" class="h-16 w-24 rounded object-cover shadow-sm" alt="Foto Kegiatan">
                <div v-else class="flex h-16 w-24 items-center justify-center rounded bg-gray-100 text-gray-400 dark:bg-slate-800">
                  No Image
                </div>
              </td>
              <td class="px-3 py-3 text-gray-900 dark:text-gray-100">{{ item.title }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ formatDate(item.activity_date) }}</td>
              <td class="px-3 py-3">
                <div class="flex items-center gap-2">
                  <Link
                    :href="`/desa/foto-kegiatan/${item.id}`"
                    class="inline-flex rounded-md border border-sky-200 px-3 py-1.5 text-xs font-semibold text-sky-700 hover:bg-sky-50 dark:border-sky-900/50 dark:text-sky-300 dark:hover:bg-sky-900/20"
                  >
                    Lihat
                  </Link>
                  <Link
                    :href="`/desa/foto-kegiatan/${item.id}/edit`"
                    class="inline-flex rounded-md border border-amber-200 px-3 py-1.5 text-xs font-semibold text-amber-700 hover:bg-amber-50 dark:border-amber-900/50 dark:text-amber-300 dark:hover:bg-amber-900/20"
                  >
                    Edit
                  </Link>
                  <button
                    type="button"
                    class="inline-flex rounded-md border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-50 dark:border-rose-900/50 dark:text-rose-300 dark:hover:bg-rose-900/20"
                    @click="hapusFoto(item.id)"
                  >
                    Hapus
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="fotoKegiatans.data.length === 0">
              <td colspan="4" class="px-3 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                Belum ada foto kegiatan yang diunggah.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <PaginationBar
        :links="fotoKegiatans.links"
        :from="fotoKegiatans.from"
        :to="fotoKegiatans.to"
        :total="fotoKegiatans.total"
      />
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
