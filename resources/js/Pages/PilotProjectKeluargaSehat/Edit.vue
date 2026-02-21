<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { mdiFileDocumentEditOutline } from '@mdi/js'
import { computed, watch } from 'vue'

const props = defineProps({
  scopeLabel: {
    type: String,
    required: true,
  },
  scopePrefix: {
    type: String,
    required: true,
  },
  sections: {
    type: Array,
    default: () => [],
  },
  report: {
    type: Object,
    required: true,
  },
})

const normalizeYearRange = (tahunAwal, tahunAkhir) => {
  const start = Number.isFinite(Number(tahunAwal)) ? Number(tahunAwal) : 2021
  const end = Number.isFinite(Number(tahunAkhir)) ? Number(tahunAkhir) : start

  return {
    start,
    end: end >= start ? end : start,
  }
}

const buildPeriods = (tahunAwal, tahunAkhir) => {
  const { start, end } = normalizeYearRange(tahunAwal, tahunAkhir)
  const list = []

  for (let year = start; year <= end; year += 1) {
    list.push({ year, semester: 1, key: `${year}-1`, semesterLabel: 'I' })
    list.push({ year, semester: 2, key: `${year}-2`, semesterLabel: 'II' })
  }

  return list
}

const rowKey = (section, clusterCode, indicatorCode, year, semester) =>
  `${section}::${String(clusterCode).toUpperCase()}::${indicatorCode}::${year}::${semester}`

const buildCatalogValues = (sections, tahunAwal, tahunAkhir, existingValues = []) => {
  const periods = buildPeriods(tahunAwal, tahunAkhir)
  const existingMap = new Map()

  for (const value of existingValues) {
    existingMap.set(
      rowKey(value.section, value.cluster_code, value.indicator_code, value.year, value.semester),
      value,
    )
  }

  const rows = []
  const consumed = new Set()
  let sortOrder = 1

  for (const section of sections) {
    const sectionKey = String(section.storage_section ?? 'pilot_project')
    const clusters = Array.isArray(section.clusters) ? section.clusters : []

    for (const cluster of clusters) {
      const clusterCode = String(cluster.code ?? '').toUpperCase()
      const indicators = Array.isArray(cluster.indicators) ? cluster.indicators : []

      for (const indicator of indicators) {
        const indicatorCode = String(indicator.code ?? '')
        const indicatorLabel = String(indicator.label ?? indicatorCode)

        for (const period of periods) {
          const key = rowKey(sectionKey, clusterCode, indicatorCode, period.year, period.semester)
          const existing = existingMap.get(key)
          if (existing) {
            consumed.add(key)
          }

          rows.push({
            section: sectionKey,
            cluster_code: clusterCode,
            indicator_code: indicatorCode,
            indicator_label: indicatorLabel,
            year: period.year,
            semester: period.semester,
            value: Number.isFinite(Number(existing?.value)) ? Number(existing.value) : 0,
            evaluation_note: String(existing?.evaluation_note ?? ''),
            keterangan_note: String(existing?.keterangan_note ?? ''),
            sort_order: sortOrder,
          })

          sortOrder += 1
        }
      }
    }
  }

  for (const value of existingValues) {
    const key = rowKey(value.section, value.cluster_code, value.indicator_code, value.year, value.semester)
    if (consumed.has(key)) {
      continue
    }

    rows.push({
      section: String(value.section ?? 'pilot_project'),
      cluster_code: String(value.cluster_code ?? '').toUpperCase(),
      indicator_code: String(value.indicator_code ?? ''),
      indicator_label: String(value.indicator_label ?? value.indicator_code ?? ''),
      year: Number(value.year ?? 0),
      semester: Number(value.semester ?? 1) === 2 ? 2 : 1,
      value: Number.isFinite(Number(value.value)) ? Number(value.value) : 0,
      evaluation_note: String(value.evaluation_note ?? ''),
      keterangan_note: String(value.keterangan_note ?? ''),
      sort_order: sortOrder,
    })

    sortOrder += 1
  }

  return rows
}

const initialValues = buildCatalogValues(
  props.sections,
  props.report.tahun_awal ?? 2021,
  props.report.tahun_akhir ?? 2024,
  Array.isArray(props.report.values) ? props.report.values : [],
)

