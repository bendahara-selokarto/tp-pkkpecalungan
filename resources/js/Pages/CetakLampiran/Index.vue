<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import {
  buildLampiranMap,
  buildScopedMenuGroups,
  formatMenuLabel,
  formatPrintLabel,
  lampiranPlaceholder,
  resolveLampiran,
  resolveModuleSlugFromHref,
  resolvePrintSourceSlug,
} from '@/menus/printMenuRegistry'
import { usePage } from '@inertiajs/vue3'
import { mdiPrinter } from '@mdi/js'
import { computed } from 'vue'

const page = usePage()
const user = computed(() => page.props.auth?.user ?? null)
const roles = computed(() => user.value?.roles ?? [])
const menuGroupModes = computed(() => user.value?.menuGroupModes ?? {})
const moduleModes = computed(() => user.value?.moduleModes ?? {})
const userScope = computed(() => user.value?.scope ?? null)
const isDesaScope = computed(() => userScope.value === 'desa')
const isKecamatanScope = computed(() => userScope.value === 'kecamatan')
const isScopeSupported = computed(() => isDesaScope.value || isKecamatanScope.value)
const scopeLabel = computed(() => (isDesaScope.value ? 'Desa' : (isKecamatanScope.value ? 'Kecamatan' : '')))

const hasRole = (role) => roles.value.includes(role)
const isSekretarisRole = computed(() =>
  hasRole('desa-sekretaris')
  || hasRole('kecamatan-sekretaris'),
)

const isExternalItem = (item) => item.external === true || (typeof item.href === 'string' && item.href.includes('report/pdf'))
const duplicateAllowedModuleSlugs = new Set([])

