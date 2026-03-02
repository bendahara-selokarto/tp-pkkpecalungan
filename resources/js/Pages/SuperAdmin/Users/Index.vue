<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import ConfirmActionModal from '@/admin-one/components/ConfirmActionModal.vue'
import PaginationBar from '@/admin-one/components/PaginationBar.vue'
import ResponsiveDataTable from '@/admin-one/components/ResponsiveDataTable.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { formatAreaLabel, formatRoleList, formatScopeLabel } from '@/utils/roleLabelFormatter'
import { Link, router } from '@inertiajs/vue3'
import { mdiAccountMultiple } from '@mdi/js'
import { computed, ref } from 'vue'

const props = defineProps({
  users: {
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

const deleteConfirmationMessage = 'Apakah Anda yakin ingin menghapus user ini?'
const isDeleteModalActive = ref(false)
const deletingId = ref(null)
const isResponsiveTableV2Enabled = computed(() => import.meta.env.VITE_UI_RESPONSIVE_TABLE_V2 !== 'false')
const perPage = computed(() => props.filters.per_page ?? 10)

const userTableColumns = [
  { key: 'name', label: 'Nama', mobileLabel: 'Nama' },
  { key: 'email', label: 'Email', mobileLabel: 'Email' },
  { key: 'roles', label: 'Role', mobileLabel: 'Role' },
  { key: 'scope', label: 'Scope', mobileLabel: 'Scope' },
  { key: 'area', label: 'Wilayah', mobileLabel: 'Wilayah' },
  { key: 'actions', label: 'Aksi', mobileLabel: 'Aksi', headerClass: 'w-44' },
]

const deleteUser = (userId) => {
  deletingId.value = userId
  isDeleteModalActive.value = true
}

const confirmDelete = () => {
  if (deletingId.value === null) {
    return
  }

  router.delete(`/super-admin/users/${deletingId.value}`, {
    onFinish: () => {
      isDeleteModalActive.value = false
      deletingId.value = null
    },
  })
}

const cancelDelete = () => {
  isDeleteModalActive.value = false
  deletingId.value = null
}

const updatePerPage = (event) => {
  const selectedPerPage = Number(event.target.value)

  router.get('/super-admin/users', { per_page: selectedPerPage }, {
    preserveScroll: true,
    preserveState: true,
    replace: true,
  })
}

</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiAccountMultiple" title="User Management" main />

    <CardBox>
      <div class="mb-4 flex items-center justify-between gap-4">
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Daftar User</h3>
        <div class="flex items-center gap-2">
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
          <Link
            href="/super-admin/users/create"
            class="inline-flex items-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700"
          >
            + Tambah User
          </Link>
        </div>
      </div>

      <ResponsiveDataTable
        v-if="isResponsiveTableV2Enabled"
        :columns="userTableColumns"
        :rows="users.data"
        row-key="id"
        min-width-class="min-w-[760px]"
        empty-text="Data user belum tersedia."
      >
        <template #cell-name="{ row }">
          <span class="font-medium text-gray-900 dark:text-gray-100">{{ row.name }}</span>
        </template>
        <template #cell-roles="{ row }">
          {{ formatRoleList(row.roles) }}
        </template>
        <template #cell-scope="{ row }">
          {{ formatScopeLabel(row.scope) }}
        </template>
        <template #cell-area="{ row }">
          {{ formatAreaLabel(row.area) }}
        </template>
        <template #cell-actions="{ row }">
          <div class="flex flex-wrap items-center justify-end gap-2 lg:justify-start">
            <Link
              :href="`/super-admin/users/${row.id}/edit`"
              class="inline-flex min-h-[44px] items-center rounded-md border border-amber-200 px-4 py-2 text-xs font-semibold text-amber-700 hover:bg-amber-50 dark:border-amber-900/50 dark:text-amber-300 dark:hover:bg-amber-900/20"
            >
              Edit
            </Link>
            <button
              type="button"
              class="inline-flex min-h-[44px] items-center rounded-md border border-rose-200 px-4 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-50 dark:border-rose-900/50 dark:text-rose-300 dark:hover:bg-rose-900/20"
              @click="deleteUser(row.id)"
            >
              Hapus
            </button>
          </div>
        </template>
      </ResponsiveDataTable>

      <div v-else class="overflow-x-auto">
        <table class="w-full min-w-[760px] text-sm">
          <thead class="border-b border-gray-200 dark:border-slate-700">
            <tr class="text-left text-gray-600 dark:text-gray-300">
              <th class="px-3 py-3 font-semibold">Nama</th>
              <th class="px-3 py-3 font-semibold">Email</th>
              <th class="px-3 py-3 font-semibold">Role</th>
              <th class="px-3 py-3 font-semibold">Scope</th>
              <th class="px-3 py-3 font-semibold">Wilayah</th>
              <th class="px-3 py-3 font-semibold w-44">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="user in users.data"
              :key="user.id"
              class="border-b border-gray-100 align-top dark:border-slate-800"
            >
              <td class="px-3 py-3 text-gray-900 dark:text-gray-100">{{ user.name }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ user.email }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ formatRoleList(user.roles) }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ formatScopeLabel(user.scope) }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ formatAreaLabel(user.area) }}</td>
              <td class="px-3 py-3">
                <div class="flex items-center gap-2">
                  <Link
                    :href="`/super-admin/users/${user.id}/edit`"
                    class="inline-flex rounded-md border border-amber-200 px-3 py-1.5 text-xs font-semibold text-amber-700 hover:bg-amber-50 dark:border-amber-900/50 dark:text-amber-300 dark:hover:bg-amber-900/20"
                  >
                    Edit
                  </Link>
                  <button
                    type="button"
                    class="inline-flex rounded-md border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-50 dark:border-rose-900/50 dark:text-rose-300 dark:hover:bg-rose-900/20"
                    @click="deleteUser(user.id)"
                  >
                    Hapus
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="users.data.length === 0">
              <td colspan="6" class="px-3 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                Data user belum tersedia.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <PaginationBar :links="users.links" :from="users.from" :to="users.to" :total="users.total" />
    </CardBox>

    <ConfirmActionModal
      v-model="isDeleteModalActive"
      title="Konfirmasi Hapus"
      :message="deleteConfirmationMessage"
      confirm-label="Ya, Hapus"
      @confirm="confirmDelete"
      @cancel="cancelDelete"
    />
  </SectionMain>
</template>
