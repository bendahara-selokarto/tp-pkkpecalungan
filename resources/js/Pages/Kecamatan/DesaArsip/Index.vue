<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import PaginationBar from '@/admin-one/components/PaginationBar.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { router } from '@inertiajs/vue3'
import { mdiArchive } from '@mdi/js'
import { computed, ref } from 'vue'

const props = defineProps({
  documents: {
    type: Object,
    required: true,
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
  desaOptions: {
    type: Array,
    default: () => ([]),
  },
  pagination: {
    type: Object,
    default: () => ({
      perPageOptions: [10, 25, 50],
    }),
  },
})

const perPage = computed(() => props.filters.per_page ?? 10)
const selectedCakupan = ref('desa')
const selectedDesaId = ref(props.filters.desa_id ? String(props.filters.desa_id) : '')
const searchKeyword = ref(props.filters.q ?? '')

const buildFilterQuery = (customPerPage = perPage.value) => {
  const query = { per_page: customPerPage }

  if (selectedDesaId.value !== '') {
    query.desa_id = Number(selectedDesaId.value)
  }

  if (searchKeyword.value.trim() !== '') {
    query.q = searchKeyword.value.trim()
  }

  return query
}

const pindahCakupan = (event) => {
  const value = String(event.target.value || 'desa')
  selectedCakupan.value = value

  if (value === 'pribadi') {
    router.get('/arsip', {}, {
      preserveScroll: true,
      preserveState: false,
      replace: true,
    })
    return
  }

  router.get('/kecamatan/desa-arsip', buildFilterQuery(), {
    preserveScroll: true,
    preserveState: true,
    replace: true,
  })
}

const updatePerPage = (event) => {
  const selectedPerPage = Number(event.target.value)

  router.get('/kecamatan/desa-arsip', buildFilterQuery(selectedPerPage), {
    preserveScroll: true,
    preserveState: true,
    replace: true,
  })
}

const terapkanFilter = () => {
  router.get('/kecamatan/desa-arsip', buildFilterQuery(), {
    preserveScroll: true,
    preserveState: true,
    replace: true,
  })
}

const resetFilter = () => {
  selectedDesaId.value = ''
  searchKeyword.value = ''

  router.get('/kecamatan/desa-arsip', { per_page: perPage.value }, {
    preserveScroll: true,
    preserveState: true,
    replace: true,
  })
}

const formatBytes = (sizeInBytes) => {
  const size = Number(sizeInBytes)
  if (!Number.isFinite(size) || size < 1024) {
    return `${Math.max(0, Math.round(size))} B`
  }

  const kiloBytes = size / 1024
  if (kiloBytes < 1024) {
    return `${kiloBytes.toFixed(1)} KB`
  }

  const megaBytes = kiloBytes / 1024
  return `${megaBytes.toFixed(1)} MB`
}

const formatDateTime = (isoDateTime) => {
  if (!isoDateTime) {
    return '-'
  }

  const parsedDate = new Date(isoDateTime)
  if (Number.isNaN(parsedDate.getTime())) {
    return '-'
  }

  return new Intl.DateTimeFormat('id-ID', {
    dateStyle: 'medium',
    timeStyle: 'short',
  }).format(parsedDate)
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiArchive" title="Monitoring Arsip Desa" main />

    <CardBox>
      <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-700 dark:text-gray-200">
          <span class="font-medium">Cakupan Arsip</span>
          <label class="inline-flex items-center gap-2">
            <input
              v-model="selectedCakupan"
              type="radio"
              name="cakupan-arsip-monitoring"
              value="pribadi"
              class="h-4 w-4 border-gray-300 text-emerald-600 focus:ring-emerald-500 dark:border-slate-600 dark:bg-slate-900"
              @change="pindahCakupan"
            >
            Arsip Saya
          </label>
          <label class="inline-flex items-center gap-2">
            <input
              v-model="selectedCakupan"
              type="radio"
              name="cakupan-arsip-monitoring"
              value="desa"
              class="h-4 w-4 border-gray-300 text-emerald-600 focus:ring-emerald-500 dark:border-slate-600 dark:bg-slate-900"
              @change="pindahCakupan"
            >
            Desa (Monitoring)
          </label>
        </div>

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

      <div class="mt-4 grid gap-3 md:grid-cols-3">
        <div>
          <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">
            Desa
          </label>
          <select
            v-model="selectedDesaId"
            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
          >
            <option value="">Semua Desa</option>
            <option v-for="desa in desaOptions" :key="`desa-${desa.id}`" :value="String(desa.id)">
              {{ desa.name }}
            </option>
          </select>
        </div>

        <div class="md:col-span-2">
          <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">
            Kata Kunci
          </label>
          <input
            v-model="searchKeyword"
            type="text"
            placeholder="Cari judul arsip, file, atau pengunggah"
            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
            @keyup.enter="terapkanFilter"
          >
        </div>
      </div>

      <div class="mt-3 flex items-center gap-2">
        <button
          type="button"
          class="inline-flex rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700"
          @click="terapkanFilter"
        >
          Terapkan Filter
        </button>
        <button
          type="button"
          class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
          @click="resetFilter"
        >
          Reset
        </button>
      </div>

      <div class="mt-4 overflow-x-auto">
        <table class="w-full min-w-[920px] text-sm">
          <thead class="border-b border-gray-200 dark:border-slate-700">
            <tr class="text-left text-gray-600 dark:text-gray-300">
              <th class="px-3 py-3 font-semibold">Judul</th>
              <th class="px-3 py-3 font-semibold">File</th>
              <th class="px-3 py-3 font-semibold">Desa</th>
              <th class="px-3 py-3 font-semibold">Pengunggah</th>
              <th class="px-3 py-3 font-semibold">Diperbarui</th>
              <th class="w-32 px-3 py-3 font-semibold">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="document in documents.data"
              :key="document.id"
              class="border-b border-gray-100 align-top dark:border-slate-800"
            >
              <td class="px-3 py-3 text-gray-900 dark:text-gray-100">
                <p class="font-medium">{{ document.title }}</p>
                <p v-if="document.description" class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                  {{ document.description }}
                </p>
              </td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">
                <p>{{ document.original_name }}</p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ formatBytes(document.size_bytes) }}</p>
              </td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ document.area?.name || '-' }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ document.creator?.name || '-' }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ formatDateTime(document.updated_at) }}</td>
              <td class="px-3 py-3">
                <a
                  :href="document.download_url"
                  class="inline-flex rounded-md border border-sky-200 px-3 py-1.5 text-xs font-semibold text-sky-700 hover:bg-sky-50 dark:border-sky-900/50 dark:text-sky-300 dark:hover:bg-sky-900/20"
                >
                  Unduh
                </a>
              </td>
            </tr>
            <tr v-if="documents.data.length === 0">
              <td colspan="6" class="px-3 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                Belum ada arsip desa di wilayah kecamatan ini.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <PaginationBar
        :links="documents.links"
        :from="documents.from"
        :to="documents.to"
        :total="documents.total"
      />
    </CardBox>
  </SectionMain>
</template>
