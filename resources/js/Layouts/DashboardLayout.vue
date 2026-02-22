<script setup>
import { Link, router, usePage } from '@inertiajs/vue3'
import { useDarkModeStore } from '@/admin-one/stores/darkMode'
import FlashMessageBar from '@/admin-one/components/FlashMessageBar.vue'
import { formatRoleList } from '@/utils/roleLabelFormatter'
import { computed, ref } from 'vue'

const page = usePage()
const darkModeStore = useDarkModeStore()

const sidebarOpen = ref(false)
const sidebarCollapsed = ref(localStorage.getItem('sidebar-collapsed') === '1')

const user = computed(() => page.props.auth?.user ?? null)
const roles = computed(() => user.value?.roles ?? [])
const activeRoles = computed(() => formatRoleList(roles.value))
const flash = computed(() => page.props.flash ?? {})
const userScope = computed(() => user.value?.scope ?? null)
const isDesaScope = computed(() => userScope.value === 'desa')
const isKecamatanScope = computed(() => userScope.value === 'kecamatan')

const hasRole = (role) => roles.value.includes(role)

const isActive = (prefix) => page.url.startsWith(prefix)
const isExternalItem = (item) => item.external === true
const isItemActive = (item) => !isExternalItem(item) && isActive(item.href)
const openExternal = (href) => {
  window.open(href, '_blank', 'noopener,noreferrer')
}

const officialReferenceItems = [
  {
    href: 'https://pubhtml5.com/zsnqq/vjcf/basic/101-150',
    label: 'Pedoman Domain Utama 101-150',
    external: true,
  },
  {
    href: 'https://pubhtml5.com/zsnqq/vjcf/basic/201-241',
    label: 'Pedoman Lanjutan 201-241',
    external: true,
  },
]

const buildScopedMenuGroups = (scope) => [
  {
    key: 'sekretaris-tpk',
    label: 'Sekretaris TPK',
    code: 'ST',
    items: [
      { href: `/${scope}/anggota-tim-penggerak`, label: 'Buku Daftar Anggota Tim Penggerak PKK' },
      { href: `/${scope}/kader-khusus`, label: 'Buku Daftar Kader Tim Penggerak PKK' },
      { href: `/${scope}/agenda-surat`, label: 'Buku Agenda Surat Masuk/Keluar' },
      { href: `/${scope}/bantuans`, label: 'Buku Keuangan' },
      { href: `/${scope}/inventaris`, label: 'Buku Inventaris' },
      { href: `/${scope}/activities`, label: 'Buku Kegiatan' },
      { href: `/${scope}/anggota-pokja`, label: 'Anggota Pokja' },
      { href: `/${scope}/prestasi-lomba`, label: 'Prestasi Lomba' },
    ],
  },
  {
    key: 'pokja-i',
    label: 'Pokja I',
    code: 'P1',
    items: [
      { href: `/${scope}/data-warga`, label: 'Daftar Warga TP PKK' },
      { href: `/${scope}/data-kegiatan-warga`, label: 'Data Kegiatan Warga' },
      { href: `/${scope}/bkl`, label: 'BKL' },
      { href: `/${scope}/bkr`, label: 'BKR' },
    ],
  },
  {
    key: 'pokja-ii',
    label: 'Pokja II',
    code: 'P2',
    items: [
      { href: `/${scope}/data-pelatihan-kader`, label: 'Data Pelatihan Kader' },
      { href: `/${scope}/taman-bacaan`, label: 'Data Isian Taman Bacaan/Perpustakaan' },
      { href: `/${scope}/koperasi`, label: 'Data Isian Koperasi' },
      { href: `/${scope}/kejar-paket`, label: 'Data Isian Kejar Paket/KF/PAUD' },
    ],
  },
  {
    key: 'pokja-iii',
    label: 'Pokja III',
    code: 'P3',
    items: [
      { href: `/${scope}/data-keluarga`, label: 'Data Keluarga' },
      { href: `/${scope}/data-industri-rumah-tangga`, label: 'Data Industri Rumah Tangga' },
      { href: `/${scope}/data-pemanfaatan-tanah-pekarangan-hatinya-pkk`, label: 'Data Pemanfaatan Tanah Pekarangan/HATINYA PKK' },
      { href: `/${scope}/warung-pkk`, label: 'Data Aset (Sarana) Desa/Kelurahan' },
    ],
  },
  {
    key: 'pokja-iv',
    label: 'Pokja IV',
    code: 'P4',
    items: [
      { href: `/${scope}/posyandu`, label: 'Data Isian Posyandu oleh TP PKK' },
      { href: `/${scope}/simulasi-penyuluhan`, label: 'Data Isian Kelompok Simulasi dan Penyuluhan' },
      { href: `/${scope}/catatan-keluarga`, label: 'Catatan Keluarga' },
      { href: `/${scope}/program-prioritas`, label: 'Program Prioritas' },
      { href: `/${scope}/pilot-project-naskah-pelaporan`, label: 'Naskah Pelaporan Pilot Project Pokja IV' },
      { href: `/${scope}/pilot-project-keluarga-sehat`, label: 'Laporan Pelaksanaan Pilot Project Gerakan Keluarga Sehat Tanggap dan Tangguh Bencana' },
    ],
  },
  {
    key: 'referensi',
    label: 'Referensi',
    code: 'REF',
    items: officialReferenceItems,
  },
]

