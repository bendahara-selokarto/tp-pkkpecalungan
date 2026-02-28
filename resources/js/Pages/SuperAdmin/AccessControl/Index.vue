<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link, router } from '@inertiajs/vue3'
import { mdiShieldCheckOutline } from '@mdi/js'
import { computed, reactive } from 'vue'

const props = defineProps({
  filters: {
    type: Object,
    required: true,
  },
  scopeOptions: {
    type: Array,
    required: true,
  },
  roleOptions: {
    type: Array,
    required: true,
  },
  modeOptions: {
    type: Array,
    required: true,
  },
  rows: {
    type: Array,
    required: true,
  },
  summary: {
    type: Object,
    required: true,
  },
})

const filterForm = reactive({
  scope: props.filters.scope ?? '',
  role: props.filters.role ?? '',
  mode: props.filters.mode ?? '',
})

const filteredRoleOptions = computed(() => {
  if (!filterForm.scope) {
    return props.roleOptions
  }

  return props.roleOptions.filter((option) => (option.scopes ?? []).includes(filterForm.scope))
})

const buildQuery = () => {
  const query = {}

  if (filterForm.scope) {
    query.scope = filterForm.scope
  }

  if (filterForm.role) {
    query.role = filterForm.role
  }

  if (filterForm.mode) {
    query.mode = filterForm.mode
  }

  return query
}

const applyFilters = () => {
  router.get('/super-admin/access-control', buildQuery(), {
    preserveScroll: true,
    preserveState: true,
    replace: true,
  })
}

const resetFilters = () => {
  filterForm.scope = ''
  filterForm.role = ''
  filterForm.mode = ''
  applyFilters()
}

