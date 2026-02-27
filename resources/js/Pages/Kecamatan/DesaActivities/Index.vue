<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import PaginationBar from '@/admin-one/components/PaginationBar.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { formatDateForDisplay } from '@/utils/dateFormatter'
import { Link, router, usePage } from '@inertiajs/vue3'
import { mdiClipboardList } from '@mdi/js'
import { computed, ref } from 'vue'

const props = defineProps({
  activities: {
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

const page = usePage()
const formatDate = (value) => formatDateForDisplay(value)
const perPage = computed(() => props.filters.per_page ?? 10)
const selectedCakupan = ref('desa')
const userRoles = computed(() => page.props.auth?.user?.roles ?? [])
const isKecamatanSekretaris = computed(() => userRoles.value.includes('kecamatan-sekretaris'))

const pindahCakupan = (event) => {
  const value = String(event.target.value || 'desa')
  selectedCakupan.value = value

  if (value === 'kecamatan') {
    router.get('/kecamatan/activities', { per_page: perPage.value }, {
      preserveScroll: true,
      preserveState: false,
      replace: true,
    })
    return
  }

  router.get('/kecamatan/desa-activities', { per_page: perPage.value }, {
    preserveScroll: true,
    preserveState: true,
    replace: true,
  })
}

const updatePerPage = (event) => {
  const selectedPerPage = Number(event.target.value)

  router.get('/kecamatan/desa-activities', { per_page: selectedPerPage }, {
    preserveScroll: true,
    preserveState: true,
    replace: true,
  })
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiClipboardList" title="Kegiatan Desa" main />

    <CardBox>
      <div class="mb-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
          <div v-if="isKecamatanSekretaris" class="flex flex-wrap items-center gap-4 text-sm text-gray-700 dark:text-gray-200">
            <span class="font-medium">Cakupan Data</span>
            <label class="inline-flex items-center gap-2">
              <input
                v-model="selectedCakupan"
                type="radio"
                name="cakupan-kegiatan-kecamatan"
                value="kecamatan"
                class="h-4 w-4 border-gray-300 text-emerald-600 focus:ring-emerald-500 dark:border-slate-600 dark:bg-slate-900"
                @change="pindahCakupan"
              >
              Kecamatan
            </label>
            <label class="inline-flex items-center gap-2">
              <input
                v-model="selectedCakupan"
                type="radio"
                name="cakupan-kegiatan-kecamatan"
                value="desa"
                class="h-4 w-4 border-gray-300 text-emerald-600 focus:ring-emerald-500 dark:border-slate-600 dark:bg-slate-900"
                @change="pindahCakupan"
              >
              Desa (Monitoring)
            </label>
          </div>
          <a
            v-else
            href="/kecamatan/activities"
            class="text-sm font-medium text-emerald-700 hover:text-emerald-800 dark:text-emerald-400 dark:hover:text-emerald-300"
          >
            Lihat Kegiatan Kecamatan
          </a>

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
              v-for="item in activities.data"
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
            <tr v-if="activities.data.length === 0">
              <td colspan="6" class="px-3 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                Belum ada kegiatan desa pada kecamatan ini.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <PaginationBar
        :links="activities.links"
        :from="activities.from"
        :to="activities.to"
        :total="activities.total"
      />
    </CardBox>
  </SectionMain>
</template>