const isModuleAllowedForCurrentUser = (item) => {
  if (isExternalItem(item)) {
    return true
  }

  const moduleSlug = resolveModuleSlugFromHref(item.href)
  if (!moduleSlug) {
    return true
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

const filterMenuItems = (items, seenInternalHrefs, seenModuleSlugs) => items.filter((item) => {
  if (!isMenuItemVisibleByExperimentalPlacement(item)) {
    return false
  }

  if (!isModuleAllowedForCurrentUser(item)) {
    return false
  }

  const moduleSlug = resolveModuleSlugFromHref(item.href)
  if (moduleSlug && seenModuleSlugs.has(moduleSlug)) {
    // Jika item adalah link PDF report, dan modul utamanya sudah ada di sidebar, sembunyikan dari lampiran
    if (item.href.includes('report/pdf') && !allowsDuplicateMenuHref(item)) {
      return false
    }
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

const baseGroups = computed(() => {
  if (isDesaScope.value) {
    return buildScopedMenuGroups('desa')
  }

  if (isKecamatanScope.value) {
    return buildScopedMenuGroups('kecamatan')
  }

  return []
})

const inputLabelBySlug = computed(() => {
  const map = {}

  baseGroups.value.forEach((group) => {
    group.items.forEach((item) => {
      const slug = resolveModuleSlugFromHref(item.href)
      if (!slug || map[slug]) {
        return
      }

      map[slug] = formatMenuLabel(item.label)
    })
  })

  return map
})

const lampiranBySlug = computed(() => buildLampiranMap(baseGroups.value))

const visibleGroups = computed(() => {
  const seenPrintHrefs = new Set()
  const seenModuleSlugs = new Set()

  // Daftarkan semua item dari Menu Utama ke dalam set seen agar tidak diduplikasi di Menu Lampiran
  baseGroups.value.forEach((group) => {
    if (!!menuGroupModes.value[group.key]) {
      group.items.forEach((item) => {
        const slug = resolveModuleSlugFromHref(item.href)
        if (slug) {
          seenModuleSlugs.add(slug)
        }
        if (!allowsDuplicateMenuHref(item)) {
          const normalizedHref = item.href?.split('?')[0] ?? ''
          seenPrintHrefs.add(normalizedHref)
        }
      })
    }
  })

  return baseGroups.value
    .filter((group) => !!menuGroupModes.value[group.key])
    .map((group) => {
      const printItems = filterMenuItems(group.printItems ?? [], seenPrintHrefs, seenModuleSlugs)
        .map((item) => ({
          ...item,
          lampiran: resolveLampiran(item, lampiranBySlug.value),
          sourceSlug: resolvePrintSourceSlug(item.href),
        }))
        .map((item) => {
          if (!item.sourceSlug) {
            return {
              ...item,
              sourceLabel: item.external ? 'Dashboard' : null,
              sourceHref: null,
              isOrphan: !item.external,
            }
          }

          return {
            ...item,
            sourceLabel: inputLabelBySlug.value[item.sourceSlug] ?? item.sourceSlug,
            sourceHref: `/${userScope.value}/${item.sourceSlug}`,
            isOrphan: false,
          }
        })

      return {
        ...group,
        mode: menuGroupModes.value[group.key],
        printItems,
      }
    })
    .filter((group) => group.printItems.length > 0)
})

const hasVisiblePrintItems = computed(() => visibleGroups.value.length > 0)

const orphanCount = computed(() => visibleGroups.value.reduce((total, group) =>
  total + group.printItems.filter((item) => item.isOrphan).length, 0))
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton
      :icon="mdiPrinter"
      :title="scopeLabel ? `Cetak Lampiran ${scopeLabel}` : 'Cetak Lampiran'"
      main
    />

    <CardBox v-if="!isScopeSupported">
      <p class="text-sm text-slate-600 dark:text-slate-300">
        Menu cetak lampiran hanya tersedia untuk akun desa atau kecamatan.
      </p>
    </CardBox>

    <div v-else class="space-y-4">
      <CardBox v-if="!hasVisiblePrintItems">
        <p class="text-sm text-slate-600 dark:text-slate-300">
          Belum ada modul cetak lampiran yang tersedia untuk akses ini.
        </p>
      </CardBox>

      <CardBox v-else-if="orphanCount">
        <p class="text-sm text-amber-700 dark:text-amber-300">
          Ada {{ orphanCount }} laporan yang belum memiliki sumber input yang jelas.
          Mohon konfirmasi modul sumbernya agar tidak ada data yatim.
        </p>
      </CardBox>

      <CardBox v-else>
        <p class="text-sm text-slate-600 dark:text-slate-300">
          Semua laporan ditautkan ke sumber input. Beberapa laporan adalah rekap otomatis.
        </p>
      </CardBox>

      <CardBox v-for="group in visibleGroups" :key="`print-group-${group.key}`">
        <div class="mb-3 flex flex-wrap items-start justify-between gap-2">
          <div>
            <h3 class="text-base font-semibold text-slate-800 dark:text-slate-100">
              {{ group.label }}
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400">
              Daftar modul tanpa input dan pusat laporan agregasi.
            </p>
          </div>
          <span
            v-if="group.mode === 'read-only'"
            class="inline-flex items-center rounded border border-amber-300 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-amber-700 dark:border-amber-800 dark:text-amber-300"
          >
            Baca
          </span>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full min-w-[520px] text-sm">
            <thead class="border-b border-slate-200 text-left text-slate-500 dark:border-slate-700 dark:text-slate-300">
              <tr>
                <th class="px-3 py-2 font-semibold">Modul</th>
                <th class="px-3 py-2 font-semibold w-28">Lampiran</th>
                <th class="px-3 py-2 font-semibold w-48">Sumber Input</th>
                <th class="px-3 py-2 font-semibold w-24 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="item in group.printItems"
                :key="`print-item-${group.key}-${item.href}`"
                class="border-b border-slate-100 text-slate-700 dark:border-slate-800 dark:text-slate-200"
              >
                <td class="px-3 py-2">
                  {{ formatPrintLabel(item.label) }}
                </td>
                <td class="px-3 py-2 text-slate-500 dark:text-slate-300">
                  {{ item.lampiran || lampiranPlaceholder }}
                </td>
                <td class="px-3 py-2">
                  <a
                    v-if="item.sourceHref"
                    :href="item.sourceHref"
                    class="text-emerald-700 hover:underline dark:text-emerald-300"
                  >
                    {{ item.sourceLabel }}
                  </a>
                  <span v-else class="text-slate-400">
                    {{ item.sourceLabel || 'Belum ada sumber' }}
                  </span>
                </td>
                <td class="px-3 py-2 text-right">
                  <a
                    v-if="item.hub"
                    :href="item.href"
                    class="inline-flex items-center rounded-md border border-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
                  >
                    Buka Pusat
                  </a>
                  <a
                    v-else
                    :href="item.href"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center rounded-md border border-emerald-200 px-2.5 py-1 text-xs font-semibold text-emerald-700 hover:bg-emerald-50 dark:border-emerald-800/70 dark:text-emerald-300 dark:hover:bg-emerald-900/30"
                  >
                    Cetak
                  </a>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </CardBox>
    </div>
  </SectionMain>
</template>
