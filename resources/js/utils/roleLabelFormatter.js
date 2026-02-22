const SCOPE_LABELS = {
  desa: 'Desa',
  kecamatan: 'Kecamatan',
}

const ROLE_SUFFIX_LABELS = {
  sekretaris: 'Sekretaris',
  bendahara: 'Bendahara',
  'pokja-i': 'Pokja I',
  'pokja-ii': 'Pokja II',
  'pokja-iii': 'Pokja III',
  'pokja-iv': 'Pokja IV',
}

const titleCase = (value) =>
  value
    .split(' ')
    .filter(Boolean)
    .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ')

export const humanizeSlug = (value) => {
  if (!value || typeof value !== 'string') {
    return '-'
  }

  return titleCase(value.replace(/[-_]+/g, ' ').trim())
}

export const formatScopeLabel = (scope) => SCOPE_LABELS[scope] ?? humanizeSlug(scope)

export const formatRoleLabel = (role, roleLabelMap = null) => {
  if (!role || typeof role !== 'string') {
    return '-'
  }

  if (roleLabelMap && typeof roleLabelMap[role] === 'string') {
    return roleLabelMap[role]
  }

  if (role === 'super-admin') {
    return 'Super Admin'
  }

  if (!role.includes('-') && !role.includes('_')) {
    return role
  }

  const [scope, suffix] = role.split(/-(.+)/)
  if ((scope === 'desa' || scope === 'kecamatan') && suffix) {
    const baseLabel = ROLE_SUFFIX_LABELS[suffix] ?? humanizeSlug(suffix)

    return `${baseLabel} (${formatScopeLabel(scope)})`
  }

  return humanizeSlug(role)
}

export const formatRoleList = (roles, roleLabelMap = null) => {
  if (!Array.isArray(roles) || roles.length === 0) {
    return '-'
  }

  return roles.map((role) => formatRoleLabel(role, roleLabelMap)).join(', ')
}

export const formatAreaLabel = (area) => {
  if (!area || typeof area !== 'object') {
    return '-'
  }

  const level = formatScopeLabel(area.level)
  const name = typeof area.name === 'string' && area.name.trim() !== '' ? area.name : '-'

  return `${level} - ${name}`
}
