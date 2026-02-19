<script setup>
import { Link, router, usePage } from '@inertiajs/vue3'
import { useDarkModeStore } from '@/admin-one/stores/darkMode'
import { computed, ref } from 'vue'

const page = usePage()
const darkModeStore = useDarkModeStore()

const sidebarOpen = ref(false)
const sidebarCollapsed = ref(localStorage.getItem('sidebar-collapsed') === '1')
const districtOpen = ref(
  page.url.startsWith('/kecamatan/activities')
    || page.url.startsWith('/kecamatan/inventaris')
    || page.url.startsWith('/kecamatan/bantuans')
    || page.url.startsWith('/kecamatan/anggota-pokja')
    || page.url.startsWith('/kecamatan/desa-activities'),
)

const user = computed(() => page.props.auth?.user ?? null)
const roles = computed(() => user.value?.roles ?? [])
const activeRoles = computed(() => roles.value.join(', ') || '-')
const userScope = computed(() => user.value?.scope ?? null)
const isDesaScope = computed(() => userScope.value === 'desa')
const isKecamatanScope = computed(() => userScope.value === 'kecamatan')

const hasRole = (role) => roles.value.includes(role)

const isActive = (prefix) => page.url.startsWith(prefix)

const primaryHref = computed(() =>
  hasRole('super-admin') ? '/super-admin/users' : '/dashboard',
)

const toggleCollapse = () => {
  sidebarCollapsed.value = !sidebarCollapsed.value
  localStorage.setItem('sidebar-collapsed', sidebarCollapsed.value ? '1' : '0')
}

const logout = () => {
  router.post('/logout')
}

const toggleTheme = () => {
  darkModeStore.set(null, true)
}
</script>

