<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { mdiClipboardList } from '@mdi/js'

const props = defineProps({
  activity: {
    type: Object,
    required: true,
  },
})

const form = useForm({
  title: props.activity.title ?? '',
  nama_petugas: props.activity.nama_petugas ?? '',
  jabatan_petugas: props.activity.jabatan_petugas ?? '',
  tempat_kegiatan: props.activity.tempat_kegiatan ?? '',
  uraian: props.activity.uraian ?? props.activity.description ?? '',
  tanda_tangan: props.activity.tanda_tangan ?? '',
  activity_date: props.activity.activity_date ?? '',
  status: props.activity.status ?? 'draft',
  image_upload: null,
  document_upload: null,
})

const setFile = (field, event) => {
  const [file] = event.target.files || []
  form[field] = file || null
}

const submit = () => {
  form
    .transform((data) => ({
      ...data,
      description: data.uraian || null,
      _method: 'put',
    }))
    .post(`/kecamatan/activities/${props.activity.id}`, {
      forceFormData: true,
    })
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiClipboardList" title="Edit Buku Kegiatan Kecamatan" main />

    <CardBox class="max-w-3xl">
      <form class="space-y-5" @submit.prevent="submit">
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Judul Kegiatan</label>
          <input v-model="form.title" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
          <p v-if="form.errors.title" class="mt-1 text-xs text-rose-600">{{ form.errors.title }}</p>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Bertugas</label>
            <input v-model="form.nama_petugas" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
            <p v-if="form.errors.nama_petugas" class="mt-1 text-xs text-rose-600">{{ form.errors.nama_petugas }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Jabatan</label>
            <input v-model="form.jabatan_petugas" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
            <p v-if="form.errors.jabatan_petugas" class="mt-1 text-xs text-rose-600">{{ form.errors.jabatan_petugas }}</p>
          </div>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Uraian</label>
          <textarea v-model="form.uraian" rows="4" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" />
          <p v-if="form.errors.uraian" class="mt-1 text-xs text-rose-600">{{ form.errors.uraian }}</p>
        </div>

        <div class="grid gap-5 md:grid-cols-3">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Kegiatan</label>
            <input v-model="form.activity_date" type="date" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.activity_date" class="mt-1 text-xs text-rose-600">{{ form.errors.activity_date }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Tempat Kegiatan</label>
            <input v-model="form.tempat_kegiatan" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
            <p v-if="form.errors.tempat_kegiatan" class="mt-1 text-xs text-rose-600">{{ form.errors.tempat_kegiatan }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
            <select v-model="form.status" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
              <option value="draft">draft</option>
              <option value="published">published</option>
            </select>
            <p v-if="form.errors.status" class="mt-1 text-xs text-rose-600">{{ form.errors.status }}</p>
          </div>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Tanda Tangan</label>
          <input v-model="form.tanda_tangan" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
          <p v-if="form.errors.tanda_tangan" class="mt-1 text-xs text-rose-600">{{ form.errors.tanda_tangan }}</p>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Upload Gambar</label>
            <a
              v-if="props.activity.image_url"
              :href="props.activity.image_url"
              target="_blank"
              rel="noopener"
              class="mb-2 inline-block text-xs font-medium text-sky-600 hover:underline dark:text-sky-400"
            >
              Lihat gambar saat ini
            </a>
            <input
              type="file"
              accept="image/*"
              class="block w-full text-sm text-gray-700 file:mr-4 file:rounded-md file:border-0 file:bg-emerald-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-emerald-700 hover:file:bg-emerald-100 dark:text-slate-200 dark:file:bg-emerald-900/30 dark:file:text-emerald-300"
              @change="setFile('image_upload', $event)"
            >
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Kosongkan jika tidak ingin mengganti gambar.</p>
            <p v-if="form.errors.image_upload" class="mt-1 text-xs text-rose-600">{{ form.errors.image_upload }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Upload Berkas</label>
            <a
              v-if="props.activity.document_url"
              :href="props.activity.document_url"
              target="_blank"
              rel="noopener"
              class="mb-2 inline-block text-xs font-medium text-sky-600 hover:underline dark:text-sky-400"
            >
              Lihat berkas saat ini
            </a>
            <input
              type="file"
              accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,image/*"
              class="block w-full text-sm text-gray-700 file:mr-4 file:rounded-md file:border-0 file:bg-sky-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-sky-700 hover:file:bg-sky-100 dark:text-slate-200 dark:file:bg-sky-900/30 dark:file:text-sky-300"
              @change="setFile('document_upload', $event)"
            >
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Kosongkan jika tidak ingin mengganti berkas.</p>
            <p v-if="form.errors.document_upload" class="mt-1 text-xs text-rose-600">{{ form.errors.document_upload }}</p>
          </div>
        </div>

        <div class="flex items-center justify-end gap-2">
          <Link href="/kecamatan/activities" class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
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
