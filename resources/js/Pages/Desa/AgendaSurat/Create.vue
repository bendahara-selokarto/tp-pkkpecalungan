<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { mdiEmailPlusOutline } from '@mdi/js'

const form = useForm({
  jenis_surat: 'masuk',
  tanggal_terima: '',
  tanggal_surat: '',
  nomor_surat: '',
  asal_surat: '',
  dari: '',
  kepada: '',
  perihal: '',
  lampiran: '',
  diteruskan_kepada: '',
  tembusan: '',
  keterangan: '',
  data_dukung_upload: null,
})

const setFile = (event) => {
  const [file] = event.target.files || []
  form.data_dukung_upload = file || null
}

const submit = () => {
  form.post('/desa/agenda-surat', {
    forceFormData: true,
  })
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiEmailPlusOutline" title="Tambah Agenda Surat Desa" main />

    <CardBox class="max-w-4xl">
      <form class="space-y-5" @submit.prevent="submit">
        <div class="grid gap-5 md:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Jenis Surat</label>
            <select v-model="form.jenis_surat" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
              <option value="masuk">Surat Masuk</option>
              <option value="keluar">Surat Keluar</option>
            </select>
            <p v-if="form.errors.jenis_surat" class="mt-1 text-xs text-rose-600">{{ form.errors.jenis_surat }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor Surat</label>
            <input v-model="form.nomor_surat" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.nomor_surat" class="mt-1 text-xs text-rose-600">{{ form.errors.nomor_surat }}</p>
          </div>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Surat</label>
            <input v-model="form.tanggal_surat" type="date" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.tanggal_surat" class="mt-1 text-xs text-rose-600">{{ form.errors.tanggal_surat }}</p>
          </div>
          <div v-if="form.jenis_surat === 'masuk'">
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Terima</label>
            <input v-model="form.tanggal_terima" type="date" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
            <p v-if="form.errors.tanggal_terima" class="mt-1 text-xs text-rose-600">{{ form.errors.tanggal_terima }}</p>
          </div>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Asal Surat</label>
            <input v-model="form.asal_surat" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
            <p v-if="form.errors.asal_surat" class="mt-1 text-xs text-rose-600">{{ form.errors.asal_surat }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Dari</label>
            <input v-model="form.dari" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
            <p v-if="form.errors.dari" class="mt-1 text-xs text-rose-600">{{ form.errors.dari }}</p>
          </div>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Kepada</label>
            <input v-model="form.kepada" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
            <p v-if="form.errors.kepada" class="mt-1 text-xs text-rose-600">{{ form.errors.kepada }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Perihal</label>
            <input v-model="form.perihal" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.perihal" class="mt-1 text-xs text-rose-600">{{ form.errors.perihal }}</p>
          </div>
        </div>

        <div class="grid gap-5 md:grid-cols-3">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Lampiran</label>
            <input v-model="form.lampiran" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
            <p v-if="form.errors.lampiran" class="mt-1 text-xs text-rose-600">{{ form.errors.lampiran }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Diteruskan Kepada</label>
            <input v-model="form.diteruskan_kepada" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
            <p v-if="form.errors.diteruskan_kepada" class="mt-1 text-xs text-rose-600">{{ form.errors.diteruskan_kepada }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Tembusan</label>
            <input v-model="form.tembusan" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
            <p v-if="form.errors.tembusan" class="mt-1 text-xs text-rose-600">{{ form.errors.tembusan }}</p>
          </div>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Keterangan</label>
          <textarea v-model="form.keterangan" rows="3" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" />
          <p v-if="form.errors.keterangan" class="mt-1 text-xs text-rose-600">{{ form.errors.keterangan }}</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Data Dukung (Unggah Berkas)</label>
          <input
            type="file"
            accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,image/*"
            class="block w-full text-sm text-gray-700 file:mr-4 file:rounded-md file:border-0 file:bg-sky-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-sky-700 hover:file:bg-sky-100 dark:text-slate-200 dark:file:bg-sky-900/30 dark:file:text-sky-300"
            @change="setFile"
          >
          <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Maksimum 10MB. Format: PDF, Office, atau gambar.</p>
          <p v-if="form.errors.data_dukung_upload" class="mt-1 text-xs text-rose-600">{{ form.errors.data_dukung_upload }}</p>
        </div>

        <div class="flex items-center justify-end gap-2">
          <Link href="/desa/agenda-surat" class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
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