<template>
  <div class="min-h-screen bg-gray-50 text-slate-800 dark:bg-slate-900 dark:text-slate-100">
    <div
      v-show="sidebarOpen"
      class="fixed inset-0 z-30 bg-slate-900/60 lg:hidden"
      @click="sidebarOpen = false"
    />

    <header class="fixed inset-x-0 top-0 z-40 h-14 border-b border-slate-200 bg-white/95 backdrop-blur dark:border-slate-700 dark:bg-slate-800/95">
      <div class="h-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
        <div class="flex items-center gap-3 min-w-0">
          <button class="inline-flex items-center justify-center rounded-md p-2 text-slate-500 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700 lg:hidden" @click="sidebarOpen = !sidebarOpen">
            <span class="sr-only">Toggle sidebar</span>
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
          <button class="hidden lg:inline-flex items-center gap-2 rounded-md px-2.5 py-2 text-slate-500 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700" @click="toggleCollapse">
            <span class="sr-only">Collapse sidebar</span>
            <svg class="h-5 w-5 transition-transform" :class="{ 'rotate-180': sidebarCollapsed }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            <span class="text-xs font-medium">{{ sidebarCollapsed ? 'Expand' : 'Minimize' }}</span>
          </button>
          <Link :href="primaryHref" class="flex items-center gap-2 min-w-0">
            <img src="/images/pkk-logo.png" alt="Logo PKK" class="h-6 w-6 object-contain">
            <span class="text-sm font-semibold tracking-wide uppercase text-slate-700 dark:text-slate-100 truncate">
              {{ page.props.appName ?? 'Laravel' }}
            </span>
          </Link>
        </div>

        <div class="flex items-center gap-3">
          <span class="hidden sm:inline text-xs text-slate-500 dark:text-slate-300">{{ activeRoles }}</span>
          <button
            type="button"
            :class="{ 'transition-colors': !darkModeStore.isInProgress }"
            class="inline-flex items-center rounded-md border border-slate-300 px-2.5 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-100 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-700"
            @click="toggleTheme"
          >
            {{ darkModeStore.isEnabled ? 'Light mode' : 'Dark mode' }}
          </button>
          <span class="text-sm text-slate-700 dark:text-slate-200">{{ user?.name }}</span>
          <a href="/profile" class="text-sm text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white">Profile</a>
          <button type="button" class="text-sm text-rose-600 hover:text-rose-700 dark:text-rose-400" @click="logout">
            Log Out
          </button>
        </div>
      </div>
    </header>

    <aside :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full', sidebarCollapsed ? 'lg:w-20' : 'lg:w-64']" class="fixed inset-y-0 left-0 z-40 w-72 transform border-r border-slate-200 bg-white transition-all duration-200 ease-in-out dark:border-slate-700 dark:bg-slate-800 lg:translate-x-0">
      <div class="h-full flex flex-col">
        <div class="h-14 px-4 flex items-center justify-between border-b border-slate-200 dark:border-slate-700">
          <Link :href="primaryHref" :class="sidebarCollapsed ? 'justify-center w-full' : ''" class="flex items-center gap-2 min-w-0">
            <img src="/images/pkk-logo.png" alt="Logo PKK" class="h-7 w-7 object-contain">
            <span v-show="!sidebarCollapsed" class="text-sm font-semibold text-slate-700 dark:text-slate-100 truncate">
              {{ page.props.appName ?? 'Laravel' }}
            </span>
          </Link>
          <button class="rounded-md p-1 text-slate-500 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700 lg:hidden" @click="sidebarOpen = false">
            <span class="sr-only">Close sidebar</span>
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div v-show="!sidebarCollapsed" class="mx-4 mt-4 rounded-lg border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-900/60">
          <p class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">User</p>
          <p class="mt-1 text-sm font-semibold text-slate-800 dark:text-slate-100">{{ user?.name }}</p>
          <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ user?.email }}</p>
          <p class="mt-2 text-xs text-emerald-700 dark:text-emerald-400">{{ activeRoles }}</p>
        </div>

        <nav class="flex-1 overflow-y-auto p-4 space-y-5">
          <div class="space-y-1">
            <p v-show="!sidebarCollapsed" class="px-3 text-xs font-semibold uppercase tracking-wide text-slate-400">Main</p>

            <Link
              v-if="!hasRole('super-admin')"
              href="/dashboard"
              :class="[sidebarCollapsed ? 'justify-center' : '', isActive('/dashboard') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700']"
              class="flex items-center gap-3 rounded-md px-3 py-2 text-sm"
            >
              <span v-show="!sidebarCollapsed">Dashboard</span>
              <span v-show="sidebarCollapsed">D</span>
            </Link>

            <Link
              v-if="hasRole('super-admin')"
              href="/super-admin/users"
              :class="[sidebarCollapsed ? 'justify-center' : '', isActive('/super-admin/users') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700']"
              class="flex items-center gap-3 rounded-md px-3 py-2 text-sm"
            >
              <span v-show="!sidebarCollapsed">Management User</span>
              <span v-show="sidebarCollapsed">MU</span>
            </Link>
          </div>

          <div class="space-y-1">
            <p v-show="!sidebarCollapsed" class="px-3 text-xs font-semibold uppercase tracking-wide text-slate-400">Activity</p>

            <a
              v-if="isDesaScope"
              href="/desa/activities"
              :class="[sidebarCollapsed ? 'justify-center' : '', isActive('/desa/activities') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700']"
              class="flex items-center gap-3 rounded-md px-3 py-2 text-sm"
            >
              <span v-show="!sidebarCollapsed">Activities Desa</span>
              <span v-show="sidebarCollapsed">AD</span>
            </a>
            <a
              v-if="isDesaScope"
              href="/desa/inventaris"
              :class="[sidebarCollapsed ? 'justify-center' : '', isActive('/desa/inventaris') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700']"
              class="flex items-center gap-3 rounded-md px-3 py-2 text-sm"
            >
              <span v-show="!sidebarCollapsed">Inventaris Desa</span>
              <span v-show="sidebarCollapsed">ID</span>
            </a>
            <a
              v-if="isDesaScope"
              href="/desa/bantuans"
              :class="[sidebarCollapsed ? 'justify-center' : '', isActive('/desa/bantuans') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700']"
              class="flex items-center gap-3 rounded-md px-3 py-2 text-sm"
            >
              <span v-show="!sidebarCollapsed">Bantuan Desa</span>
              <span v-show="sidebarCollapsed">BD</span>
            </a>
            <a
              v-if="isDesaScope"
              href="/desa/anggota-pokja"
              :class="[sidebarCollapsed ? 'justify-center' : '', isActive('/desa/anggota-pokja') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700']"
              class="flex items-center gap-3 rounded-md px-3 py-2 text-sm"
            >
              <span v-show="!sidebarCollapsed">Anggota Pokja Desa</span>
              <span v-show="sidebarCollapsed">APD</span>
            </a>

            <button
              v-if="isKecamatanScope"
              type="button"
              :class="[sidebarCollapsed ? 'justify-center' : 'justify-between', isActive('/kecamatan/activities') || isActive('/kecamatan/inventaris') || isActive('/kecamatan/bantuans') || isActive('/kecamatan/anggota-pokja') || isActive('/kecamatan/desa-activities') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700']"
              class="w-full flex items-center rounded-md px-3 py-2 text-sm"
              @click="sidebarCollapsed ? (window.location.href = '/kecamatan/activities') : (districtOpen = !districtOpen)"
            >
              <span class="flex items-center gap-3">
                <span v-show="!sidebarCollapsed">Kecamatan</span>
                <span v-show="sidebarCollapsed">K</span>
              </span>
              <svg v-show="!sidebarCollapsed" class="h-4 w-4 transition-transform" :class="{ 'rotate-180': districtOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>

            <div v-show="districtOpen && !sidebarCollapsed" class="space-y-1 pl-4">
              <a
                href="/kecamatan/activities"
                :class="isActive('/kecamatan/activities') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700'"
                class="flex items-center gap-2 rounded-md px-3 py-2 text-sm"
              >
                Activities Kecamatan
              </a>
              <a
                href="/kecamatan/inventaris"
                :class="isActive('/kecamatan/inventaris') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700'"
                class="flex items-center gap-2 rounded-md px-3 py-2 text-sm"
              >
                Inventaris Kecamatan
              </a>
              <a
                href="/kecamatan/bantuans"
                :class="isActive('/kecamatan/bantuans') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700'"
                class="flex items-center gap-2 rounded-md px-3 py-2 text-sm"
              >
                Bantuan Kecamatan
              </a>
              <a
                href="/kecamatan/anggota-pokja"
                :class="isActive('/kecamatan/anggota-pokja') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700'"
                class="flex items-center gap-2 rounded-md px-3 py-2 text-sm"
              >
                Anggota Pokja Kecamatan
              </a>
              <a
                href="/kecamatan/desa-activities"
                :class="isActive('/kecamatan/desa-activities') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700'"
                class="flex items-center gap-2 rounded-md px-3 py-2 text-sm"
              >
                Activities Desa
              </a>
            </div>
          </div>

          <div class="space-y-1">
            <p v-show="!sidebarCollapsed" class="px-3 text-xs font-semibold uppercase tracking-wide text-slate-400">Account</p>
            <a
              href="/profile"
              :class="[sidebarCollapsed ? 'justify-center' : '', isActive('/profile') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700']"
              class="flex items-center gap-3 rounded-md px-3 py-2 text-sm"
            >
              <span v-show="!sidebarCollapsed">Profile</span>
              <span v-show="sidebarCollapsed">P</span>
            </a>
          </div>
        </nav>

        <div class="p-4 border-t border-slate-200 dark:border-slate-700">
          <button
            type="button"
            class="w-full rounded-md border border-rose-200 px-3 py-2 text-sm font-medium text-rose-600 hover:bg-rose-50 dark:border-rose-900/60 dark:text-rose-400 dark:hover:bg-rose-900/20"
            @click="logout"
          >
            <span v-show="!sidebarCollapsed">Log Out</span>
            <span v-show="sidebarCollapsed">LO</span>
          </button>
        </div>
      </div>
    </aside>

    <div :class="sidebarCollapsed ? 'lg:pl-20' : 'lg:pl-64'" class="pt-14 transition-all duration-200">
      <main class="px-4 sm:px-6 lg:px-8 py-6">
        <slot />
      </main>
    </div>
  </div>
</template>
