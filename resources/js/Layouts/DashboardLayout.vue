<script setup>
import { Link, router, usePage } from '@inertiajs/vue3'
import { useDarkModeStore } from '@/admin-one/stores/darkMode'
import FlashMessageBar from '@/admin-one/components/FlashMessageBar.vue'
import { formatRoleList } from '@/utils/roleLabelFormatter'
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import {
  desaMenuGroups,
  kecamatanMenuGroups,
} from '@/menus/printMenuRegistry'

const page = usePage()
const darkModeStore = useDarkModeStore()
const sidebarCollapsedKey = 'admin-one-sidebar-collapsed'
const runtimeErrorEventName = 'ui-runtime-error'

const readSidebarCollapsedPreference = () => {
  try {
    return localStorage.getItem(sidebarCollapsedKey) === '1'
  } catch (_error) {
    return false
  }
}

const persistSidebarCollapsedPreference = (collapsed) => {
  try {
    localStorage.setItem(sidebarCollapsedKey, collapsed ? '1' : '0')
  } catch (_error) {
    // Ignore storage failures so UI state still works in-memory.
  }
}

const isAsideMobileExpanded = ref(false)
const isAsideLgActive = ref(false)
const isAsideDesktopCollapsed = ref(readSidebarCollapsedPreference())
const runtimeErrorVisible = ref(false)
let removeNavigateListener = null

const user = computed(() => page.props.auth?.user ?? null)
const roles = computed(() => user.value?.roles ?? [])
const menuGroupModes = computed(() => user.value?.menuGroupModes ?? {})
const moduleModes = computed(() => user.value?.moduleModes ?? {})
const activeRoles = computed(() => formatRoleList(roles.value))
const flash = computed(() => page.props.flash ?? {})
const userScope = computed(() => user.value?.scope ?? null)
const isDesaScope = computed(() => userScope.value === 'desa')
const isKecamatanScope = computed(() => userScope.value === 'kecamatan')
const normalizedPath = computed(() => page.url.split('?')[0])
const isProfilePage = computed(() => normalizedPath.value === '/profile')
const pathSegments = computed(() => normalizedPath.value.split('/').filter(Boolean))
const currentModuleSlug = computed(() => pathSegments.value[1] ?? null)
const currentModuleMode = computed(() =>
  currentModuleSlug.value ? (moduleModes.value[currentModuleSlug.value] ?? null) : null,
)
const isCurrentModuleReadOnly = computed(() => currentModuleMode.value === 'read-only')

const hasRole = (role) => roles.value.includes(role)
const isSekretarisRole = computed(() =>
  hasRole('desa-sekretaris')
  || hasRole('kecamatan-sekretaris'),
)

const isActive = (prefix) => page.url.startsWith(prefix)

const isExternalItem = (item) => item.external === true
const isItemActive = (item) => !isExternalItem(item) && isActive(item.href)
const openExternal = (href) => {
  window.open(href, '_blank', 'noopener,noreferrer')
}

const duplicateAllowedModuleSlugs = new Set([
  'activities',
  'program-prioritas',
  'inventaris',
  'bantuans',
  'prestasi-lomba',
  'kader-khusus',
  'buku-notulen-rapat',
  'buku-daftar-hadir',
  'catatan-keluarga',
])

const resolveModuleSlugFromHref = (href) => {
  if (typeof href !== 'string' || href.length === 0 || href.startsWith('http')) {
    return null
  }

  const normalizedPath = href.split('?')[0]
  const segments = normalizedPath.split('/').filter(Boolean)
  if (segments.length < 2) {
    return null
  }

  // Handle nested paths for Pokja Data Kegiatan and Data Umum
  // e.g., /desa/catatan-keluarga/data-kegiatan-pkk-pokja-ii/report/pdf -> data-kegiatan-pkk-pokja-ii
  if (segments.length >= 3 && segments[1] === 'catatan-keluarga') {
    const slug = segments[2]
    if (slug === 'buku-data-umum-pokja-iv') return 'data-umum-pkk'
    if (slug === 'buku-asi-eksklusif-pokja-iv') return 'posyandu'
    if (slug === 'buku-iva-test-pokja-iv') return 'posyandu'
    if (slug === 'buku-data-kegiatan-posyandu-pokja-iv') return 'posyandu'
    if (slug === 'buku-kader-khusus-pokja-iv') return 'kader-khusus'

    return slug
  }

  // Handle nested paths for Simulasi books
  // e.g., /desa/simulasi/buku-tamu -> buku-tamu-simulasi
  if (segments.length >= 3 && segments[1] === 'simulasi') {
    return `${segments[2]}-simulasi`
  }

  return segments[1]
}

