<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { mdiPackageVariantPlus } from '@mdi/js'

const form = useForm({
  name: '',
  asal_barang: '',
  tanggal_penerimaan: '',
  tempat_penyimpanan: '',
  keterangan: '',
  quantity: 1,
  unit: 'unit',
  condition: 'baik',
})

const submit = () => {
  form
    .transform((data) => ({
      ...data,
      description: data.keterangan || null,
    }))
    .post('/desa/inventaris')
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiPackageVariantPlus" title="Tambah Inventaris Desa" main />

    <CardBox class="max-w-3xl">
      <form class="space-y-5" @submit.prevent="submit">
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Barang</label>
          <input
            v-model="form.name"
            type="text"
            class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
            required
          >
          <p v-if="form.errors.name" class="mt-1 text-xs text-rose-600">{{ form.errors.name }}</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Asal Barang</label>
          <input
            v-model="form.asal_barang"
            type="text"
            class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
          >
          <p v-if="form.errors.asal_barang" class="mt-1 text-xs text-rose-600">{{ form.errors.asal_barang }}</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Keterangan</label>
          <textarea
            v-model="form.keterangan"
            rows="3"
            class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
          />
          <p v-if="form.errors.keterangan" class="mt-1 text-xs text-rose-600">{{ form.errors.keterangan }}</p>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Penerimaan/Pembelian</label>
            <input
              v-model="form.tanggal_penerimaan"
              type="text"
              inputmode="numeric"
              placeholder="DD/MM/YYYY"
              class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
            >
            <p v-if="form.errors.tanggal_penerimaan" class="mt-1 text-xs text-rose-600">{{ form.errors.tanggal_penerimaan }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Tempat Penyimpanan</label>
            <input
              v-model="form.tempat_penyimpanan"
              type="text"
              class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
            >
            <p v-if="form.errors.tempat_penyimpanan" class="mt-1 text-xs text-rose-600">{{ form.errors.tempat_penyimpanan }}</p>
          </div>
        </div>

        <div class="grid gap-5 md:grid-cols-3">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah</label>
            <input
              v-model="form.quantity"
              type="number"
              min="1"
              class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
              required
            >
            <p v-if="form.errors.quantity" class="mt-1 text-xs text-rose-600">{{ form.errors.quantity }}</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Satuan</label>
            <input
              v-model="form.unit"
              type="text"
              class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
              required
            >
            <p v-if="form.errors.unit" class="mt-1 text-xs text-rose-600">{{ form.errors.unit }}</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Kondisi</label>
            <select
              v-model="form.condition"
              class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
              required
            >
              <option value="baik">baik</option>
              <option value="rusak_ringan">rusak ringan</option>
              <option value="rusak_berat">rusak berat</option>
            </select>
            <p v-if="form.errors.condition" class="mt-1 text-xs text-rose-600">{{ form.errors.condition }}</p>
          </div>
        </div>

        <div class="flex items-center justify-end gap-2">
          <Link
            href="/desa/inventaris"
            class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
          >
            Batal
          </Link>
          <button
            type="submit"
            class="inline-flex rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60"
            :disabled="form.processing"
          >
            Simpan
          </button>
        </div>
      </form>
    </CardBox>
  </SectionMain>
</template>