const form = useForm({
  judul_laporan: props.report.judul_laporan ?? '',
  dasar_hukum: props.report.dasar_hukum ?? '',
  pendahuluan: props.report.pendahuluan ?? '',
  maksud_tujuan: props.report.maksud_tujuan ?? '',
  pelaksanaan: props.report.pelaksanaan ?? '',
  dokumentasi: props.report.dokumentasi ?? '',
  penutup: props.report.penutup ?? '',
  tahun_awal: props.report.tahun_awal ?? 2021,
  tahun_akhir: props.report.tahun_akhir ?? 2024,
  values: initialValues,
})

const periods = computed(() => buildPeriods(form.tahun_awal, form.tahun_akhir))
const periodYears = computed(() => [...new Set(periods.value.map((period) => period.year))])

const groupedValues = computed(() => {
  const valueMap = new Map()
  for (const row of form.values) {
    valueMap.set(
      rowKey(row.section, row.cluster_code, row.indicator_code, row.year, row.semester),
      row,
    )
  }

  return (props.sections ?? []).map((section) => {
    const sectionKey = String(section.storage_section ?? 'pilot_project')
    const clusters = Array.isArray(section.clusters) ? section.clusters : []

    return {
      key: sectionKey,
      label: section.label ?? sectionKey,
      clusters: clusters.map((cluster) => {
        const clusterCode = String(cluster.code ?? '').toUpperCase()
        const indicators = Array.isArray(cluster.indicators) ? cluster.indicators : []

        return {
          key: clusterCode,
          label: cluster.label ?? clusterCode,
          indicators: indicators.map((indicator, index) => {
            const indicatorCode = String(indicator.code ?? '')
            const indicatorLabel = String(indicator.label ?? indicatorCode)
            const cells = periods.value.map((period) =>
              valueMap.get(rowKey(sectionKey, clusterCode, indicatorCode, period.year, period.semester)) ?? null,
            )
            const noteCell = [...cells].reverse().find((cell) => cell !== null) ?? null

            return {
              no: index + 1,
              code: indicatorCode,
              label: indicatorLabel,
              cells,
              noteCell,
            }
          }),
        }
      }),
    }
  })
})

const regenerateCatalogValues = () => {
  form.values = buildCatalogValues(props.sections, form.tahun_awal, form.tahun_akhir, form.values)
}

const periodKey = computed(() => {
  const { start, end } = normalizeYearRange(form.tahun_awal, form.tahun_akhir)

  return `${start}-${end}`
})

watch(periodKey, (nextPeriodKey, previousPeriodKey) => {
  if (nextPeriodKey === previousPeriodKey) {
    return
  }

  regenerateCatalogValues()
})

