<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { mdiStore } from '@mdi/js'

const form = useForm({
  nama_kejar_paket: '',
  jenis_kejar_paket: '',
  jumlah_warga_belajar_l: '',
  jumlah_warga_belajar_p: '',
  jumlah_pengajar_l: '',
  jumlah_pengajar_p: '',
})

const submit = () => {
  form.post('/kecamatan/kejar-paket')
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiStore" title="Tambah Kejar Paket Kecamatan" main />

    <CardBox class="max-w-4xl">
      <form class="space-y-5" @submit.prevent="submit">
        <div class="grid gap-5 md:grid-cols-3">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Kejar Paket</label>
            <input v-model="form.nama_kejar_paket" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.nama_kejar_paket" class="mt-1 text-xs text-rose-600">{{ form.errors.nama_kejar_paket }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Jenis Kejar Paket/KF/PAUD</label>
            <input v-model="form.jenis_kejar_paket" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.jenis_kejar_paket" class="mt-1 text-xs text-rose-600">{{ form.errors.jenis_kejar_paket }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Warga Belajar/Siswa (L)</label>
            <input v-model="form.jumlah_warga_belajar_l" type="number" min="0" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.jumlah_warga_belajar_l" class="mt-1 text-xs text-rose-600">{{ form.errors.jumlah_warga_belajar_l }}</p>
          </div>
        </div>

        <div class="grid gap-5 md:grid-cols-3">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Warga Belajar/Siswa (P)</label>
            <input v-model="form.jumlah_warga_belajar_p" type="number" min="0" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.jumlah_warga_belajar_p" class="mt-1 text-xs text-rose-600">{{ form.errors.jumlah_warga_belajar_p }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Pengajar (L)</label>
            <input v-model="form.jumlah_pengajar_l" type="number" min="0" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.jumlah_pengajar_l" class="mt-1 text-xs text-rose-600">{{ form.errors.jumlah_pengajar_l }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Pengajar (P)</label>
            <input v-model="form.jumlah_pengajar_p" type="number" min="0" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.jumlah_pengajar_p" class="mt-1 text-xs text-rose-600">{{ form.errors.jumlah_pengajar_p }}</p>
          </div>
        </div>

        <div class="flex items-center justify-end gap-2">
          <Link href="/kecamatan/kejar-paket" class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
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






