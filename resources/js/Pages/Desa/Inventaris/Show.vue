<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { formatDateForDisplay } from '@/utils/dateFormatter'
import { Link } from '@inertiajs/vue3'
import { mdiPackageVariant } from '@mdi/js'

const props = defineProps({
  inventaris: {
    type: Object,
    required: true,
  },
})

const formatCondition = (value) => value.replace('_', ' ')
const formatDate = (value) => formatDateForDisplay(value)
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiPackageVariant" title="Detail Inventaris Desa" main />

    <CardBox class="max-w-3xl space-y-4">
      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Nama Barang</p>
        <p class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ props.inventaris.name }}</p>
      </div>

      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Deskripsi</p>
        <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.inventaris.keterangan || props.inventaris.description || '-' }}</p>
      </div>

      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Asal Barang</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.inventaris.asal_barang || '-' }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Tanggal Penerimaan/Pembelian</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ formatDate(props.inventaris.tanggal_penerimaan) }}</p>
        </div>
      </div>

      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Jumlah</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.inventaris.quantity }} {{ props.inventaris.unit }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Kondisi</p>
          <p class="text-sm capitalize text-gray-700 dark:text-gray-300">{{ formatCondition(props.inventaris.condition) }}</p>
        </div>
      </div>

      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Tempat Penyimpanan</p>
        <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.inventaris.tempat_penyimpanan || '-' }}</p>
      </div>

      <div class="flex items-center justify-end gap-2">
        <Link
          href="/desa/inventaris"
          class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
        >
          Kembali
        </Link>
        <Link
          :href="`/desa/inventaris/${props.inventaris.id}/edit`"
          class="inline-flex rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700"
        >
          Edit
        </Link>
      </div>
    </CardBox>
  </SectionMain>
</template>