const submit = () => {
  form.transform((data) => ({
    ...data,
    values: (data.values ?? []).map((row, index) => ({
      ...row,
      sort_order: index + 1,
    })),
  })).put(`${props.scopePrefix}/${props.report.id}`, {
    preserveScroll: true,
  })
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton
      :icon="mdiFileDocumentEditOutline"
      :title="`Edit Laporan Pilot Project ${scopeLabel}`"
      main
    />

    <CardBox>
      <form class="space-y-5" @submit.prevent="submit">
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Judul Laporan</label>
          <input
            v-model="form.judul_laporan"
            type="text"
            class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
          >
          <p v-if="form.errors.judul_laporan" class="mt-1 text-xs text-rose-600">{{ form.errors.judul_laporan }}</p>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Tahun Awal</label>
            <input
              v-model.number="form.tahun_awal"
              type="number"
              class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
            >
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Tahun Akhir</label>
            <input
              v-model.number="form.tahun_akhir"
              type="number"
              class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
            >
          </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Dasar Hukum</label>
            <textarea v-model="form.dasar_hukum" rows="3" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" />
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Pendahuluan</label>
            <textarea v-model="form.pendahuluan" rows="3" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" />
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Maksud dan Tujuan</label>
            <textarea v-model="form.maksud_tujuan" rows="3" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" />
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Pelaksanaan</label>
            <textarea v-model="form.pelaksanaan" rows="3" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" />
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Dokumentasi</label>
            <textarea v-model="form.dokumentasi" rows="3" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" />
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Penutup</label>
            <textarea v-model="form.penutup" rows="3" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" />
          </div>
        </div>

        <div class="space-y-3">
          <div class="flex items-center justify-between gap-3">
            <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Nilai Indikator</h4>
            <button type="button" class="inline-flex rounded-md border border-sky-300 px-3 py-1.5 text-xs font-semibold text-sky-700 hover:bg-sky-50 dark:border-sky-900/50 dark:text-sky-300 dark:hover:bg-sky-900/20" @click="regenerateCatalogValues">
              Regenerasi Dari Katalog
            </button>
          </div>

          <p class="text-xs text-gray-500 dark:text-gray-400">
            Layout di bawah mengikuti pola pedoman (kolom tahun dan semester).
          </p>
          <p v-if="form.errors.values" class="text-xs text-rose-600">{{ form.errors.values }}</p>

          <div
            v-for="section in groupedValues"
            :key="section.key"
            class="rounded-md border border-gray-200 p-3 dark:border-slate-700"
          >
            <h5 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ section.label }}</h5>

            <div
              v-for="cluster in section.clusters"
              :key="`${section.key}-${cluster.key}`"
              class="mt-3 overflow-x-auto"
            >
              <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300">
                {{ cluster.key }}. {{ cluster.label }}
              </p>

              <table class="w-full min-w-[1200px] text-xs">
                <thead class="border-b border-gray-200 dark:border-slate-700">
                  <tr class="text-left text-gray-600 dark:text-gray-300">
                    <th rowspan="2" class="px-2 py-2 font-semibold text-center w-10">NO</th>
                    <th rowspan="2" class="px-2 py-2 font-semibold">Data Utama yang Dimonitor</th>
                    <th
                      v-for="year in periodYears"
                      :key="`${section.key}-${cluster.key}-year-${year}`"
                      colspan="2"
                      class="px-2 py-2 font-semibold text-center"
                    >
                      {{ year }}
                    </th>
                    <th rowspan="2" class="px-2 py-2 font-semibold">Evaluasi</th>
                    <th v-if="section.key === 'data_dukung'" rowspan="2" class="px-2 py-2 font-semibold">Keterangan</th>
                  </tr>
                  <tr class="text-left text-gray-600 dark:text-gray-300">
                    <template v-for="year in periodYears" :key="`${section.key}-${cluster.key}-sem-${year}`">
                      <th class="w-20 min-w-[5rem] px-2 py-2 text-center font-semibold">I</th>
                      <th class="w-20 min-w-[5rem] px-2 py-2 text-center font-semibold">II</th>
                    </template>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="indicator in cluster.indicators"
                    :key="`${section.key}-${cluster.key}-${indicator.code}`"
                    class="border-b border-gray-100 dark:border-slate-800"
                  >
                    <td class="px-2 py-2 text-center text-gray-700 dark:text-gray-300">{{ indicator.no }}</td>
                    <td class="px-2 py-2 text-gray-800 dark:text-gray-200">{{ indicator.label }}</td>
                    <td
                      v-for="cell in indicator.cells"
                      :key="`${section.key}-${cluster.key}-${indicator.code}-${cell?.year}-${cell?.semester}`"
                      class="min-w-[5rem] px-1.5 py-2"
                    >
                      <input
                        v-if="cell"
                        v-model.number="cell.value"
                        type="number"
                        min="0"
                        class="w-full min-w-[5rem] rounded-md border-gray-300 px-2 text-center text-xs shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                      >
                      <span v-else class="text-gray-400">-</span>
                    </td>
                    <td class="px-2 py-2">
                      <input
                        v-if="indicator.noteCell"
                        v-model="indicator.noteCell.evaluation_note"
                        type="text"
                        class="w-full rounded-md border-gray-300 text-xs shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                      >
                      <span v-else class="text-gray-400">-</span>
                    </td>
                    <td v-if="section.key === 'data_dukung'" class="px-2 py-2">
                      <input
                        v-if="indicator.noteCell"
                        v-model="indicator.noteCell.keterangan_note"
                        type="text"
                        class="w-full rounded-md border-gray-300 text-xs shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                      >
                      <span v-else class="text-gray-400">-</span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="flex items-center justify-end gap-2">
          <Link
            :href="scopePrefix"
            class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
          >
            Batal
          </Link>
          <button
            type="submit"
            class="inline-flex rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60"
            :disabled="form.processing"
          >
            Simpan Perubahan
          </button>
        </div>
      </form>
    </CardBox>
  </SectionMain>
</template>