const desaMenuGroups = buildScopedMenuGroups('desa')

const kecamatanMenuGroups = [
  ...buildScopedMenuGroups('kecamatan'),
  {
    key: 'monitoring',
    label: 'Monitoring Kecamatan',
    code: 'MON',
    items: [
      { href: '/kecamatan/desa-activities', label: 'Kegiatan Desa' },
    ],
  },
]

const buildGroupState = (groups) => groups.reduce((state, group) => {
  state[group.key] = group.items.some((item) => isItemActive(item))
  return state
}, {})

const desaGroupOpen = ref(buildGroupState(desaMenuGroups))
const kecamatanGroupOpen = ref(buildGroupState(kecamatanMenuGroups))

const isGroupActive = (group) => group.items.some((item) => isItemActive(item))

const openGroupPrimaryItem = (group) => {
  const firstItem = group.items[0]
  if (!firstItem) {
    return
  }

  if (isExternalItem(firstItem)) {
    openExternal(firstItem.href)
    return
  }

  window.location.href = firstItem.href
}

const isGroupOpen = (scope, key) => {
  if (scope === 'desa') {
    return !!desaGroupOpen.value[key]
  }

  return !!kecamatanGroupOpen.value[key]
}

const toggleGroup = (scope, group) => {
  if (sidebarCollapsed.value) {
    openGroupPrimaryItem(group)
    return
  }

  if (scope === 'desa') {
    desaGroupOpen.value[group.key] = !desaGroupOpen.value[group.key]
    return
  }

  kecamatanGroupOpen.value[group.key] = !kecamatanGroupOpen.value[group.key]
}

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

const pkkLogo = '/images/pkk-logo.png'