const isModuleAllowedForCurrentUser = (item) => {
  // If item has a sourceKey (from consolidation), check if user has access to that specific group
  if (item.sourceKey) {
    const groupMode = menuGroupModes.value[item.sourceKey] ?? null
    if (!groupMode) {
      return false
    }

    // Hide read-only items to avoid UI confusion (Ownership-Only Visibility)
    if (groupMode === 'read-only' && !hasRole('super-admin')) {
      return false
    }
  }

  if (isExternalItem(item)) {
    return true
  }

  const moduleSlug = resolveModuleSlugFromHref(item.href)
  if (!moduleSlug) {
    // If we can't resolve a module slug, default to hidden for safety, 
    // unless it's a known internal non-module path like # or /
    return item.href === '#' || item.href === '/'
  }

  return !!moduleModes.value[moduleSlug]
}

const allowsDuplicateMenuHref = (item) => {
  const moduleSlug = resolveModuleSlugFromHref(item.href)

  return moduleSlug ? duplicateAllowedModuleSlugs.has(moduleSlug) : false
}

const isMenuItemVisibleByExperimentalPlacement = (item) => {
  const visibility = String(item?.uiVisibility ?? 'default')

  if (visibility === 'disabled') {
    return false
  }

  if (visibility === 'sekretaris-only') {
    return isSekretarisRole.value
  }

  if (visibility === 'desa-pokja-i-only') {
    return isDesaScope.value && hasRole('desa-pokja-i')
  }

  return true
}

const filterMenuItems = (items, seenInternalHrefs) => items.filter((item) => {
  if (!isMenuItemVisibleByExperimentalPlacement(item)) {
    return false
  }

  if (
    isKecamatanScope.value
    && isSekretarisRole.value
    && item.sourceKey === 'common-pembantu'
  ) {
    return false
  }

  if (!isModuleAllowedForCurrentUser(item)) {
    return false
  }

  const normalizedHref = item.href?.split('?')[0] ?? ''

  if (!allowsDuplicateMenuHref(item) && seenInternalHrefs.has(normalizedHref)) {
    return false
  }

  if (!allowsDuplicateMenuHref(item)) {
    seenInternalHrefs.add(normalizedHref)
  }

  return true
})

const withMode = (groups) => {
  const seenInternalHrefs = new Set()

  return groups
    .filter((group) => {
      // Check if any of the original keys from consolidation have a mode in backend
      let effectiveMode = null
      if (group.originalKeys) {
        // Resolve the effective mode (prefer read-write over read-only)
        const modes = group.originalKeys.map((key) => menuGroupModes.value[key]).filter(Boolean)
        if (modes.includes('read-write')) {
          effectiveMode = 'read-write'
        } else if (modes.length > 0) {
          effectiveMode = 'read-only'
        }
      } else {
        effectiveMode = menuGroupModes.value[group.key]
      }

      if (!effectiveMode) {
        return false
      }

      // Ownership-Only Visibility: Only show groups where user has READ-WRITE access (unless Super Admin)
      if (effectiveMode === 'read-only' && !hasRole('super-admin')) {
        return false
      }

      return true
    })
    .map((group) => {
      // Resolve the effective mode (prefer read-write over read-only)
      let mode = 'read-only'
      if (group.originalKeys) {
        const hasRW = group.originalKeys.some((key) => menuGroupModes.value[key] === 'read-write')
        mode = hasRW ? 'read-write' : 'read-only'
      } else {
        mode = menuGroupModes.value[group.key]
      }

      return {
        ...group,
        mode,
        items: filterMenuItems(group.items, seenInternalHrefs),
      }
    })
    .filter((group) => group.items.length > 0)
}

