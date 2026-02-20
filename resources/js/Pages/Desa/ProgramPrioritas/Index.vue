<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import ConfirmActionModal from '@/admin-one/components/ConfirmActionModal.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link, router } from '@inertiajs/vue3'
import { mdiClipboardList } from '@mdi/js'
import { ref } from 'vue'

defineProps({
  programPrioritas: {
    type: Array,
    required: true,
  },
})


const deleteConfirmationMessage = 'Apakah Anda yakin ingin menghapus data program prioritas ini?'
const isDeleteModalActive = ref(false)
const deletingId = ref(null)

const hapusData = (id) => {
  deletingId.value = id
  isDeleteModalActive.value = true
}

const confirmDelete = () => {
  if (deletingId.value === null) {
    return
  }

  router.delete(`/desa/program-prioritas/${deletingId.value}`, {
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

const formatJadwal = (item) => {
  const jadwal = []
  if (item.jadwal_i) jadwal.push('I')
  if (item.jadwal_ii) jadwal.push('II')
  if (item.jadwal_iii) jadwal.push('III')
  if (item.jadwal_iv) jadwal.push('IV')
  return jadwal.join(', ') || '-'
}

const formatDana = (item) => {
  const dana = []
  if (item.sumber_dana_pusat) dana.push('Pusat')
  if (item.sumber_dana_apbd) dana.push('APBD')
  if (item.sumber_dana_swd) dana.push('SWD')
  if (item.sumber_dana_bant) dana.push('Bant')
  return dana.join(', ') || '-'
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiClipboardList" title="Program Prioritas Desa" main />

    <CardBox>
      <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Daftar Program Prioritas</h3>
        <div class="flex items-center gap-2">
          <a
            href="/desa/program-prioritas/report/pdf"
            target="_blank"
            rel="noopener"
            class="inline-flex items-center rounded-md border border-sky-300 px-4 py-2 text-sm font-medium text-sky-700 hover:bg-sky-50 dark:border-sky-900/50 dark:text-sky-300 dark:hover:bg-sky-900/20"
          >
            Cetak PDF
          </a>
          <Link
            href="/desa/program-prioritas/create"
            class="inline-flex items-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700"
          >
            + Tambah Program
          </Link>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full min-w-[1200px] text-sm">
          <thead class="border-b border-gray-200 dark:border-slate-700">
            <tr class="text-left text-gray-600 dark:text-gray-300">
              <th class="px-3 py-3 font-semibold">Program</th>
              <th class="px-3 py-3 font-semibold">Prioritas</th>
              <th class="px-3 py-3 font-semibold">Kegiatan</th>
              <th class="px-3 py-3 font-semibold">Sasaran</th>
              <th class="px-3 py-3 font-semibold">Jadwal</th>
              <th class="px-3 py-3 font-semibold">Sumber Dana</th>
              <th class="px-3 py-3 font-semibold">Keterangan</th>
              <th class="px-3 py-3 font-semibold w-44">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="item in programPrioritas"
              :key="item.id"
              class="border-b border-gray-100 align-top dark:border-slate-800"
            >
              <td class="px-3 py-3 text-gray-900 dark:text-gray-100">{{ item.program }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ item.prioritas_program }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ item.kegiatan }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ item.sasaran_target }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ formatJadwal(item) }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ formatDana(item) }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ item.keterangan || '-' }}</td>
              <td class="px-3 py-3">
                <div class="flex items-center gap-2">
                  <Link
                    :href="`/desa/program-prioritas/${item.id}`"
                    class="inline-flex rounded-md border border-sky-200 px-3 py-1.5 text-xs font-semibold text-sky-700 hover:bg-sky-50 dark:border-sky-900/50 dark:text-sky-300 dark:hover:bg-sky-900/20"
                  >
                    Lihat
                  </Link>
                  <Link
                    :href="`/desa/program-prioritas/${item.id}/edit`"
                    class="inline-flex rounded-md border border-amber-200 px-3 py-1.5 text-xs font-semibold text-amber-700 hover:bg-amber-50 dark:border-amber-900/50 dark:text-amber-300 dark:hover:bg-amber-900/20"
                  >
                    Edit
                  </Link>
                  <button
                    type="button"
                    class="inline-flex rounded-md border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-50 dark:border-rose-900/50 dark:text-rose-300 dark:hover:bg-rose-900/20"
                    @click="hapusData(item.id)"
                  >
                    Hapus
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="programPrioritas.length === 0">
              <td colspan="8" class="px-3 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                Data program prioritas belum tersedia.
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
