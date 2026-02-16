import { defineStore } from 'pinia'

const themeKey = 'themeMode'

export const useUiStore = defineStore('ui', {
  state: () => ({
    isSidebarOpen: false,
    theme: typeof localStorage !== 'undefined' ? localStorage.getItem(themeKey) || 'system' : 'system',
  }),
  actions: {
    toggleSidebar() {
      this.isSidebarOpen = !this.isSidebarOpen
    },
    setSidebar(open) {
      this.isSidebarOpen = !!open
    },
    setTheme(theme) {
      this.theme = theme
      if (typeof localStorage !== 'undefined') {
        localStorage.setItem(themeKey, theme)
      }
    },
  },
})
