<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { mdiAccountGroup } from '@mdi/js'

const form = useForm({
  desa: '',
  nama_bkr: '',
  no_tgl_sk: '',
  nama_ketua_kelompok: '',
  jumlah_anggota: 0,
  kegiatan: '',
})

const submit = () => {
  form.post('/kecamatan/bkr')
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiAccountGroup" title="Tambah BKR Kecamatan" main />

    <CardBox class="max-w-4xl">
      <form class="space-y-5" @submit.prevent="submit">
        <div class="grid gap-5 md:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Desa</label>
            <input v-model="form.desa" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.desa" class="mt-1 text-xs text-rose-600">{{ form.errors.desa }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama BKR</label>
            <input v-model="form.nama_bkr" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.nama_bkr" class="mt-1 text-xs text-rose-600">{{ form.errors.nama_bkr }}</p>
          </div>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">No/Tgl SK</label>
            <input v-model="form.no_tgl_sk" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.no_tgl_sk" class="mt-1 text-xs text-rose-600">{{ form.errors.no_tgl_sk }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Ketua Kelompok</label>
            <input v-model="form.nama_ketua_kelompok" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.nama_ketua_kelompok" class="mt-1 text-xs text-rose-600">{{ form.errors.nama_ketua_kelompok }}</p>
          </div>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah Anggota</label>
          <input v-model="form.jumlah_anggota" type="number" min="0" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
          <p v-if="form.errors.jumlah_anggota" class="mt-1 text-xs text-rose-600">{{ form.errors.jumlah_anggota }}</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Kegiatan</label>
          <textarea v-model="form.kegiatan" rows="4" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required></textarea>
          <p v-if="form.errors.kegiatan" class="mt-1 text-xs text-rose-600">{{ form.errors.kegiatan }}</p>
        </div>

        <div class="flex items-center justify-end gap-2">
          <Link href="/kecamatan/bkr" class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
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

