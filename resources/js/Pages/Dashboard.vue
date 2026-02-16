<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import CardBoxWidget from '@/admin-one/components/CardBoxWidget.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { mdiChartTimelineVariant, mdiClipboardList, mdiCheckCircle, mdiPencilCircle } from '@mdi/js'

defineProps({
  dashboardStats: {
    type: Object,
    default: () => ({
      total: 0,
      this_month: 0,
      published: 0,
      draft: 0,
    }),
  },
})
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiChartTimelineVariant" title="Dashboard" main />

    <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">
      <CardBoxWidget :icon="mdiClipboardList" :number="dashboardStats.total" label="Total Activity" color="text-blue-500" />
      <CardBoxWidget :icon="mdiChartTimelineVariant" :number="dashboardStats.this_month" label="Bulan Ini" color="text-indigo-500" />
      <CardBoxWidget :icon="mdiCheckCircle" :number="dashboardStats.published" label="Published" color="text-emerald-500" />
      <CardBoxWidget :icon="mdiPencilCircle" :number="dashboardStats.draft" label="Draft" color="text-amber-500" />
    </div>

    <CardBox>
      <p class="text-sm text-gray-600 dark:text-gray-300">
        Integrasi Admin One berhasil. Anda bisa lanjut migrasi halaman Blade lain ke Inertia secara bertahap.
      </p>
    </CardBox>
  </SectionMain>
</template>