const desaVisibleMenuGroups = computed(() => withMode(desaMenuGroups))
const kecamatanVisibleMenuGroups = computed(() => withMode(kecamatanMenuGroups))
const hasVisibleDomainMenu = computed(() =>
  (isDesaScope.value && desaVisibleMenuGroups.value.length > 0)
  || (isKecamatanScope.value && kecamatanVisibleMenuGroups.value.length > 0),
)

function isGroupActive(group) {
  return group.items.some((item) => isItemActive(item))
}

const shouldOpenGroupByDefault = (group) =>
  isGroupActive(group) || group.mode === 'read-write'

const syncGroupState = (current, groups) => {
  const next = {}
  groups.forEach((group) => {
    next[group.key] = current[group.key] ?? shouldOpenGroupByDefault(group)
  })

  return next
}

const desaGroupOpen = ref({})
const kecamatanGroupOpen = ref({})

watch(
  desaVisibleMenuGroups,
  (groups) => {
    desaGroupOpen.value = syncGroupState(desaGroupOpen.value, groups)
  },
  { immediate: true },
)

watch(
  kecamatanVisibleMenuGroups,
  (groups) => {
    kecamatanGroupOpen.value = syncGroupState(kecamatanGroupOpen.value, groups)
  },
  { immediate: true },
)

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
  if (isAsideDesktopCollapsed.value) {
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

const layoutAsidePadding = computed(() => (isAsideDesktopCollapsed.value ? 'xl:pl-20' : 'xl:pl-64'))

const toggleCollapse = () => {
  isAsideDesktopCollapsed.value = !isAsideDesktopCollapsed.value
  persistSidebarCollapsedPreference(isAsideDesktopCollapsed.value)
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

const showRuntimeErrorFallback = () => {
  runtimeErrorVisible.value = true
}

const reloadPage = () => {
  if (typeof window !== 'undefined') {
    window.location.reload()
  }
}

const dismissRuntimeError = () => {
  runtimeErrorVisible.value = false
}

onMounted(() => {
  if (typeof window !== 'undefined') {
    window.addEventListener(runtimeErrorEventName, showRuntimeErrorFallback)
  }

  removeNavigateListener = router.on('navigate', () => {
    isAsideMobileExpanded.value = false
    isAsideLgActive.value = false
  })
})

onBeforeUnmount(() => {
  if (typeof window !== 'undefined') {
    window.removeEventListener(runtimeErrorEventName, showRuntimeErrorFallback)
  }

  if (typeof removeNavigateListener === 'function') {
    removeNavigateListener()
    removeNavigateListener = null
  }
})
</script>

<template>
  <div class="min-h-screen bg-gray-50 text-slate-800 dark:bg-slate-900 dark:text-slate-100">
    <div
      v-show="isAsideMobileExpanded"
      class="fixed inset-0 z-30 bg-slate-900/60 lg:hidden"
      @click="isAsideMobileExpanded = false"
    />
    <div
      v-show="isAsideLgActive"
      class="fixed inset-0 z-30 hidden bg-slate-900/60 lg:block xl:hidden"
      @click="isAsideLgActive = false"
    />

    <header class="fixed inset-x-0 top-0 z-40 h-14 border-b border-slate-200 bg-white/95 backdrop-blur dark:border-slate-700 dark:bg-slate-800/95">
      <div class="h-full px-4 sm:px-6 lg:px-8 flex items-center justify-between lg:justify-end">
        <div class="flex items-center gap-3 min-w-0 lg:hidden">
          <button class="inline-flex items-center justify-center rounded-md p-2 text-slate-500 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700 lg:hidden" @click="isAsideMobileExpanded = !isAsideMobileExpanded">
            <span class="sr-only">Buka atau tutup sidebar</span>
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
          <button class="hidden lg:inline-flex xl:hidden items-center gap-2 rounded-md px-2.5 py-2 text-slate-500 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700" @click="isAsideLgActive = !isAsideLgActive">
            <span class="sr-only">Buka atau tutup sidebar</span>
            <svg class="h-5 w-5 transition-transform" :class="{ 'rotate-180': isAsideLgActive }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            <span class="text-xs font-medium">{{ isAsideLgActive ? 'Tutup' : 'Menu' }}</span>
          </button>
          <button class="hidden xl:inline-flex items-center gap-2 rounded-md px-2.5 py-2 text-slate-500 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700" @click="toggleCollapse">
            <span class="sr-only">Ringkas sidebar</span>
            <svg class="h-5 w-5 transition-transform" :class="{ 'rotate-180': isAsideDesktopCollapsed }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            <span class="text-xs font-medium">{{ isAsideDesktopCollapsed ? 'Lebarkan' : 'Ringkas' }}</span>
          </button>
          <Link :href="primaryHref" class="flex items-center gap-2 min-w-0">
            <img :src="pkkLogo" alt="" aria-hidden="true" class="h-6 w-6 object-contain" @error="hideBrokenImage">
            <span class="text-sm font-semibold tracking-wide uppercase text-slate-700 dark:text-slate-100 truncate">
              {{ page.props.appName ?? 'Akaraya PKK' }}
            </span>
          </Link>
        </div>

        <div class="flex items-center gap-3">
          <Link
            v-if="!hasRole('super-admin')"
            href="/dashboard"
            :class="isActive('/dashboard') ? 'text-cyan-700 dark:text-cyan-300' : 'text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white'"
            class="text-sm font-medium"
          >
            Dashboard
          </Link>
          <Link
            v-if="!hasRole('super-admin')"
            href="/arsip"
            :class="isActive('/arsip') ? 'text-cyan-700 dark:text-cyan-300' : 'text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white'"
            class="text-sm font-medium"
          >
            Arsip
          </Link>
          <button
            type="button"
            :class="{ 'transition-colors': !darkModeStore.isInProgress }"
            class="inline-flex items-center rounded-md border border-slate-300 bg-white/90 px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm backdrop-blur hover:bg-white dark:border-slate-600 dark:bg-slate-900/80 dark:text-slate-200 dark:hover:bg-slate-900"
            @click="toggleTheme"
          >
            {{ darkModeStore.isEnabled ? 'Light mode' : 'Dark mode' }}
          </button>
          <a href="/profile" class="text-sm text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white">Profil</a>
          <button type="button" class="text-sm text-rose-600 hover:text-rose-700 dark:text-rose-400" @click="logout">
            Keluar
          </button>
        </div>
      </div>
    </header>

    <aside :class="[
      isAsideMobileExpanded ? 'translate-x-0' : '-translate-x-full',
      isAsideLgActive ? 'lg:translate-x-0' : 'lg:-translate-x-full',
      'xl:translate-x-0',
      isAsideDesktopCollapsed ? 'xl:w-20' : 'xl:w-64',
    ]" class="fixed inset-y-0 left-0 z-40 w-72 transform border-r border-slate-200 bg-white transition-all duration-200 ease-in-out dark:border-slate-700 dark:bg-slate-800">
      <button
        type="button"
        class="absolute -right-3 top-20 z-50 hidden h-7 w-7 items-center justify-center rounded-full border border-slate-300 bg-white text-slate-600 shadow-sm hover:bg-slate-100 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700 lg:hidden xl:inline-flex"
        :title="isAsideDesktopCollapsed ? 'Lebarkan sidebar' : 'Ringkas sidebar'"
        @click="toggleCollapse"
      >
        <span class="sr-only">{{ isAsideDesktopCollapsed ? 'Lebarkan sidebar' : 'Ringkas sidebar' }}</span>
        <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': isAsideDesktopCollapsed }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
      </button>

      <div class="h-full flex flex-col">
        <div class="h-14 px-4 flex items-center justify-between border-b border-slate-200 dark:border-slate-700">
          <Link :href="primaryHref" :class="isAsideDesktopCollapsed ? 'justify-center w-full' : ''" class="flex items-center gap-2 min-w-0">
            <img :src="pkkLogo" alt="" aria-hidden="true" class="h-7 w-7 object-contain" @error="hideBrokenImage">
            <span v-show="!isAsideDesktopCollapsed" class="text-sm font-semibold text-slate-700 dark:text-slate-100 truncate">
              {{ page.props.appName ?? 'Akaraya PKK' }}
            </span>
          </Link>
          <button class="rounded-md p-1 text-slate-500 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700 lg:hidden" @click="isAsideMobileExpanded = false">
            <span class="sr-only">Tutup sidebar</span>
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div v-show="!isAsideDesktopCollapsed" class="mx-4 mt-4 rounded-lg border border-cyan-200 bg-cyan-50/70 p-3 dark:border-cyan-700 dark:bg-cyan-900/20">
          <p class="text-xs font-medium uppercase tracking-wide text-cyan-700 dark:text-cyan-300">Pengguna</p>
          <p class="mt-1 text-sm font-semibold text-slate-800 dark:text-slate-100">{{ user?.name }}</p>
          <p class="mt-2 text-xs text-cyan-700 dark:text-cyan-300">{{ activeRoles }}</p>
        </div>

        <nav class="flex-1 overflow-y-auto p-4 space-y-5">
          <div v-if="hasRole('super-admin')" class="space-y-1">
            <p v-show="!isAsideDesktopCollapsed" class="px-3 text-xs font-semibold uppercase tracking-wide text-slate-400">Utama</p>

            <Link
              v-if="hasRole('super-admin')"
              href="/super-admin/users"
              :class="[isAsideDesktopCollapsed ? 'justify-center' : '', isActive('/super-admin/users') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700']"
              class="flex items-center gap-3 rounded-md px-3 py-2 text-sm"
            >
              <span v-show="!isAsideDesktopCollapsed">Manajemen User</span>
              <span v-show="isAsideDesktopCollapsed">MU</span>
            </Link>
            <Link
              v-if="hasRole('super-admin')"
              href="/super-admin/areas"
              :class="[isAsideDesktopCollapsed ? 'justify-center' : '', isActive('/super-admin/areas') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700']"
              class="flex items-center gap-3 rounded-md px-3 py-2 text-sm"
            >
              <span v-show="!isAsideDesktopCollapsed">Manajemen Wilayah</span>
              <span v-show="isAsideDesktopCollapsed">MW</span>
            </Link>
            <Link
              v-if="hasRole('super-admin')"
              href="/super-admin/access-control"
              :class="[isAsideDesktopCollapsed ? 'justify-center' : '', isActive('/super-admin/access-control') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700']"
              class="flex items-center gap-3 rounded-md px-3 py-2 text-sm"
            >
              <span v-show="!isAsideDesktopCollapsed">Management Ijin Akses</span>
              <span v-show="isAsideDesktopCollapsed">IA</span>
            </Link>
            <Link
              v-if="hasRole('super-admin')"
              href="/super-admin/arsip"
              :class="[isAsideDesktopCollapsed ? 'justify-center' : '', isActive('/super-admin/arsip') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700']"
              class="flex items-center gap-3 rounded-md px-3 py-2 text-sm"
            >
              <span v-show="!isAsideDesktopCollapsed">Management Arsip</span>
              <span v-show="isAsideDesktopCollapsed">AR</span>
            </Link>
          </div>

          <div v-if="!isProfilePage && !hasRole('super-admin')" class="space-y-1">
            <p v-show="!isAsideDesktopCollapsed" class="px-3 text-xs font-semibold uppercase tracking-wide text-slate-400">Menu Domain</p>

            <template v-if="isDesaScope">
              <div v-for="group in desaVisibleMenuGroups" :key="`desa-${group.key}`" class="space-y-1">
                <button
                  type="button"
                  :class="[isAsideDesktopCollapsed ? 'justify-center' : 'justify-between', isGroupActive(group) ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700']"
                  class="w-full flex items-center rounded-md px-3 py-2 text-sm"
                  @click="toggleGroup('desa', group)"
                >
                  <span class="flex items-center gap-3">
                    <span v-show="!isAsideDesktopCollapsed">{{ group.label }}</span>
                    <span v-show="isAsideDesktopCollapsed">{{ group.code }}</span>
                    <span
                      v-if="!isAsideDesktopCollapsed && group.mode === 'read-only'"
                      class="inline-flex items-center rounded border border-amber-300 px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-amber-700 dark:border-amber-800 dark:text-amber-300"
                    >
                      Baca
                    </span>
                  </span>
                  <svg v-show="!isAsideDesktopCollapsed" class="h-4 w-4 transition-transform" :class="{ 'rotate-180': isGroupOpen('desa', group.key) }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </button>

                <div v-show="isGroupOpen('desa', group.key) && !isAsideDesktopCollapsed" class="space-y-1 pl-4">
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
              <div v-for="group in kecamatanVisibleMenuGroups" :key="`kecamatan-${group.key}`" class="space-y-1">
                <button
                  type="button"
                  :class="[isAsideDesktopCollapsed ? 'justify-center' : 'justify-between', isGroupActive(group) ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700']"
                  class="w-full flex items-center rounded-md px-3 py-2 text-sm"
                  @click="toggleGroup('kecamatan', group)"
                >
                  <span class="flex items-center gap-3">
                    <span v-show="!isAsideDesktopCollapsed">{{ group.label }}</span>
                    <span v-show="isAsideDesktopCollapsed">{{ group.code }}</span>
                    <span
                      v-if="!isAsideDesktopCollapsed && group.mode === 'read-only'"
                      class="inline-flex items-center rounded border border-amber-300 px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-amber-700 dark:border-amber-800 dark:text-amber-300"
                    >
                      Baca
                    </span>
                  </span>
                  <svg v-show="!isAsideDesktopCollapsed" class="h-4 w-4 transition-transform" :class="{ 'rotate-180': isGroupOpen('kecamatan', group.key) }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </button>

                <div v-show="isGroupOpen('kecamatan', group.key) && !isAsideDesktopCollapsed" class="space-y-1 pl-4">
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

            <p
              v-if="!hasRole('super-admin') && !hasVisibleDomainMenu"
              class="rounded-md border border-dashed border-slate-300 px-3 py-2 text-xs text-slate-500 dark:border-slate-600 dark:text-slate-300"
            >
              Belum ada menu domain yang dapat ditampilkan untuk akun ini.
            </p>
          </div>

        </nav>

      </div>
    </aside>

    <div :class="layoutAsidePadding" class="pt-14 transition-all duration-200">
      <main :class="[{ 'module-read-only': isCurrentModuleReadOnly }, 'px-4 sm:px-6 lg:px-8 py-6']">
        <FlashMessageBar :flash="flash" />
        <div
          v-if="runtimeErrorVisible"
          class="mb-4 flex items-start justify-between gap-3 rounded-md border border-amber-300 bg-amber-50 px-4 py-3 text-amber-900 dark:border-amber-700 dark:bg-amber-950/30 dark:text-amber-200"
        >
          <p class="text-sm">
            Terjadi gangguan antarmuka karena error JavaScript. Muat ulang halaman untuk memulihkan tampilan.
          </p>
          <div class="flex items-center gap-2">
            <button
              type="button"
              class="rounded border border-amber-500 px-2 py-1 text-xs font-medium hover:bg-amber-100 dark:hover:bg-amber-900/40"
              @click="reloadPage"
            >
              Muat Ulang
            </button>
            <button
              type="button"
              class="rounded px-2 py-1 text-xs font-medium hover:bg-amber-100 dark:hover:bg-amber-900/40"
              @click="dismissRuntimeError"
            >
              Tutup
            </button>
          </div>
        </div>
        <slot />
      </main>
    </div>
  </div>
</template>

<style scoped>
.module-read-only :deep(a[href$='/create']),
.module-read-only :deep(a[href*='/edit']),
.module-read-only :deep(button.border-rose-200),
.module-read-only :deep(button.border-rose-300) {
  display: none !important;
}
</style>
