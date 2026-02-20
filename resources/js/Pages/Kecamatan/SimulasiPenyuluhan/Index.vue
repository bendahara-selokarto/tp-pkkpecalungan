<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import ConfirmActionModal from '@/admin-one/components/ConfirmActionModal.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link, router } from '@inertiajs/vue3'
import { mdiAccountGroup } from '@mdi/js'
import { ref } from 'vue'

defineProps({
  simulasiPenyuluhanItems: {
    type: Array,
    required: true,
  },
})


const totalKader = (item) => Number(item.jumlah_kader_l || 0) + Number(item.jumlah_kader_p || 0)

const deleteConfirmationMessage = 'Apakah Anda yakin ingin menghapus data isian kelompok simulasi dan penyuluhan ini?'
const isDeleteModalActive = ref(false)
const deletingId = ref(null)

const hapusSimulasiPenyuluhan = (id) => {
  deletingId.value = id
  isDeleteModalActive.value = true
}

const confirmDelete = () => {
  if (deletingId.value === null) {
    return
  }

  router.delete(`/kecamatan/simulasi-penyuluhan/${deletingId.value}`, {
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
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiAccountGroup" title="Data Isian Kelompok Simulasi dan Penyuluhan (Kecamatan)" main />

    <CardBox>
      <div class="mb-4 flex items-center justify-between gap-4">
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Daftar Data Isian Kelompok Simulasi dan Penyuluhan</h3>
        <div class="flex items-center gap-2">
          <a
            href="/kecamatan/simulasi-penyuluhan/report/pdf"
            target="_blank"
            rel="noopener"
            class="inline-flex items-center rounded-md border border-sky-300 px-4 py-2 text-sm font-medium text-sky-700 hover:bg-sky-50 dark:border-sky-900/50 dark:text-sky-300 dark:hover:bg-sky-900/20"
          >
            Cetak PDF
          </a>
          <Link
            href="/kecamatan/simulasi-penyuluhan/create"
            class="inline-flex items-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700"
          >
            + Tambah Data
          </Link>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full min-w-[1020px] text-sm">
          <thead class="border-b border-gray-200 dark:border-slate-700">
            <tr class="text-left text-gray-600 dark:text-gray-300">
              <th class="px-3 py-3 font-semibold">Nama Kegiatan</th>
              <th class="px-3 py-3 font-semibold">Jenis Simulasi dan Penyuluhan</th>
              <th class="px-3 py-3 font-semibold text-center">Kelompok</th>
              <th class="px-3 py-3 font-semibold text-center">Sosialisasi</th>
              <th class="px-3 py-3 font-semibold text-center">Kader L</th>
              <th class="px-3 py-3 font-semibold text-center">Kader P</th>
              <th class="px-3 py-3 font-semibold text-center">Total Kader</th>
              <th class="px-3 py-3 font-semibold w-44">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="item in simulasiPenyuluhanItems"
              :key="item.id"
              class="border-b border-gray-100 align-top dark:border-slate-800"
            >
              <td class="px-3 py-3 text-gray-900 dark:text-gray-100">{{ item.nama_kegiatan }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ item.jenis_simulasi_penyuluhan }}</td>
              <td class="px-3 py-3 text-center text-gray-700 dark:text-gray-300">{{ item.jumlah_kelompok }}</td>
              <td class="px-3 py-3 text-center text-gray-700 dark:text-gray-300">{{ item.jumlah_sosialisasi }}</td>
              <td class="px-3 py-3 text-center text-gray-700 dark:text-gray-300">{{ item.jumlah_kader_l }}</td>
              <td class="px-3 py-3 text-center text-gray-700 dark:text-gray-300">{{ item.jumlah_kader_p }}</td>
              <td class="px-3 py-3 text-center text-gray-700 dark:text-gray-300">{{ totalKader(item) }}</td>
              <td class="px-3 py-3">
                <div class="flex items-center gap-2">
                  <Link
                    :href="`/kecamatan/simulasi-penyuluhan/${item.id}`"
                    class="inline-flex rounded-md border border-sky-200 px-3 py-1.5 text-xs font-semibold text-sky-700 hover:bg-sky-50 dark:border-sky-900/50 dark:text-sky-300 dark:hover:bg-sky-900/20"
                  >
                    Lihat
                  </Link>
                  <Link
                    :href="`/kecamatan/simulasi-penyuluhan/${item.id}/edit`"
                    class="inline-flex rounded-md border border-amber-200 px-3 py-1.5 text-xs font-semibold text-amber-700 hover:bg-amber-50 dark:border-amber-900/50 dark:text-amber-300 dark:hover:bg-amber-900/20"
                  >
                    Edit
                  </Link>
                  <button
                    type="button"
                    class="inline-flex rounded-md border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-50 dark:border-rose-900/50 dark:text-rose-300 dark:hover:bg-rose-900/20"
                    @click="hapusSimulasiPenyuluhan(item.id)"
                  >
                    Hapus
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="simulasiPenyuluhanItems.length === 0">
              <td colspan="8" class="px-3 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                Data isian kelompok simulasi dan penyuluhan belum tersedia.
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



