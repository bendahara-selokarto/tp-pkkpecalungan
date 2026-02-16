<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link } from '@inertiajs/vue3'
import { mdiHandHeart } from '@mdi/js'

const props = defineProps({
  bantuan: {
    type: Object,
    required: true,
  },
})

const formatSource = (value) => value.replace('_', ' ')
const formatAmount = (value) => new Intl.NumberFormat('id-ID').format(Number(value))
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiHandHeart" title="Detail Bantuan Desa" main />

    <CardBox class="max-w-3xl space-y-4">
      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Nama Bantuan</p>
        <p class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ props.bantuan.name }}</p>
      </div>

      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Jenis</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.bantuan.category }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Sumber</p>
          <p class="text-sm capitalize text-gray-700 dark:text-gray-300">{{ formatSource(props.bantuan.source) }}</p>
        </div>
      </div>

      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Nominal</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">Rp {{ formatAmount(props.bantuan.amount) }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Tanggal Diterima</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.bantuan.received_date }}</p>
        </div>
      </div>

      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Deskripsi</p>
        <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.bantuan.description || '-' }}</p>
      </div>

      <div class="flex items-center justify-end gap-2">
        <Link href="/desa/bantuans" class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
          Kembali
        </Link>
        <Link :href="`/desa/bantuans/${props.bantuan.id}/edit`" class="inline-flex rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
          Edit
        </Link>
      </div>
    </CardBox>
  </SectionMain>
</template>
