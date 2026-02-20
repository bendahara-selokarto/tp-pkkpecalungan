<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import ConfirmActionModal from '@/admin-one/components/ConfirmActionModal.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link, router } from '@inertiajs/vue3'
import { mdiAccountGroup } from '@mdi/js'
import { ref } from 'vue'

const props = defineProps({
  dataPelatihanKaderItems: {
    type: Array,
    required: true,
  },
})


const deleteConfirmationMessage = 'Apakah Anda yakin ingin menghapus data ini?'
const isDeleteModalActive = ref(false)
const deletingId = ref(null)

const hapusDataPelatihanKader = (id) => {
  deletingId.value = id
  isDeleteModalActive.value = true
}

const confirmDelete = () => {
  if (deletingId.value === null) {
    return
  }

  router.delete(`/kecamatan/data-pelatihan-kader/${deletingId.value}`, {
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
    <SectionTitleLineWithButton :icon="mdiAccountGroup" title="Data Pelatihan Kader Kecamatan" main />

    <CardBox>
      <div class="mb-4 flex items-center justify-between gap-4">
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Daftar Data Pelatihan Kader</h3>
        <div class="flex items-center gap-2">
          <a
            href="/kecamatan/data-pelatihan-kader/report/pdf"
            target="_blank"
            rel="noopener"
            class="inline-flex items-center rounded-md border border-sky-300 px-4 py-2 text-sm font-medium text-sky-700 hover:bg-sky-50 dark:border-sky-900/50 dark:text-sky-300 dark:hover:bg-sky-900/20"
          >
            Cetak PDF
          </a>
          <Link
            href="/kecamatan/data-pelatihan-kader/create"
            class="inline-flex items-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700"
          >
            + Tambah Data
          </Link>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full min-w-[1100px] text-sm">
          <thead class="border-b border-gray-200 dark:border-slate-700">
            <tr class="text-left text-gray-600 dark:text-gray-300">
              <th class="px-3 py-3 font-semibold">No Registrasi</th>
              <th class="px-3 py-3 font-semibold">Nama Kader</th>
              <th class="px-3 py-3 font-semibold">Jabatan/Fungsi</th>
              <th class="px-3 py-3 font-semibold">Judul Pelatihan</th>
              <th class="px-3 py-3 font-semibold text-center">Tahun</th>
              <th class="px-3 py-3 font-semibold text-center">Status Sertifikat</th>
              <th class="px-3 py-3 font-semibold w-44">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="item in props.dataPelatihanKaderItems"
              :key="item.id"
              class="border-b border-gray-100 align-top dark:border-slate-800"
            >
              <td class="px-3 py-3 text-gray-900 dark:text-gray-100">{{ item.nomor_registrasi }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ item.nama_lengkap_kader }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ item.jabatan_fungsi }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ item.judul_pelatihan }}</td>
              <td class="px-3 py-3 text-center text-gray-700 dark:text-gray-300">{{ item.tahun_penyelenggaraan }}</td>
              <td class="px-3 py-3 text-center text-gray-700 dark:text-gray-300">{{ item.status_sertifikat }}</td>
              <td class="px-3 py-3">
                <div class="flex items-center gap-2">
                  <Link
                    :href="`/kecamatan/data-pelatihan-kader/${item.id}`"
                    class="inline-flex rounded-md border border-sky-200 px-3 py-1.5 text-xs font-semibold text-sky-700 hover:bg-sky-50 dark:border-sky-900/50 dark:text-sky-300 dark:hover:bg-sky-900/20"
                  >
                    Lihat
                  </Link>
                  <Link
                    :href="`/kecamatan/data-pelatihan-kader/${item.id}/edit`"
                    class="inline-flex rounded-md border border-amber-200 px-3 py-1.5 text-xs font-semibold text-amber-700 hover:bg-amber-50 dark:border-amber-900/50 dark:text-amber-300 dark:hover:bg-amber-900/20"
                  >
                    Edit
                  </Link>
                  <button
                    type="button"
                    class="inline-flex rounded-md border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-50 dark:border-rose-900/50 dark:text-rose-300 dark:hover:bg-rose-900/20"
                    @click="hapusDataPelatihanKader(item.id)"
                  >
                    Hapus
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="props.dataPelatihanKaderItems.length === 0">
              <td colspan="7" class="px-3 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                Data Pelatihan Kader belum tersedia.
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

