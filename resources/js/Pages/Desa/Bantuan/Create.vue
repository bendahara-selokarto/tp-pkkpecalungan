<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { mdiHandHeart } from '@mdi/js'

const form = useForm({
  lokasi_penerima: '',
  jenis_bantuan: 'uang',
  keterangan: '',
  asal_bantuan: 'pusat',
  jumlah: 0,
  tanggal: '',
})

const submit = () => {
  form.post('/desa/bantuans')
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiHandHeart" title="Tambah Bantuan Desa" main />

    <CardBox class="max-w-3xl">
      <form class="space-y-5" @submit.prevent="submit">
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Lokasi Penerima (Sasaran)</label>
          <input v-model="form.lokasi_penerima" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
          <p v-if="form.errors.lokasi_penerima" class="mt-1 text-xs text-rose-600">{{ form.errors.lokasi_penerima }}</p>
        </div>

        <div class="grid gap-5 md:grid-cols-3">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal</label>
            <input v-model="form.tanggal" type="date" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.tanggal" class="mt-1 text-xs text-rose-600">{{ form.errors.tanggal }}</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Asal Bantuan</label>
            <select v-model="form.asal_bantuan" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
              <option value="pusat">Pusat</option>
              <option value="provinsi">Provinsi</option>
              <option value="kabupaten">Kabupaten</option>
              <option value="pihak_ketiga">Pihak Ketiga</option>
              <option value="lainnya">Lainnya</option>
            </select>
            <p v-if="form.errors.asal_bantuan" class="mt-1 text-xs text-rose-600">{{ form.errors.asal_bantuan }}</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Jenis Bantuan</label>
            <select v-model="form.jenis_bantuan" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
              <option value="uang">Uang</option>
              <option value="barang">Barang</option>
            </select>
            <p v-if="form.errors.jenis_bantuan" class="mt-1 text-xs text-rose-600">{{ form.errors.jenis_bantuan }}</p>
          </div>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah</label>
            <input v-model="form.jumlah" type="number" min="0" step="0.01" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.jumlah" class="mt-1 text-xs text-rose-600">{{ form.errors.jumlah }}</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Keterangan</label>
            <textarea v-model="form.keterangan" rows="3" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" />
            <p v-if="form.errors.keterangan" class="mt-1 text-xs text-rose-600">{{ form.errors.keterangan }}</p>
          </div>
        </div>

        <div class="flex items-center justify-end gap-2">
          <Link href="/desa/bantuans" class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
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