const hideBrokenImage = (event) => {
  event.target.style.display = 'none'
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
            <img :src="pkkLogo" alt="" aria-hidden="true" class="h-6 w-6 object-contain" @error="hideBrokenImage">
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
          <a href="/profile" class="text-sm text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white">Profil</a>
          <button type="button" class="text-sm text-rose-600 hover:text-rose-700 dark:text-rose-400" @click="logout">
            Keluar (Log Out)
          </button>
        </div>
      </div>
    </header>

    <aside :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full', sidebarCollapsed ? 'lg:w-20' : 'lg:w-64']" class="fixed inset-y-0 left-0 z-40 w-72 transform border-r border-slate-200 bg-white transition-all duration-200 ease-in-out dark:border-slate-700 dark:bg-slate-800 lg:translate-x-0">
      <div class="h-full flex flex-col">
        <div class="h-14 px-4 flex items-center justify-between border-b border-slate-200 dark:border-slate-700">
          <Link :href="primaryHref" :class="sidebarCollapsed ? 'justify-center w-full' : ''" class="flex items-center gap-2 min-w-0">
            <img :src="pkkLogo" alt="" aria-hidden="true" class="h-7 w-7 object-contain" @error="hideBrokenImage">
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
              <span v-show="!sidebarCollapsed">Manajemen User</span>
              <span v-show="sidebarCollapsed">MU</span>
            </Link>
          </div>

          <div class="space-y-1">
            <p v-show="!sidebarCollapsed" class="px-3 text-xs font-semibold uppercase tracking-wide text-slate-400">Menu Domain</p>

            <template v-if="isDesaScope">
              <div v-for="group in desaMenuGroups" :key="`desa-${group.key}`" class="space-y-1">
                <button
                  type="button"
                  :class="[sidebarCollapsed ? 'justify-center' : 'justify-between', isGroupActive(group) ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700']"
                  class="w-full flex items-center rounded-md px-3 py-2 text-sm"
                  @click="toggleGroup('desa', group)"
                >
                  <span class="flex items-center gap-3">
                    <span v-show="!sidebarCollapsed">{{ group.label }}</span>
                    <span v-show="sidebarCollapsed">{{ group.code }}</span>
                  </span>
                  <svg v-show="!sidebarCollapsed" class="h-4 w-4 transition-transform" :class="{ 'rotate-180': isGroupOpen('desa', group.key) }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </button>

                <div v-show="isGroupOpen('desa', group.key) && !sidebarCollapsed" class="space-y-1 pl-4">
                  <a
                    v-for="item in group.items"
                    :key="item.href"
                    :href="item.href"
                    :target="isExternalItem(item) ? '_blank' : null"
                    :rel="isExternalItem(item) ? 'noopener noreferrer' : null"
                    :class="isItemActive(item) ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700'"
                    class="flex items-center gap-2 rounded-md px-3 py-2 text-sm"
                  >
                    {{ item.label }}
                  </a>
                </div>
              </div>
            </template>

            <template v-if="isKecamatanScope">
              <div v-for="group in kecamatanMenuGroups" :key="`kecamatan-${group.key}`" class="space-y-1">
                <button
                  type="button"
                  :class="[sidebarCollapsed ? 'justify-center' : 'justify-between', isGroupActive(group) ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700']"
                  class="w-full flex items-center rounded-md px-3 py-2 text-sm"
                  @click="toggleGroup('kecamatan', group)"
                >
                  <span class="flex items-center gap-3">
                    <span v-show="!sidebarCollapsed">{{ group.label }}</span>
                    <span v-show="sidebarCollapsed">{{ group.code }}</span>
                  </span>
                  <svg v-show="!sidebarCollapsed" class="h-4 w-4 transition-transform" :class="{ 'rotate-180': isGroupOpen('kecamatan', group.key) }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </button>

                <div v-show="isGroupOpen('kecamatan', group.key) && !sidebarCollapsed" class="space-y-1 pl-4">
                  <a
                    v-for="item in group.items"
                    :key="item.href"
                    :href="item.href"
                    :target="isExternalItem(item) ? '_blank' : null"
                    :rel="isExternalItem(item) ? 'noopener noreferrer' : null"
                    :class="isItemActive(item) ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700'"
                    class="flex items-center gap-2 rounded-md px-3 py-2 text-sm"
                  >
                    {{ item.label }}
                  </a>
                </div>
              </div>
            </template>
          </div>

          <div class="space-y-1">
            <p v-show="!sidebarCollapsed" class="px-3 text-xs font-semibold uppercase tracking-wide text-slate-400">Akun</p>
            <a
              href="/profile"
              :class="[sidebarCollapsed ? 'justify-center' : '', isActive('/profile') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700']"
              class="flex items-center gap-3 rounded-md px-3 py-2 text-sm"
            >
              <span v-show="!sidebarCollapsed">Profil</span>
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
            <span v-show="!sidebarCollapsed">Keluar (Log Out)</span>
            <span v-show="sidebarCollapsed">LO</span>
          </button>
        </div>
      </div>
    </aside>

    <div :class="sidebarCollapsed ? 'lg:pl-20' : 'lg:pl-64'" class="pt-14 transition-all duration-200">
      <main class="px-4 sm:px-6 lg:px-8 py-6">
        <FlashMessageBar :flash="flash" />
        <slot />
      </main>
    </div>
  </div>
</template>
