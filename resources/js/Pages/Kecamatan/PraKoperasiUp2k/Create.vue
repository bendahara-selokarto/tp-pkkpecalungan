<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { mdiAccountGroup } from '@mdi/js'

const tingkatOptions = [
  { value: 'pemula', label: 'Pemula' },
  { value: 'madya', label: 'Madya' },
  { value: 'utama', label: 'Utama' },
  { value: 'mandiri', label: 'Mandiri' },
]

const form = useForm({
  tingkat: tingkatOptions[0]?.value ?? '',
  jumlah_kelompok: '',
  jumlah_peserta: '',
  keterangan: '',
})

const submit = () => {
  form.post('/kecamatan/pra-koperasi-up2k')
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiAccountGroup" title="Tambah Pra Koperasi/UP2K Kecamatan" main />

    <CardBox class="max-w-3xl">
      <form class="space-y-5" @submit.prevent="submit">
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Tingkat</label>
          <select v-model="form.tingkat" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <option v-for="option in tingkatOptions" :key="option.value" :value="option.value">
              {{ option.label }}
            </option>
          </select>
          <p v-if="form.errors.tingkat" class="mt-1 text-xs text-rose-600">{{ form.errors.tingkat }}</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah Kelompok</label>
          <input v-model="form.jumlah_kelompok" type="number" min="0" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
          <p v-if="form.errors.jumlah_kelompok" class="mt-1 text-xs text-rose-600">{{ form.errors.jumlah_kelompok }}</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah Peserta</label>
          <input v-model="form.jumlah_peserta" type="number" min="0" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
          <p v-if="form.errors.jumlah_peserta" class="mt-1 text-xs text-rose-600">{{ form.errors.jumlah_peserta }}</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Keterangan</label>
          <textarea v-model="form.keterangan" rows="3" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"></textarea>
          <p v-if="form.errors.keterangan" class="mt-1 text-xs text-rose-600">{{ form.errors.keterangan }}</p>
        </div>

        <div class="flex items-center justify-end gap-2">
          <Link href="/kecamatan/pra-koperasi-up2k" class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
            Batal
          </Link>
          <button type="submit" class="inline-flex rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60" :disabled="form.processing">
            Simpan
          </button>
        </div>
      </form>
    </CardBox>
  </SectionMain>
</template>
