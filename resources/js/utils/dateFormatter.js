const CANONICAL_DATE_PATTERN = /^(\d{4})-(\d{2})-(\d{2})$/
const DISPLAY_DATE_PATTERN = /^(\d{2})\/(\d{2})\/(\d{4})$/

export const formatDateForDisplay = (value, fallback = '-') => {
  if (value === null || value === undefined) {
    return fallback
  }

  const raw = String(value).trim()
  if (raw === '') {
    return fallback
  }

  const canonicalMatch = raw.match(CANONICAL_DATE_PATTERN)
  if (canonicalMatch) {
    const [, year, month, day] = canonicalMatch
    return `${day}/${month}/${year}`
  }

  if (DISPLAY_DATE_PATTERN.test(raw)) {
    return raw
  }

  return raw
}
