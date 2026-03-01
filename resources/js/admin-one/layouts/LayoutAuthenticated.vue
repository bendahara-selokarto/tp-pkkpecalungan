<script setup>
import AsideMenu from '@/admin-one/components/AsideMenu.vue';
import BaseIcon from '@/admin-one/components/BaseIcon.vue';
import FooterBar from '@/admin-one/components/FooterBar.vue';
import FormControl from '@/admin-one/components/FormControl.vue';
import NavBar from '@/admin-one/components/NavBar.vue';
import NavBarItemPlain from '@/admin-one/components/NavBarItemPlain.vue';
import { buildMenuAside } from '@/admin-one/menuAside.js';
import menuNavBar from '@/admin-one/menuNavBar.js';
import { useDarkModeStore } from '@/admin-one/stores/darkMode.js';
import { router, usePage } from '@inertiajs/vue3';
import { mdiBackburger, mdiForwardburger, mdiMenu, mdiMenuOpen } from '@mdi/js';
import { computed, ref } from 'vue';

const darkModeStore = useDarkModeStore();
const page = usePage();

const isAsideMobileExpanded = ref(false);
const isAsideLgActive = ref(false);
const isAsideDesktopCollapsed = ref(localStorage.getItem('admin-one-sidebar-collapsed') === '1');

const layoutAsidePadding = computed(() => (isAsideDesktopCollapsed.value ? '' : 'xl:pl-60'));

const menuAside = computed(() => buildMenuAside(page.props.auth?.user?.roles ?? []));

router.on('navigate', () => {
  isAsideMobileExpanded.value = false;
  isAsideLgActive.value = false;
});

const menuClick = (event, item) => {
  if (item.isToggleLightDark) {
    darkModeStore.set(null, true);
  }

  if (item.isLogout) {
    router.post('/logout');
  }
};

const toggleDesktopAside = () => {
  isAsideDesktopCollapsed.value = !isAsideDesktopCollapsed.value;
  localStorage.setItem('admin-one-sidebar-collapsed', isAsideDesktopCollapsed.value ? '1' : '0');
};
</script>

<template>
  <div
    :class="{
      'overflow-hidden lg:overflow-visible': isAsideMobileExpanded,
    }"
  >
    <div
      :class="[layoutAsidePadding, { 'ml-60 lg:ml-0': isAsideMobileExpanded }]"
      class="transition-(--transition-position) min-h-screen w-screen bg-gray-50 pt-14 lg:w-auto dark:bg-slate-800 dark:text-slate-100"
    >
      <NavBar
        :menu="menuNavBar"
        :class="[layoutAsidePadding, { 'ml-60 lg:ml-0': isAsideMobileExpanded }]"
        @menu-click="menuClick"
      >
        <NavBarItemPlain
          display="flex lg:hidden"
          @click.prevent="isAsideMobileExpanded = !isAsideMobileExpanded"
        >
          <BaseIcon :path="isAsideMobileExpanded ? mdiBackburger : mdiForwardburger" size="24" />
        </NavBarItemPlain>
        <NavBarItemPlain display="hidden lg:flex xl:hidden" @click.prevent="isAsideLgActive = true">
          <BaseIcon :path="mdiMenu" size="24" />
        </NavBarItemPlain>
        <NavBarItemPlain display="hidden xl:flex" @click.prevent="toggleDesktopAside">
          <BaseIcon :path="isAsideDesktopCollapsed ? mdiMenu : mdiMenuOpen" size="24" />
          <span class="ml-2 text-xs font-medium">
            {{ isAsideDesktopCollapsed ? 'Expand sidebar' : 'Collapse sidebar' }}
          </span>
        </NavBarItemPlain>
        <NavBarItemPlain as="div" use-margin>
          <FormControl placeholder="Search (ctrl+k)" ctrl-k-focus transparent borderless />
        </NavBarItemPlain>
      </NavBar>
      <AsideMenu
        :is-aside-mobile-expanded="isAsideMobileExpanded"
        :is-aside-lg-active="isAsideLgActive"
        :is-aside-desktop-collapsed="isAsideDesktopCollapsed"
        :menu="menuAside"
        @menu-click="menuClick"
        @aside-lg-close-click="isAsideLgActive = false"
      />
      <slot />
      <FooterBar>
        Get more with
        <a href="https://tailwind-vue.justboil.me/" target="_blank" class="text-blue-600"
          >Premium version</a
        >
      </FooterBar>
    </div>
  </div>
</template>
