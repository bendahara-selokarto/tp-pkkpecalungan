<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import { mdiStore } from '@mdi/js'
import { computed } from 'vue'

const props = defineProps({
  kejarPaketItems: {
    type: Array,
    required: true,
  },
})

const page = usePage()
const flashSuccess = computed(() => page.props.flash?.success)

const hapusKejarPaket = (id) => {
  if (!window.confirm('Apakah Anda yakin ingin menghapus data kejar paket ini?')) {
    return
  }

  router.delete(`/desa/kejar-paket/${id}`)
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiStore" title="Kejar Paket Desa" main />

    <div
      v-if="flashSuccess"
      class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900/40 dark:bg-emerald-900/20 dark:text-emerald-300"
    >
      {{ flashSuccess }}
    </div>

    <CardBox>
      <div class="mb-4 flex items-center justify-between gap-4">
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Daftar Kejar Paket</h3>
        <div class="flex items-center gap-2">
          <a
            href="/desa/kejar-paket/report/pdf"
            target="_blank"
            rel="noopener"
            class="inline-flex items-center rounded-md border border-sky-300 px-4 py-2 text-sm font-medium text-sky-700 hover:bg-sky-50 dark:border-sky-900/50 dark:text-sky-300 dark:hover:bg-sky-900/20"
          >
            Cetak PDF
          </a>
          <Link
            href="/desa/kejar-paket/create"
            class="inline-flex items-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700"
          >
            + Tambah Data
          </Link>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full min-w-[980px] text-sm">
          <thead class="border-b border-gray-200 dark:border-slate-700">
            <tr class="text-left text-gray-600 dark:text-gray-300">
              <th class="px-3 py-3 font-semibold">Nama Kejar Paket</th>
              <th class="px-3 py-3 font-semibold">Jenis Kejar Paket/KF/PAUD</th>
              <th class="px-3 py-3 font-semibold">Warga Belajar/Siswa (L)</th>
              <th class="px-3 py-3 font-semibold">Warga Belajar/Siswa (P)</th>
              <th class="px-3 py-3 font-semibold">Pengajar (L)</th>
              <th class="px-3 py-3 font-semibold">Pengajar (P)</th>
              <th class="px-3 py-3 font-semibold w-44">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="item in props.kejarPaketItems"
              :key="item.id"
              class="border-b border-gray-100 align-top dark:border-slate-800"
            >
              <td class="px-3 py-3 text-gray-900 dark:text-gray-100">{{ item.nama_kejar_paket }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ item.jenis_kejar_paket }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ item.jumlah_warga_belajar_l }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ item.jumlah_warga_belajar_p }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ item.jumlah_pengajar_l }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ item.jumlah_pengajar_p }}</td>
              <td class="px-3 py-3">
                <div class="flex items-center gap-2">
                  <Link
                    :href="`/desa/kejar-paket/${item.id}`"
                    class="inline-flex rounded-md border border-sky-200 px-3 py-1.5 text-xs font-semibold text-sky-700 hover:bg-sky-50 dark:border-sky-900/50 dark:text-sky-300 dark:hover:bg-sky-900/20"
                  >
                    Lihat
                  </Link>
                  <Link
                    :href="`/desa/kejar-paket/${item.id}/edit`"
                    class="inline-flex rounded-md border border-amber-200 px-3 py-1.5 text-xs font-semibold text-amber-700 hover:bg-amber-50 dark:border-amber-900/50 dark:text-amber-300 dark:hover:bg-amber-900/20"
                  >
                    Edit
                  </Link>
                  <button
                    type="button"
                    class="inline-flex rounded-md border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-50 dark:border-rose-900/50 dark:text-rose-300 dark:hover:bg-rose-900/20"
                    @click="hapusKejarPaket(item.id)"
                  >
                    Hapus
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="props.kejarPaketItems.length === 0">
              <td colspan="7" class="px-3 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                Data kejar paket belum tersedia.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </CardBox>
  </SectionMain>
</template>






