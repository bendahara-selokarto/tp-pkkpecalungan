import { mdiAccountCircle, mdiAccountMultiple, mdiMapMarker, mdiMonitor } from '@mdi/js'

export const buildMenuAside = (roles = []) => {
  const menu = [
    {
      route: '/dashboard',
      icon: mdiMonitor,
      label: 'Dashboard',
    },
  ]

  if (roles.includes('super-admin')) {
    menu.push({
      route: '/super-admin/users',
      icon: mdiAccountMultiple,
      label: 'Manajemen User',
    })
    menu.push({
      route: '/super-admin/areas',
      icon: mdiMapMarker,
      label: 'Manajemen Wilayah',
    })
  }

  menu.push({
    href: '/profile',
    icon: mdiAccountCircle,
    label: 'Profil',
  })

  return menu
}
