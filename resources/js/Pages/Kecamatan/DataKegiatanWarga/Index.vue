<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { mdiClipboardList } from '@mdi/js'

const props = defineProps({
  recapItems: {
    type: Array,
    required: true,
  },
  kegiatanOptions: {
    type: Array,
    required: true,
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
})
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiClipboardList" title="Rekap Data Kegiatan Warga per Desa" main />

    <CardBox>
      <div class="mb-4 flex items-center justify-between gap-4">
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Daftar Rekapitulasi Kegiatan Warga</h3>
        <div class="flex items-center gap-2">
          <a
            href="/kecamatan/data-kegiatan-warga/report/pdf"
            target="_blank"
            rel="noopener"
            class="inline-flex items-center rounded-md border border-sky-300 px-4 py-2 text-sm font-medium text-sky-700 hover:bg-sky-50 dark:border-sky-900/50 dark:text-sky-300 dark:hover:bg-sky-900/20"
          >
            Cetak PDF
          </a>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full min-w-[1200px] text-xs">
          <thead class="border-b border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800/50">
            <tr class="text-left text-gray-600 dark:text-gray-300">
              <th rowspan="2" class="px-3 py-3 font-semibold border-r border-gray-200 dark:border-slate-700 text-center w-12">NO</th>
              <th rowspan="2" class="px-3 py-3 font-semibold border-r border-gray-200 dark:border-slate-700 min-w-[200px]">NAMA DESA</th>
              <th v-for="kegiatan in props.kegiatanOptions" :key="kegiatan" colspan="2" class="px-3 py-2 font-semibold border-b border-gray-200 dark:border-slate-700 text-center border-r last:border-r-0">
                {{ kegiatan }}
              </th>
            </tr>
            <tr class="text-left text-gray-600 dark:text-gray-300">
              <template v-for="(kegiatan, index) in props.kegiatanOptions" :key="`sub-${index}`">
                <th class="px-2 py-2 font-semibold text-center border-r border-gray-200 dark:border-slate-700">Aktivitas</th>
                <th class="px-2 py-2 font-semibold border-r border-gray-200 dark:border-slate-700 last:border-r-0">Keterangan</th>
              </template>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(item, index) in props.recapItems"
              :key="item.area_id"
              class="border-b border-gray-100 align-top dark:border-slate-800 hover:bg-gray-50 dark:hover:bg-slate-900/40"
            >
              <td class="px-3 py-3 text-center border-r border-gray-100 dark:border-slate-800">{{ index + 1 }}</td>
              <td class="px-3 py-3 font-medium text-gray-900 dark:text-gray-100 border-r border-gray-100 dark:border-slate-800">
                {{ item.nama_desa }}
              </td>
              <template v-for="activity in item.activities" :key="activity.kegiatan">
                <td class="px-2 py-3 text-center border-r border-gray-100 dark:border-slate-800">
                  <span 
                    :class="[
                      'inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium',
                      activity.aktivitas ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-400'
                    ]"
                  >
                    {{ activity.aktivitas ? 'Y' : 'T' }}
                  </span>
                </td>
                <td class="px-2 py-3 text-gray-700 dark:text-gray-300 border-r border-gray-100 dark:border-slate-800 last:border-r-0 italic text-[10px] max-w-[150px] truncate" :title="activity.keterangan">
                  {{ activity.keterangan }}
                </td>
              </template>
            </tr>
            <tr v-if="props.recapItems.length === 0">
              <td :colspan="2 + (props.kegiatanOptions.length * 2)" class="px-3 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                Data rekapitulasi desa belum tersedia.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </CardBox>
  </SectionMain>
</template>
