<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { mdiHandHeart } from '@mdi/js'

const props = defineProps({
  entry: {
    type: Object,
    required: true,
  },
})

const form = useForm({
  transaction_date: props.entry.transaction_date ?? '',
  source: props.entry.source ?? 'kas_tunai',
  description: props.entry.description ?? '',
  reference_number: props.entry.reference_number ?? '',
  entry_type: props.entry.entry_type ?? 'pemasukan',
  amount: props.entry.amount ?? 0,
})

const submit = () => {
  form.put(`/desa/buku-keuangan/${props.entry.id}`)
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiHandHeart" title="Edit Transaksi Buku Keuangan Desa" main />

    <CardBox class="max-w-3xl">
      <form class="space-y-5" @submit.prevent="submit">
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Uraian</label>
          <input v-model="form.description" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
          <p v-if="form.errors.description" class="mt-1 text-xs text-rose-600">{{ form.errors.description }}</p>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Sumber</label>
            <select v-model="form.source" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
              <option value="kas_tunai">Kas Tunai</option>
              <option value="bank">Bank</option>
              <option value="pusat">Pusat</option>
              <option value="provinsi">Provinsi</option>
              <option value="kabupaten">Kabupaten</option>
              <option value="pihak_ketiga">Pihak Ketiga</option>
              <option value="swadaya">Swadaya</option>
              <option value="lainnya">Lainnya</option>
            </select>
            <p v-if="form.errors.source" class="mt-1 text-xs text-rose-600">{{ form.errors.source }}</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Jenis Transaksi</label>
            <select v-model="form.entry_type" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
              <option value="pemasukan">Pemasukan</option>
              <option value="pengeluaran">Pengeluaran</option>
            </select>
            <p v-if="form.errors.entry_type" class="mt-1 text-xs text-rose-600">{{ form.errors.entry_type }}</p>
          </div>
        </div>

        <div class="grid gap-5 md:grid-cols-3">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Transaksi</label>
            <input v-model="form.transaction_date" type="date" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.transaction_date" class="mt-1 text-xs text-rose-600">{{ form.errors.transaction_date }}</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor Bukti Kas</label>
            <input v-model="form.reference_number" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
            <p v-if="form.errors.reference_number" class="mt-1 text-xs text-rose-600">{{ form.errors.reference_number }}</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Nominal</label>
            <input v-model="form.amount" type="number" min="0.01" step="0.01" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.amount" class="mt-1 text-xs text-rose-600">{{ form.errors.amount }}</p>
          </div>
        </div>

        <div class="flex items-center justify-end gap-2">
          <Link href="/desa/buku-keuangan" class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
            Batal
          </Link>
          <button type="submit" class="inline-flex rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60" :disabled="form.processing">
            Update
          </button>
        </div>
      </form>
    </CardBox>
  </SectionMain>
</template>

