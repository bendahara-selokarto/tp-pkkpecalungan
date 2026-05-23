<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { mdiCamera } from '@mdi/js'

const form = useForm({
  title: '',
  activity_date: '',
  description: '',
  image_upload: null,
})

const setFile = (event) => {
  const [file] = event.target.files || []
  form.image_upload = file || null
}

const submit = () => {
  form.post('/desa/foto-kegiatan', {
    forceFormData: true,
  })
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiCamera" title="Unggah Foto Kegiatan" main />

    <CardBox class="max-w-2xl">
      <form class="space-y-5" @submit.prevent="submit">
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Judul Kegiatan</label>
          <input v-model="form.title" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
          <p v-if="form.errors.title" class="mt-1 text-xs text-rose-600">{{ form.errors.title }}</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Kegiatan</label>
          <input v-model="form.activity_date" type="date" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
          <p v-if="form.errors.activity_date" class="mt-1 text-xs text-rose-600">{{ form.errors.activity_date }}</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Keterangan (Opsional)</label>
          <textarea v-model="form.description" rows="3" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" />
          <p v-if="form.errors.description" class="mt-1 text-xs text-rose-600">{{ form.errors.description }}</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Unggah Foto</label>
          <input
            type="file"
            accept="image/*"
            class="block w-full text-sm text-gray-700 file:mr-4 file:rounded-md file:border-0 file:bg-emerald-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-emerald-700 hover:file:bg-emerald-100 dark:text-slate-200 dark:file:bg-emerald-900/30 dark:file:text-emerald-300"
            @change="setFile"
          >
          <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Maksimum 5MB. Format gambar umum (jpg, png, webp).</p>
          <p v-if="form.errors.image_upload" class="mt-1 text-xs text-rose-600">{{ form.errors.image_upload }}</p>
        </div>

        <div class="flex items-center justify-end gap-2">
          <Link href="/desa/foto-kegiatan" class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
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
