<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import PaginationBar from '@/admin-one/components/PaginationBar.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { router } from '@inertiajs/vue3'
import { mdiBookOpenVariant } from '@mdi/js'
import { computed } from 'vue'

const props = defineProps({
  catatanKeluargaItems: {
    type: Object,
    required: true,
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
  pagination: {
    type: Object,
    default: () => ({
      perPageOptions: [10, 25, 50],
    }),
  },
})

const perPage = computed(() => props.filters.per_page ?? 10)

const updatePerPage = (event) => {
  const selectedPerPage = Number(event.target.value)

  router.get('/kecamatan/catatan-keluarga', { per_page: selectedPerPage }, {
    preserveScroll: true,
    preserveState: true,
    replace: true,
  })
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiBookOpenVariant" title="Catatan Keluarga Kecamatan" main />

    <CardBox>
      <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <p class="text-xs text-gray-500 dark:text-gray-400">
          Laporan catatan keluarga tersedia pada submenu <span class="font-semibold">Sekretaris TPK</span> di sidebar.
        </p>
        <label class="text-xs text-gray-600 dark:text-gray-300">
          Per halaman
          <select
            :value="perPage"
            class="ml-2 rounded-md border border-gray-300 px-2 py-1 text-xs dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
            @change="updatePerPage"
          >
            <option v-for="option in pagination.perPageOptions" :key="`per-page-${option}`" :value="option">
              {{ option }}
            </option>
          </select>
        </label>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full min-w-[1200px] text-sm">
          <thead class="border-b border-gray-200 dark:border-slate-700">
            <tr class="text-left text-gray-600 dark:text-gray-300">
              <th class="px-3 py-3 font-semibold text-center">No</th>
              <th class="px-3 py-3 font-semibold">Nama Kepala Rumah Tangga</th>
              <th class="px-3 py-3 font-semibold text-center">Jumlah Anggota Rumah Tangga</th>
              <th class="px-3 py-3 font-semibold text-center">Kerja Bakti</th>
              <th class="px-3 py-3 font-semibold text-center">Rukun Kematian</th>
              <th class="px-3 py-3 font-semibold text-center">Keagamaan</th>
              <th class="px-3 py-3 font-semibold text-center">Jimpitan</th>
              <th class="px-3 py-3 font-semibold text-center">Arisan</th>
              <th class="px-3 py-3 font-semibold text-center">Lain-Lain</th>
              <th class="px-3 py-3 font-semibold">Keterangan</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="item in props.catatanKeluargaItems.data"
              :key="item.id"
              class="border-b border-gray-100 align-top dark:border-slate-800"
            >
              <td class="px-3 py-3 text-center text-gray-700 dark:text-gray-300">{{ item.nomor_urut }}</td>
              <td class="px-3 py-3 text-gray-900 dark:text-gray-100">{{ item.nama_kepala_rumah_tangga }}</td>
              <td class="px-3 py-3 text-center text-gray-700 dark:text-gray-300">{{ item.jumlah_anggota_rumah_tangga }}</td>
              <td class="px-3 py-3 text-center text-gray-700 dark:text-gray-300">{{ item.kerja_bakti }}</td>
              <td class="px-3 py-3 text-center text-gray-700 dark:text-gray-300">{{ item.rukun_kematian }}</td>
              <td class="px-3 py-3 text-center text-gray-700 dark:text-gray-300">{{ item.kegiatan_keagamaan }}</td>
              <td class="px-3 py-3 text-center text-gray-700 dark:text-gray-300">{{ item.jimpitan }}</td>
              <td class="px-3 py-3 text-center text-gray-700 dark:text-gray-300">{{ item.arisan }}</td>
              <td class="px-3 py-3 text-center text-gray-700 dark:text-gray-300">{{ item.lain_lain }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ item.keterangan || '-' }}</td>
            </tr>
            <tr v-if="props.catatanKeluargaItems.data.length === 0">
              <td colspan="10" class="px-3 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                Catatan Keluarga belum tersedia. Isi data di modul Data Warga dan Data Kegiatan Warga terlebih dahulu.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <PaginationBar
        :links="catatanKeluargaItems.links"
        :from="catatanKeluargaItems.from"
        :to="catatanKeluargaItems.to"
        :total="catatanKeluargaItems.total"
      />
    </CardBox>
  </SectionMain>
</template>
