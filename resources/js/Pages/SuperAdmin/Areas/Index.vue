<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import PaginationBar from '@/admin-one/components/PaginationBar.vue'
import ResponsiveDataTable from '@/admin-one/components/ResponsiveDataTable.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { formatScopeLabel } from '@/utils/roleLabelFormatter'
import { Link, router } from '@inertiajs/vue3'
import { mdiMapMarker } from '@mdi/js'
import { computed } from 'vue'

const props = defineProps({
  areas: {
    type: Object,
    required: true,
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
})

const isResponsiveTableV2Enabled = computed(() => import.meta.env.VITE_UI_RESPONSIVE_TABLE_V2 !== 'false')
const perPage = computed(() => props.filters.per_page ?? 10)
const AREA_INDEX_PARTIAL_PROPS = Object.freeze([
  'areas',
  'filters',
])

const areaTableColumns = [
  { key: 'name', label: 'Nama Wilayah', mobileLabel: 'Wilayah' },
  { key: 'level', label: 'Level', mobileLabel: 'Level' },
  { key: 'parent', label: 'Induk', mobileLabel: 'Induk' },
  { key: 'chairperson_name', label: 'Ketua TP PKK', mobileLabel: 'Ketua' },
  { key: 'actions', label: 'Aksi', mobileLabel: 'Aksi', headerClass: 'w-32' },
]

const visitAreasIndex = (query) => {
  router.get('/super-admin/areas', query, {
    preserveScroll: true,
    preserveState: true,
    replace: true,
    only: AREA_INDEX_PARTIAL_PROPS,
  })
}

const updatePerPage = (event) => {
  const selectedPerPage = Number(event.target.value)

  visitAreasIndex({
    ...props.filters,
    page: 1,
    per_page: selectedPerPage,
  })
}

</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiMapMarker" title="Manajemen Wilayah" main />

    <CardBox>
      <div class="mb-4 flex items-center justify-between gap-4">
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Daftar Wilayah</h3>
        <div class="flex items-center gap-2">
          <label class="text-xs text-gray-600 dark:text-gray-300">
            Per halaman
            <select
              :value="perPage"
              class="ml-2 rounded-md border border-gray-300 px-2 py-1 text-xs dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
              @change="updatePerPage"
            >
              <option v-for="option in [10, 25, 50]" :key="`per-page-${option}`" :value="option">
                {{ option }}
              </option>
            </select>
          </label>
        </div>
      </div>

      <ResponsiveDataTable
        v-if="isResponsiveTableV2Enabled"
        :columns="areaTableColumns"
        :rows="areas.data"
        row-key="id"
        min-width-class="min-w-[760px]"
        empty-text="Data wilayah belum tersedia."
      >
        <template #cell-name="{ row }">
          <span class="font-medium text-gray-900 dark:text-gray-100">{{ row.name }}</span>
        </template>
        <template #cell-level="{ row }">
          {{ formatScopeLabel(row.level) }}
        </template>
        <template #cell-parent="{ row }">
          {{ row.parent?.name ?? '-' }}
        </template>
        <template #cell-chairperson_name="{ row }">
          {{ row.chairperson_name ?? '-' }}
        </template>
        <template #cell-actions="{ row }">
          <div class="flex flex-wrap items-center justify-end gap-2 lg:justify-start">
            <Link
              :href="`/super-admin/areas/${row.id}/edit`"
              class="inline-flex min-h-[44px] items-center rounded-md border border-amber-200 px-4 py-2 text-xs font-semibold text-amber-700 hover:bg-amber-50 dark:border-amber-900/50 dark:text-amber-300 dark:hover:bg-amber-900/20"
            >
              Edit Metadata
            </Link>
          </div>
        </template>
      </ResponsiveDataTable>

      <div v-else class="overflow-x-auto">
        <table class="w-full min-w-[760px] text-sm">
          <thead class="border-b border-gray-200 dark:border-slate-700">
            <tr class="text-left text-gray-600 dark:text-gray-300">
              <th class="px-3 py-3 font-semibold">Nama Wilayah</th>
              <th class="px-3 py-3 font-semibold">Level</th>
              <th class="px-3 py-3 font-semibold">Induk</th>
              <th class="px-3 py-3 font-semibold">Ketua TP PKK</th>
              <th class="px-3 py-3 font-semibold w-32">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="area in areas.data"
              :key="area.id"
              class="border-b border-gray-100 align-top dark:border-slate-800"
            >
              <td class="px-3 py-3 text-gray-900 dark:text-gray-100">{{ area.name }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ formatScopeLabel(area.level) }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ area.parent?.name ?? '-' }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ area.chairperson_name ?? '-' }}</td>
              <td class="px-3 py-3">
                <div class="flex items-center gap-2">
                  <Link
                    :href="`/super-admin/areas/${area.id}/edit`"
                    class="inline-flex rounded-md border border-amber-200 px-3 py-1.5 text-xs font-semibold text-amber-700 hover:bg-amber-50 dark:border-amber-900/50 dark:text-amber-300 dark:hover:bg-amber-900/20"
                  >
                    Edit
                  </Link>
                </div>
              </td>
            </tr>
            <tr v-if="areas.data.length === 0">
              <td colspan="5" class="px-3 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                Data wilayah belum tersedia.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <PaginationBar
        :links="areas.links"
        :from="areas.from"
        :to="areas.to"
        :total="areas.total"
        :only="AREA_INDEX_PARTIAL_PROPS"
        :replace="true"
      />
    </CardBox>
  </SectionMain>
</template>