const modeBadgeClass = (mode) => {
  if (mode === 'read-write') {
    return 'border-emerald-200 text-emerald-700 dark:border-emerald-900/50 dark:text-emerald-300'
  }

  if (mode === 'read-only') {
    return 'border-amber-200 text-amber-700 dark:border-amber-900/50 dark:text-amber-300'
  }

  return 'border-slate-300 text-slate-700 dark:border-slate-700 dark:text-slate-300'
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiShieldCheckOutline" title="Management Ijin Akses" main />

    <CardBox class="mb-4">
      <div class="mb-4">
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Matriks Akses Modul dan Group Role</h3>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
          Halaman ini read-only untuk observasi keputusan desain sebelum aktivasi perubahan ijin akses.
        </p>
      </div>

      <div class="grid gap-3 md:grid-cols-3 xl:grid-cols-6">
        <div class="rounded border border-slate-200 px-3 py-2 dark:border-slate-700">
          <p class="text-[11px] uppercase tracking-wide text-slate-500 dark:text-slate-400">Total Baris</p>
          <p class="mt-1 text-lg font-semibold text-slate-800 dark:text-slate-100">{{ summary.total_rows }}</p>
        </div>
        <div class="rounded border border-slate-200 px-3 py-2 dark:border-slate-700">
          <p class="text-[11px] uppercase tracking-wide text-slate-500 dark:text-slate-400">Terfilter</p>
          <p class="mt-1 text-lg font-semibold text-slate-800 dark:text-slate-100">{{ summary.filtered_rows }}</p>
        </div>
        <div class="rounded border border-emerald-200 px-3 py-2 dark:border-emerald-900/50">
          <p class="text-[11px] uppercase tracking-wide text-emerald-700 dark:text-emerald-300">Baca Tulis</p>
          <p class="mt-1 text-lg font-semibold text-emerald-700 dark:text-emerald-300">{{ summary.read_write }}</p>
        </div>
        <div class="rounded border border-amber-200 px-3 py-2 dark:border-amber-900/50">
          <p class="text-[11px] uppercase tracking-wide text-amber-700 dark:text-amber-300">Baca Saja</p>
          <p class="mt-1 text-lg font-semibold text-amber-700 dark:text-amber-300">{{ summary.read_only }}</p>
        </div>
        <div class="rounded border border-slate-300 px-3 py-2 dark:border-slate-700">
          <p class="text-[11px] uppercase tracking-wide text-slate-600 dark:text-slate-300">Tidak Tampil</p>
          <p class="mt-1 text-lg font-semibold text-slate-700 dark:text-slate-200">{{ summary.hidden }}</p>
        </div>
        <div class="flex items-end justify-end">
          <Link
            href="/super-admin/users"
            class="inline-flex items-center rounded-md border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
          >
            Kembali ke User
          </Link>
        </div>
      </div>
    </CardBox>

    <CardBox class="mb-4">
      <form class="grid gap-3 md:grid-cols-2 xl:grid-cols-5" @submit.prevent="applyFilters">
        <label class="text-sm">
          <span class="mb-1 block text-slate-700 dark:text-slate-200">Scope</span>
          <select
            v-model="filterForm.scope"
            class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
          >
            <option value="">Semua Scope</option>
            <option v-for="option in scopeOptions" :key="`scope-${option.value}`" :value="option.value">
              {{ option.label }}
            </option>
          </select>
        </label>

        <label class="text-sm">
          <span class="mb-1 block text-slate-700 dark:text-slate-200">Role</span>
          <select
            v-model="filterForm.role"
            class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
          >
            <option value="">Semua Role</option>
            <option v-for="option in filteredRoleOptions" :key="`role-${option.value}`" :value="option.value">
              {{ option.label }}
            </option>
          </select>
        </label>

        <label class="text-sm">
          <span class="mb-1 block text-slate-700 dark:text-slate-200">Mode Akses</span>
          <select
            v-model="filterForm.mode"
            class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
          >
            <option value="">Semua Mode</option>
            <option v-for="option in modeOptions" :key="`mode-${option.value}`" :value="option.value">
              {{ option.label }}
            </option>
          </select>
        </label>

        <div class="flex items-end gap-2 md:col-span-2 xl:col-span-2">
          <button
            type="submit"
            class="inline-flex rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700"
          >
            Terapkan Filter
          </button>
          <button
            type="button"
            class="inline-flex rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
            @click="resetFilters"
          >
            Reset
          </button>
        </div>
      </form>
    </CardBox>

    <CardBox>
      <div class="overflow-x-auto">
        <table class="w-full min-w-[980px] text-sm">
          <thead class="border-b border-slate-200 dark:border-slate-700">
            <tr class="text-left text-slate-600 dark:text-slate-300">
              <th class="px-3 py-3 font-semibold">Scope</th>
              <th class="px-3 py-3 font-semibold">Role</th>
              <th class="px-3 py-3 font-semibold">Group Role</th>
              <th class="px-3 py-3 font-semibold">Modul</th>
              <th class="px-3 py-3 font-semibold">Mode Efektif</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="row in rows"
              :key="row.id"
              class="border-b border-slate-100 align-top dark:border-slate-800"
            >
              <td class="px-3 py-3 text-slate-700 dark:text-slate-300">{{ row.scope_label }}</td>
              <td class="px-3 py-3 text-slate-700 dark:text-slate-300">
                <p class="font-medium text-slate-900 dark:text-slate-100">{{ row.role_label }}</p>
                <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">{{ row.role }}</p>
              </td>
              <td class="px-3 py-3 text-slate-700 dark:text-slate-300">
                <p class="font-medium text-slate-900 dark:text-slate-100">{{ row.group_label }}</p>
                <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">{{ row.group }}</p>
              </td>
              <td class="px-3 py-3 text-slate-700 dark:text-slate-300">
                <p class="font-medium text-slate-900 dark:text-slate-100">{{ row.module_label }}</p>
                <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">{{ row.module }}</p>
              </td>
              <td class="px-3 py-3">
                <span class="inline-flex rounded border px-2 py-1 text-xs font-semibold" :class="modeBadgeClass(row.mode)">
                  {{ row.mode_label }}
                </span>
              </td>
            </tr>
            <tr v-if="rows.length === 0">
              <td colspan="5" class="px-3 py-6 text-center text-sm text-slate-500 dark:text-slate-400">
                Tidak ada data untuk kombinasi filter saat ini.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </CardBox>
  </SectionMain>
</template>
