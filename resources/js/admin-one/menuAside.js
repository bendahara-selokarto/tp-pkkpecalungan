import { mdiAccountCircle, mdiAccountMultiple, mdiMonitor } from '@mdi/js'

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
      label: 'Management User',
    })
  }

  menu.push({
    href: '/profile',
    icon: mdiAccountCircle,
    label: 'Profile',
  })

  return menu
}
