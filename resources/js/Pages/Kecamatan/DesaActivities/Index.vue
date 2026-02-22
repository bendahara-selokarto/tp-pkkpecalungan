<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { formatDateForDisplay } from '@/utils/dateFormatter'
import { Link } from '@inertiajs/vue3'
import { mdiClipboardList } from '@mdi/js'

defineProps({
  activities: {
    type: Array,
    required: true,
  },
})

const formatDate = (value) => formatDateForDisplay(value)
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiClipboardList" title="Activities Desa" main />

    <CardBox>
      <div class="mb-4">
        <Link
          href="/kecamatan/activities"
          class="text-sm font-medium text-emerald-700 hover:text-emerald-800 dark:text-emerald-400 dark:hover:text-emerald-300"
        >
          Lihat Kegiatan Kecamatan
        </Link>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full min-w-[900px] text-sm">
          <thead class="border-b border-gray-200 dark:border-slate-700">
            <tr class="text-left text-gray-600 dark:text-gray-300">
              <th class="px-3 py-3 font-semibold">Judul</th>
              <th class="px-3 py-3 font-semibold">Desa</th>
              <th class="px-3 py-3 font-semibold">Tanggal</th>
              <th class="px-3 py-3 font-semibold">Status</th>
              <th class="px-3 py-3 font-semibold">Dibuat Oleh</th>
              <th class="px-3 py-3 font-semibold w-24">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="item in activities"
              :key="item.id"
              class="border-b border-gray-100 align-top dark:border-slate-800"
            >
              <td class="px-3 py-3 text-gray-900 dark:text-gray-100">{{ item.title }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ item.area?.name || '-' }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ formatDate(item.activity_date) }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ item.status }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ item.creator?.name || '-' }}</td>
              <td class="px-3 py-3">
                <Link
                  :href="`/kecamatan/desa-activities/${item.id}`"
                  class="inline-flex rounded-md border border-sky-200 px-3 py-1.5 text-xs font-semibold text-sky-700 hover:bg-sky-50 dark:border-sky-900/50 dark:text-sky-300 dark:hover:bg-sky-900/20"
                >
                  Lihat
                </Link>
              </td>
            </tr>
            <tr v-if="activities.length === 0">
              <td colspan="6" class="px-3 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                Belum ada kegiatan desa pada kecamatan ini.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </CardBox>
  </SectionMain>
</template>
