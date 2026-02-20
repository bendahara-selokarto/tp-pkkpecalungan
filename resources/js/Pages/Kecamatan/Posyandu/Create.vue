<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { mdiStore } from '@mdi/js'

const form = useForm({
  nama_posyandu: '',
  nama_pengelola: '',
  nama_sekretaris: '',
  jenis_posyandu: '',
  jumlah_kader: '',
  jenis_kegiatan: '',
  frekuensi_layanan: '',
  jumlah_pengunjung_l: '',
  jumlah_pengunjung_p: '',
  jumlah_petugas_l: '',
  jumlah_petugas_p: '',
})

const submit = () => {
  form.post('/kecamatan/posyandu')
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiStore" title="Tambah Posyandu Kecamatan" main />

    <CardBox class="max-w-6xl">
      <form class="space-y-5" @submit.prevent="submit">
        <div class="grid gap-5 md:grid-cols-3">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Posyandu</label>
            <input v-model="form.nama_posyandu" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.nama_posyandu" class="mt-1 text-xs text-rose-600">{{ form.errors.nama_posyandu }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Pengelola</label>
            <input v-model="form.nama_pengelola" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.nama_pengelola" class="mt-1 text-xs text-rose-600">{{ form.errors.nama_pengelola }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Sekretaris</label>
            <input v-model="form.nama_sekretaris" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.nama_sekretaris" class="mt-1 text-xs text-rose-600">{{ form.errors.nama_sekretaris }}</p>
          </div>
        </div>

        <div class="grid gap-5 md:grid-cols-3">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Jenis Posyandu</label>
            <input v-model="form.jenis_posyandu" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.jenis_posyandu" class="mt-1 text-xs text-rose-600">{{ form.errors.jenis_posyandu }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah Kader</label>
            <input v-model="form.jumlah_kader" type="number" min="0" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.jumlah_kader" class="mt-1 text-xs text-rose-600">{{ form.errors.jumlah_kader }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Jenis Kegiatan</label>
            <input v-model="form.jenis_kegiatan" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.jenis_kegiatan" class="mt-1 text-xs text-rose-600">{{ form.errors.jenis_kegiatan }}</p>
          </div>
        </div>

        <div class="grid gap-5 md:grid-cols-4">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Frekuensi</label>
            <input v-model="form.frekuensi_layanan" type="number" min="0" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.frekuensi_layanan" class="mt-1 text-xs text-rose-600">{{ form.errors.frekuensi_layanan }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Pengunjung (L)</label>
            <input v-model="form.jumlah_pengunjung_l" type="number" min="0" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.jumlah_pengunjung_l" class="mt-1 text-xs text-rose-600">{{ form.errors.jumlah_pengunjung_l }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Pengunjung (P)</label>
            <input v-model="form.jumlah_pengunjung_p" type="number" min="0" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.jumlah_pengunjung_p" class="mt-1 text-xs text-rose-600">{{ form.errors.jumlah_pengunjung_p }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Petugas (L)</label>
            <input v-model="form.jumlah_petugas_l" type="number" min="0" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.jumlah_petugas_l" class="mt-1 text-xs text-rose-600">{{ form.errors.jumlah_petugas_l }}</p>
          </div>
        </div>

        <div class="grid gap-5 md:grid-cols-4">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Petugas (P)</label>
            <input v-model="form.jumlah_petugas_p" type="number" min="0" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.jumlah_petugas_p" class="mt-1 text-xs text-rose-600">{{ form.errors.jumlah_petugas_p }}</p>
          </div>
        </div>

        <div class="flex items-center justify-end gap-2">
          <Link href="/kecamatan/posyandu" class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
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
