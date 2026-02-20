<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import { mdiAccountGroup } from '@mdi/js'
import { computed } from 'vue'

defineProps({
  kaderKhususItems: {
    type: Array,
    required: true,
  },
})

const page = usePage()
const flashSuccess = computed(() => page.props.flash?.success)

const formatJenisKelamin = (value) => (value === 'L' ? 'Laki-laki' : 'Perempuan')
const formatStatusPerkawinan = (value) => (value === 'kawin' ? 'Nikah' : 'Belum Nikah')

const hapusKaderKhusus = (id) => {
  if (!window.confirm('Apakah Anda yakin ingin menghapus data kader TP PKK ini?')) {
    return
  }

  router.delete(`/desa/kader-khusus/${id}`)
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiAccountGroup" title="Buku Daftar Kader Tim Penggerak PKK Desa" main />

    <div
      v-if="flashSuccess"
      class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900/40 dark:bg-emerald-900/20 dark:text-emerald-300"
    >
      {{ flashSuccess }}
    </div>

    <CardBox>
      <div class="mb-4 flex items-center justify-between gap-4">
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Daftar Kader Tim Penggerak PKK</h3>
        <div class="flex items-center gap-2">
          <a
            href="/desa/kader-khusus/report/pdf"
            target="_blank"
            rel="noopener"
            class="inline-flex items-center rounded-md border border-sky-300 px-4 py-2 text-sm font-medium text-sky-700 hover:bg-sky-50 dark:border-sky-900/50 dark:text-sky-300 dark:hover:bg-sky-900/20"
          >
            Cetak PDF
          </a>
          <Link
            href="/desa/kader-khusus/create"
            class="inline-flex items-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700"
          >
            + Tambah Kader TP PKK
          </Link>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full min-w-[960px] text-sm">
          <thead class="border-b border-gray-200 dark:border-slate-700">
            <tr class="text-left text-gray-600 dark:text-gray-300">
              <th class="px-3 py-3 font-semibold">Nama</th>
              <th class="px-3 py-3 font-semibold">Jenis Kelamin</th>
              <th class="px-3 py-3 font-semibold">Umur</th>
              <th class="px-3 py-3 font-semibold">Status</th>
              <th class="px-3 py-3 font-semibold">Jenis Kader TP PKK</th>
              <th class="px-3 py-3 font-semibold w-44">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="item in kaderKhususItems"
              :key="item.id"
              class="border-b border-gray-100 align-top dark:border-slate-800"
            >
              <td class="px-3 py-3 text-gray-900 dark:text-gray-100">{{ item.nama }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ formatJenisKelamin(item.jenis_kelamin) }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ item.umur ?? '-' }} tahun</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ formatStatusPerkawinan(item.status_perkawinan) }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ item.jenis_kader_khusus }}</td>
              <td class="px-3 py-3">
                <div class="flex items-center gap-2">
                  <Link
                    :href="`/desa/kader-khusus/${item.id}`"
                    class="inline-flex rounded-md border border-sky-200 px-3 py-1.5 text-xs font-semibold text-sky-700 hover:bg-sky-50 dark:border-sky-900/50 dark:text-sky-300 dark:hover:bg-sky-900/20"
                  >
                    Lihat
                  </Link>
                  <Link
                    :href="`/desa/kader-khusus/${item.id}/edit`"
                    class="inline-flex rounded-md border border-amber-200 px-3 py-1.5 text-xs font-semibold text-amber-700 hover:bg-amber-50 dark:border-amber-900/50 dark:text-amber-300 dark:hover:bg-amber-900/20"
                  >
                    Edit
                  </Link>
                  <button
                    type="button"
                    class="inline-flex rounded-md border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-50 dark:border-rose-900/50 dark:text-rose-300 dark:hover:bg-rose-900/20"
                    @click="hapusKaderKhusus(item.id)"
                  >
                    Hapus
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="kaderKhususItems.length === 0">
              <td colspan="6" class="px-3 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                Data kader TP PKK belum tersedia.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </CardBox>
  </SectionMain>
</template>


