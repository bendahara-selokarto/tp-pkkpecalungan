<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { formatDateForDisplay } from '@/utils/dateFormatter'
import { Link } from '@inertiajs/vue3'
import { mdiHandHeart } from '@mdi/js'

const props = defineProps({
  entry: {
    type: Object,
    required: true,
  },
})

const formatSource = (value) => value.replace('_', ' ')
const formatEntryType = (value) => value === 'pengeluaran' ? 'Pengeluaran' : 'Pemasukan'
const formatAmount = (value) => new Intl.NumberFormat('id-ID').format(Number(value))
const formatDate = (value) => formatDateForDisplay(value)
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiHandHeart" title="Detail Buku Keuangan Desa" main />

    <CardBox class="max-w-3xl space-y-4">
      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Uraian</p>
        <p class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ props.entry.description }}</p>
      </div>

      <div class="grid gap-4 md:grid-cols-3">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Tanggal Transaksi</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ formatDate(props.entry.transaction_date) }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Sumber</p>
          <p class="text-sm capitalize text-gray-700 dark:text-gray-300">{{ formatSource(props.entry.source) }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Jenis Transaksi</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ formatEntryType(props.entry.entry_type) }}</p>
        </div>
      </div>

      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Nominal</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">Rp {{ formatAmount(props.entry.amount) }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Nomor Bukti Kas</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.entry.reference_number || '-' }}</p>
        </div>
      </div>

      <div class="flex items-center justify-end gap-2">
        <Link href="/desa/buku-keuangan" class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
          Kembali
        </Link>
        <Link :href="`/desa/buku-keuangan/${props.entry.id}/edit`" class="inline-flex rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
          Edit
        </Link>
      </div>
    </CardBox>
  </SectionMain>
</template>
