<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { mdiBookOpenVariant } from '@mdi/js'

const props = defineProps({
  catatanKeluargaItems: {
    type: Array,
    required: true,
  },
})
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiBookOpenVariant" title="Catatan Keluarga Kecamatan" main />

    <CardBox>
      <div class="mb-4 flex flex-wrap items-center justify-end gap-2">
        <a
          href="/kecamatan/catatan-keluarga/report/pdf"
          target="_blank"
          rel="noopener"
          class="inline-flex items-center rounded-md border border-sky-300 px-4 py-2 text-sm font-medium text-sky-700 hover:bg-sky-50 dark:border-sky-900/50 dark:text-sky-300 dark:hover:bg-sky-900/20"
        >
          Cetak 4.15
        </a>
        <a
          href="/kecamatan/catatan-keluarga/rekap-dasa-wisma/report/pdf"
          target="_blank"
          rel="noopener"
          class="inline-flex items-center rounded-md border border-emerald-300 px-4 py-2 text-sm font-medium text-emerald-700 hover:bg-emerald-50 dark:border-emerald-900/50 dark:text-emerald-300 dark:hover:bg-emerald-900/20"
        >
          Cetak 4.16a
        </a>
        <a
          href="/kecamatan/catatan-keluarga/rekap-pkk-rt/report/pdf"
          target="_blank"
          rel="noopener"
          class="inline-flex items-center rounded-md border border-indigo-300 px-4 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-50 dark:border-indigo-900/50 dark:text-indigo-300 dark:hover:bg-indigo-900/20"
        >
          Cetak 4.16b
        </a>
        <a
          href="/kecamatan/catatan-keluarga/catatan-pkk-rw/report/pdf"
          target="_blank"
          rel="noopener"
          class="inline-flex items-center rounded-md border border-amber-300 px-4 py-2 text-sm font-medium text-amber-700 hover:bg-amber-50 dark:border-amber-900/50 dark:text-amber-300 dark:hover:bg-amber-900/20"
        >
          Cetak 4.16c
        </a>
        <a
          href="/kecamatan/catatan-keluarga/rekap-rw/report/pdf"
          target="_blank"
          rel="noopener"
          class="inline-flex items-center rounded-md border border-rose-300 px-4 py-2 text-sm font-medium text-rose-700 hover:bg-rose-50 dark:border-rose-900/50 dark:text-rose-300 dark:hover:bg-rose-900/20"
        >
          Cetak 4.16d
        </a>
        <a
          href="/kecamatan/catatan-keluarga/tp-pkk-desa-kelurahan/report/pdf"
          target="_blank"
          rel="noopener"
          class="inline-flex items-center rounded-md border border-fuchsia-300 px-4 py-2 text-sm font-medium text-fuchsia-700 hover:bg-fuchsia-50 dark:border-fuchsia-900/50 dark:text-fuchsia-300 dark:hover:bg-fuchsia-900/20"
        >
          Cetak 4.17a
        </a>
        <a
          href="/kecamatan/catatan-keluarga/tp-pkk-kecamatan/report/pdf"
          target="_blank"
          rel="noopener"
          class="inline-flex items-center rounded-md border border-violet-300 px-4 py-2 text-sm font-medium text-violet-700 hover:bg-violet-50 dark:border-violet-900/50 dark:text-violet-300 dark:hover:bg-violet-900/20"
        >
          Cetak 4.17b
        </a>
        <a
          href="/kecamatan/catatan-keluarga/tp-pkk-kabupaten-kota/report/pdf"
          target="_blank"
          rel="noopener"
          class="inline-flex items-center rounded-md border border-cyan-300 px-4 py-2 text-sm font-medium text-cyan-700 hover:bg-cyan-50 dark:border-cyan-900/50 dark:text-cyan-300 dark:hover:bg-cyan-900/20"
        >
          Cetak 4.17c
        </a>
        <a
          href="/kecamatan/catatan-keluarga/tp-pkk-provinsi/report/pdf"
          target="_blank"
          rel="noopener"
          class="inline-flex items-center rounded-md border border-teal-300 px-4 py-2 text-sm font-medium text-teal-700 hover:bg-teal-50 dark:border-teal-900/50 dark:text-teal-300 dark:hover:bg-teal-900/20"
        >
          Cetak 4.17d
        </a>
        <a
          href="/kecamatan/catatan-keluarga/rekap-ibu-hamil-dasawisma/report/pdf"
          target="_blank"
          rel="noopener"
          class="inline-flex items-center rounded-md border border-lime-300 px-4 py-2 text-sm font-medium text-lime-700 hover:bg-lime-50 dark:border-lime-900/50 dark:text-lime-300 dark:hover:bg-lime-900/20"
        >
          Cetak 4.18a
        </a>
        <a
          href="/kecamatan/catatan-keluarga/rekap-ibu-hamil-pkk-rt/report/pdf"
          target="_blank"
          rel="noopener"
          class="inline-flex items-center rounded-md border border-orange-300 px-4 py-2 text-sm font-medium text-orange-700 hover:bg-orange-50 dark:border-orange-900/50 dark:text-orange-300 dark:hover:bg-orange-900/20"
        >
          Cetak 4.18b
        </a>
        <a
          href="/kecamatan/catatan-keluarga/rekap-ibu-hamil-pkk-rw/report/pdf"
          target="_blank"
          rel="noopener"
          class="inline-flex items-center rounded-md border border-yellow-300 px-4 py-2 text-sm font-medium text-yellow-700 hover:bg-yellow-50 dark:border-yellow-900/50 dark:text-yellow-300 dark:hover:bg-yellow-900/20"
        >
          Cetak 4.18c
        </a>
        <a
          href="/kecamatan/catatan-keluarga/rekap-ibu-hamil-pkk-dusun-lingkungan/report/pdf"
          target="_blank"
          rel="noopener"
          class="inline-flex items-center rounded-md border border-red-300 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-50 dark:border-red-900/50 dark:text-red-300 dark:hover:bg-red-900/20"
        >
          Cetak 4.18d
        </a>
        <a
          href="/kecamatan/catatan-keluarga/rekap-ibu-hamil-tp-pkk-kecamatan/report/pdf"
          target="_blank"
          rel="noopener"
          class="inline-flex items-center rounded-md border border-blue-300 px-4 py-2 text-sm font-medium text-blue-700 hover:bg-blue-50 dark:border-blue-900/50 dark:text-blue-300 dark:hover:bg-blue-900/20"
        >
          Cetak 4.19b
        </a>
        <a
          href="/kecamatan/catatan-keluarga/data-umum-pkk/report/pdf"
          target="_blank"
          rel="noopener"
          class="inline-flex items-center rounded-md border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-900/20"
        >
          Cetak 4.20a
        </a>
        <a
          href="/kecamatan/catatan-keluarga/data-umum-pkk-kecamatan/report/pdf"
          target="_blank"
          rel="noopener"
          class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-900/20"
        >
          Cetak 4.20b
        </a>
        <a
          href="/kecamatan/catatan-keluarga/data-kegiatan-pkk-pokja-iii/report/pdf"
          target="_blank"
          rel="noopener"
          class="inline-flex items-center rounded-md border border-emerald-400 px-4 py-2 text-sm font-medium text-emerald-700 hover:bg-emerald-50 dark:border-emerald-700 dark:text-emerald-300 dark:hover:bg-emerald-900/20"
        >
          Cetak 4.23
        </a>
        <a
          href="/kecamatan/catatan-keluarga/data-kegiatan-pkk-pokja-iv/report/pdf"
          target="_blank"
          rel="noopener"
          class="inline-flex items-center rounded-md border border-cyan-400 px-4 py-2 text-sm font-medium text-cyan-700 hover:bg-cyan-50 dark:border-cyan-700 dark:text-cyan-300 dark:hover:bg-cyan-900/20"
        >
          Cetak 4.24
        </a>
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
              v-for="item in props.catatanKeluargaItems"
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
            <tr v-if="props.catatanKeluargaItems.length === 0">
              <td colspan="10" class="px-3 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                Catatan Keluarga belum tersedia. Isi data di modul Data Warga dan Data Kegiatan Warga terlebih dahulu.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </CardBox>
  </SectionMain>
</template>
