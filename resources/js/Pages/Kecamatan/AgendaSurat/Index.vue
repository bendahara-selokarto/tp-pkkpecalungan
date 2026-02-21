<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import ConfirmActionModal from '@/admin-one/components/ConfirmActionModal.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { formatDateForDisplay } from '@/utils/dateFormatter'
import { Link, router } from '@inertiajs/vue3'
import { mdiEmailFastOutline } from '@mdi/js'
import { ref } from 'vue'

defineProps({
  agendaSurats: {
    type: Array,
    required: true,
  },
})

const deleteConfirmationMessage = 'Apakah Anda yakin ingin menghapus agenda surat ini?'
const isDeleteModalActive = ref(false)
const deletingId = ref(null)

const hapusAgenda = (id) => {
  deletingId.value = id
  isDeleteModalActive.value = true
}

const confirmDelete = () => {
  if (deletingId.value === null) {
    return
  }

  router.delete(`/kecamatan/agenda-surat/${deletingId.value}`, {
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

const formatDate = (value) => {
  return formatDateForDisplay(value)
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiEmailFastOutline" title="Agenda Surat Kecamatan" main />

    <CardBox>
      <div class="mb-4 flex items-center justify-between gap-4">
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Buku Agenda Surat</h3>
        <div class="flex items-center gap-2">
          <a
            href="/kecamatan/agenda-surat/ekspedisi/report/pdf"
            target="_blank"
            rel="noopener"
            class="inline-flex items-center rounded-md border border-indigo-300 px-4 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-50 dark:border-indigo-900/50 dark:text-indigo-300 dark:hover:bg-indigo-900/20"
          >
            Cetak Ekspedisi
          </a>
          <a
            href="/kecamatan/agenda-surat/report/pdf"
            target="_blank"
            rel="noopener"
            class="inline-flex items-center rounded-md border border-sky-300 px-4 py-2 text-sm font-medium text-sky-700 hover:bg-sky-50 dark:border-sky-900/50 dark:text-sky-300 dark:hover:bg-sky-900/20"
          >
            Cetak PDF
          </a>
          <Link
            href="/kecamatan/agenda-surat/create"
            class="inline-flex items-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700"
          >
            + Tambah Agenda
          </Link>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full min-w-[980px] text-sm">
          <thead class="border-b border-gray-200 dark:border-slate-700">
            <tr class="text-left text-gray-600 dark:text-gray-300">
              <th class="px-3 py-3 font-semibold">Jenis</th>
              <th class="px-3 py-3 font-semibold">Tgl Surat</th>
              <th class="px-3 py-3 font-semibold">No Surat</th>
              <th class="px-3 py-3 font-semibold">Perihal</th>
              <th class="px-3 py-3 font-semibold w-44">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="item in agendaSurats"
              :key="item.id"
              class="border-b border-gray-100 align-top dark:border-slate-800"
            >
              <td class="px-3 py-3 uppercase text-gray-900 dark:text-gray-100">{{ item.jenis_surat }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ formatDate(item.tanggal_surat) }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ item.nomor_surat }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ item.perihal }}</td>
              <td class="px-3 py-3">
                <div class="flex items-center gap-2">
                  <Link
                    :href="`/kecamatan/agenda-surat/${item.id}`"
                    class="inline-flex rounded-md border border-sky-200 px-3 py-1.5 text-xs font-semibold text-sky-700 hover:bg-sky-50 dark:border-sky-900/50 dark:text-sky-300 dark:hover:bg-sky-900/20"
                  >
                    Lihat
                  </Link>
                  <Link
                    :href="`/kecamatan/agenda-surat/${item.id}/edit`"
                    class="inline-flex rounded-md border border-amber-200 px-3 py-1.5 text-xs font-semibold text-amber-700 hover:bg-amber-50 dark:border-amber-900/50 dark:text-amber-300 dark:hover:bg-amber-900/20"
                  >
                    Edit
                  </Link>
                  <button
                    type="button"
                    class="inline-flex rounded-md border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-50 dark:border-rose-900/50 dark:text-rose-300 dark:hover:bg-rose-900/20"
                    @click="hapusAgenda(item.id)"
                  >
                    Hapus
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="agendaSurats.length === 0">
              <td colspan="5" class="px-3 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                Data agenda surat belum tersedia.
              </td>
            </tr>
          </tbody>
        </table>
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

