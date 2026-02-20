<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import ConfirmActionModal from '@/admin-one/components/ConfirmActionModal.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link, router } from '@inertiajs/vue3'
import { mdiStore } from '@mdi/js'
import { ref } from 'vue'

const props = defineProps({
  posyanduItems: {
    type: Array,
    required: true,
  },
})


const deleteConfirmationMessage = 'Apakah Anda yakin ingin menghapus data posyandu ini?'
const isDeleteModalActive = ref(false)
const deletingId = ref(null)

const hapusPosyandu = (id) => {
  deletingId.value = id
  isDeleteModalActive.value = true
}

const confirmDelete = () => {
  if (deletingId.value === null) {
    return
  }

  router.delete(`/kecamatan/posyandu/${deletingId.value}`, {
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
    <SectionTitleLineWithButton :icon="mdiStore" title="Data Isian Posyandu oleh TP PKK Kecamatan" main />

    <CardBox>
      <div class="mb-4 flex items-center justify-between gap-4">
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Daftar Data Isian Posyandu oleh TP PKK</h3>
        <div class="flex items-center gap-2">
          <a
            href="/kecamatan/posyandu/report/pdf"
            target="_blank"
            rel="noopener"
            class="inline-flex items-center rounded-md border border-sky-300 px-4 py-2 text-sm font-medium text-sky-700 hover:bg-sky-50 dark:border-sky-900/50 dark:text-sky-300 dark:hover:bg-sky-900/20"
          >
            Cetak PDF
          </a>
          <Link
            href="/kecamatan/posyandu/create"
            class="inline-flex items-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700"
          >
            + Tambah Data
          </Link>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full min-w-[1400px] text-sm">
          <thead class="border-b border-gray-200 dark:border-slate-700">
            <tr class="text-left text-gray-600 dark:text-gray-300">
              <th class="px-3 py-3 font-semibold">Nama Posyandu</th>
              <th class="px-3 py-3 font-semibold">Pengelola</th>
              <th class="px-3 py-3 font-semibold">Sekretaris</th>
              <th class="px-3 py-3 font-semibold">Jenis Posyandu</th>
              <th class="px-3 py-3 font-semibold">Jumlah Kader</th>
              <th class="px-3 py-3 font-semibold">Jenis Kegiatan</th>
              <th class="px-3 py-3 font-semibold">Frekuensi</th>
              <th class="px-3 py-3 font-semibold">Pengunjung (L)</th>
              <th class="px-3 py-3 font-semibold">Pengunjung (P)</th>
              <th class="px-3 py-3 font-semibold">Petugas (L)</th>
              <th class="px-3 py-3 font-semibold">Petugas (P)</th>
              <th class="px-3 py-3 font-semibold w-44">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="item in props.posyanduItems"
              :key="item.id"
              class="border-b border-gray-100 align-top dark:border-slate-800"
            >
              <td class="px-3 py-3 text-gray-900 dark:text-gray-100">{{ item.nama_posyandu }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ item.nama_pengelola }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ item.nama_sekretaris }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ item.jenis_posyandu }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ item.jumlah_kader }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ item.jenis_kegiatan }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ item.frekuensi_layanan }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ item.jumlah_pengunjung_l }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ item.jumlah_pengunjung_p }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ item.jumlah_petugas_l }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ item.jumlah_petugas_p }}</td>
              <td class="px-3 py-3">
                <div class="flex items-center gap-2">
                  <Link
                    :href="`/kecamatan/posyandu/${item.id}`"
                    class="inline-flex rounded-md border border-sky-200 px-3 py-1.5 text-xs font-semibold text-sky-700 hover:bg-sky-50 dark:border-sky-900/50 dark:text-sky-300 dark:hover:bg-sky-900/20"
                  >
                    Lihat
                  </Link>
                  <Link
                    :href="`/kecamatan/posyandu/${item.id}/edit`"
                    class="inline-flex rounded-md border border-amber-200 px-3 py-1.5 text-xs font-semibold text-amber-700 hover:bg-amber-50 dark:border-amber-900/50 dark:text-amber-300 dark:hover:bg-amber-900/20"
                  >
                    Edit
                  </Link>
                  <button
                    type="button"
                    class="inline-flex rounded-md border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-50 dark:border-rose-900/50 dark:text-rose-300 dark:hover:bg-rose-900/20"
                    @click="hapusPosyandu(item.id)"
                  >
                    Hapus
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="props.posyanduItems.length === 0">
              <td colspan="12" class="px-3 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                Data posyandu belum tersedia.
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
