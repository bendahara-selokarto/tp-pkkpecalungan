<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { mdiAccountEdit } from '@mdi/js'
import { computed, watch } from 'vue'

const props = defineProps({
  user: {
    type: Object,
    required: true,
  },
  roles: {
    type: Array,
    required: true,
  },
  areas: {
    type: Array,
    required: true,
  },
})

const form = useForm({
  name: props.user.name ?? '',
  email: props.user.email ?? '',
  password: '',
  role: props.user.roles?.[0] ?? props.roles[0] ?? '',
  scope: props.user.scope ?? 'desa',
  area_id: props.user.area_id ?? '',
})

const scopeRoleMap = {
  desa: ['admin-desa'],
  kecamatan: ['admin-kecamatan', 'super-admin'],
}

const filteredRoles = computed(() => scopeRoleMap[form.scope] ?? [])
const filteredAreas = computed(() => props.areas.filter((area) => area.level === form.scope))

watch(
  () => form.scope,
  () => {
    if (!filteredRoles.value.includes(form.role)) {
      form.role = filteredRoles.value[0] ?? ''
    }

    if (!filteredAreas.value.some((area) => String(area.id) === String(form.area_id))) {
      form.area_id = ''
    }
  },
  { immediate: true },
)

const submit = () => {
  form.put(`/super-admin/users/${props.user.id}`)
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiAccountEdit" title="Edit User" main />

    <CardBox class="max-w-3xl">
      <form class="space-y-5" @submit.prevent="submit">
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama</label>
          <input
            v-model="form.name"
            type="text"
            class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
            required
          >
          <p v-if="form.errors.name" class="mt-1 text-xs text-rose-600">{{ form.errors.name }}</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
          <input
            v-model="form.email"
            type="email"
            class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
            required
          >
          <p v-if="form.errors.email" class="mt-1 text-xs text-rose-600">{{ form.errors.email }}</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Password (opsional)</label>
          <input
            v-model="form.password"
            type="password"
            class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
          >
          <p v-if="form.errors.password" class="mt-1 text-xs text-rose-600">{{ form.errors.password }}</p>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Role</label>
            <select
              v-model="form.role"
              class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
              required
            >
              <option v-for="role in filteredRoles" :key="role" :value="role">{{ role }}</option>
            </select>
            <p v-if="form.errors.role" class="mt-1 text-xs text-rose-600">{{ form.errors.role }}</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Scope</label>
            <select
              v-model="form.scope"
              class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
              required
            >
              <option value="kecamatan">Kecamatan</option>
              <option value="desa">Desa</option>
            </select>
            <p v-if="form.errors.scope" class="mt-1 text-xs text-rose-600">{{ form.errors.scope }}</p>
          </div>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Wilayah</label>
          <select
            v-model="form.area_id"
            class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
            required
          >
            <option value="">Pilih wilayah</option>
            <option v-for="area in filteredAreas" :key="area.id" :value="area.id">
              {{ area.level }} - {{ area.name }}
            </option>
          </select>
          <p v-if="form.errors.area_id" class="mt-1 text-xs text-rose-600">{{ form.errors.area_id }}</p>
        </div>

        <div class="flex items-center justify-end gap-2">
          <Link
            href="/super-admin/users"
            class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
          >
            Batal
          </Link>
          <button
            type="submit"
            class="inline-flex rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60"
            :disabled="form.processing"
          >
            Update
          </button>
        </div>
      </form>
    </CardBox>
  </SectionMain>
</template>
