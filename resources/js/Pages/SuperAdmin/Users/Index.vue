<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import ConfirmActionModal from '@/admin-one/components/ConfirmActionModal.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { formatAreaLabel, formatRoleList, formatScopeLabel } from '@/utils/roleLabelFormatter'
import { Link, router } from '@inertiajs/vue3'
import { mdiAccountMultiple } from '@mdi/js'
import { ref } from 'vue'

const props = defineProps({
  users: {
    type: Object,
    required: true,
  },
})

const deleteConfirmationMessage = 'Apakah Anda yakin ingin menghapus user ini?'
const isDeleteModalActive = ref(false)
const deletingId = ref(null)

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

</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiAccountMultiple" title="User Management" main />

    <CardBox>
      <div class="mb-4 flex items-center justify-between gap-4">
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Daftar User</h3>
        <Link
          href="/super-admin/users/create"
          class="inline-flex items-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700"
        >
          + Tambah User
        </Link>
      </div>

      <div class="overflow-x-auto">
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

      <div class="mt-5 flex flex-wrap items-center gap-2">
        <template v-for="(link, index) in users.links" :key="`page-${index}`">
          <span
            v-if="!link.url"
            class="rounded-md border border-gray-200 px-3 py-1.5 text-xs text-gray-400 dark:border-slate-700 dark:text-gray-500"
            v-html="link.label"
          />
          <Link
            v-else
            :href="link.url"
            class="rounded-md border px-3 py-1.5 text-xs"
            :class="link.active
              ? 'border-emerald-600 bg-emerald-600 text-white'
              : 'border-gray-200 text-gray-700 hover:bg-gray-100 dark:border-slate-700 dark:text-gray-300 dark:hover:bg-slate-800'"
            v-html="link.label"
          />
        </template>
      </div>
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
