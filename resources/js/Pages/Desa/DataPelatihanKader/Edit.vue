<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { mdiAccountGroup } from '@mdi/js'

const props = defineProps({
  dataPelatihanKader: {
    type: Object,
    required: true,
  },
  statusSertifikatOptions: {
    type: Array,
    required: true,
  },
})

const form = useForm({
  nomor_registrasi: props.dataPelatihanKader.nomor_registrasi,
  nama_lengkap_kader: props.dataPelatihanKader.nama_lengkap_kader,
  tanggal_masuk_tp_pkk: props.dataPelatihanKader.tanggal_masuk_tp_pkk,
  jabatan_fungsi: props.dataPelatihanKader.jabatan_fungsi,
  nomor_urut_pelatihan: props.dataPelatihanKader.nomor_urut_pelatihan,
  judul_pelatihan: props.dataPelatihanKader.judul_pelatihan,
  jenis_kriteria_kaderisasi: props.dataPelatihanKader.jenis_kriteria_kaderisasi,
  tahun_penyelenggaraan: props.dataPelatihanKader.tahun_penyelenggaraan,
  institusi_penyelenggara: props.dataPelatihanKader.institusi_penyelenggara,
  status_sertifikat: props.dataPelatihanKader.status_sertifikat,
})

const submit = () => {
  form.put(`/desa/data-pelatihan-kader/${props.dataPelatihanKader.id}`)
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiAccountGroup" title="Edit Data Pelatihan Kader Desa" main />

    <CardBox class="max-w-6xl">
      <form class="space-y-5" @submit.prevent="submit">
        <div class="grid gap-5 md:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">No Registrasi</label>
            <input v-model="form.nomor_registrasi" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.nomor_registrasi" class="mt-1 text-xs text-rose-600">{{ form.errors.nomor_registrasi }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Lengkap Kader</label>
            <input v-model="form.nama_lengkap_kader" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.nama_lengkap_kader" class="mt-1 text-xs text-rose-600">{{ form.errors.nama_lengkap_kader }}</p>
          </div>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal/Bulan/Tahun atau Sejak Tahun Masuk TP PKK</label>
            <input v-model="form.tanggal_masuk_tp_pkk" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.tanggal_masuk_tp_pkk" class="mt-1 text-xs text-rose-600">{{ form.errors.tanggal_masuk_tp_pkk }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Jabatan/Fungsi dalam TP PKK</label>
            <input v-model="form.jabatan_fungsi" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.jabatan_fungsi" class="mt-1 text-xs text-rose-600">{{ form.errors.jabatan_fungsi }}</p>
          </div>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">No Urut Pelatihan</label>
            <input v-model="form.nomor_urut_pelatihan" type="number" min="1" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.nomor_urut_pelatihan" class="mt-1 text-xs text-rose-600">{{ form.errors.nomor_urut_pelatihan }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Judul Pelatihan</label>
            <input v-model="form.judul_pelatihan" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.judul_pelatihan" class="mt-1 text-xs text-rose-600">{{ form.errors.judul_pelatihan }}</p>
          </div>
        </div>

        <div class="grid gap-5 md:grid-cols-3">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Jenis Kriteria Kaderisasi</label>
            <input v-model="form.jenis_kriteria_kaderisasi" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.jenis_kriteria_kaderisasi" class="mt-1 text-xs text-rose-600">{{ form.errors.jenis_kriteria_kaderisasi }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Tahun Penyelenggaraan</label>
            <input v-model="form.tahun_penyelenggaraan" type="number" min="1900" max="2100" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.tahun_penyelenggaraan" class="mt-1 text-xs text-rose-600">{{ form.errors.tahun_penyelenggaraan }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Status Sertifikat</label>
            <select v-model="form.status_sertifikat" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
              <option v-for="status in props.statusSertifikatOptions" :key="status" :value="status">{{ status }}</option>
            </select>
            <p v-if="form.errors.status_sertifikat" class="mt-1 text-xs text-rose-600">{{ form.errors.status_sertifikat }}</p>
          </div>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Institusi Penyelenggara</label>
          <input v-model="form.institusi_penyelenggara" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
          <p v-if="form.errors.institusi_penyelenggara" class="mt-1 text-xs text-rose-600">{{ form.errors.institusi_penyelenggara }}</p>
        </div>

        <div class="flex items-center justify-end gap-2">
          <Link href="/desa/data-pelatihan-kader" class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
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
