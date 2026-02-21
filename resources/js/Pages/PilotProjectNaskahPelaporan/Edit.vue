<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { mdiFileDocumentEditOutline } from '@mdi/js'
import { computed } from 'vue'

const props = defineProps({
  scopeLabel: {
    type: String,
    required: true,
  },
  scopePrefix: {
    type: String,
    required: true,
  },
  defaultLetterFrom: {
    type: String,
    default: '',
  },
  attachmentCategories: {
    type: Object,
    default: () => ({}),
  },
  report: {
    type: Object,
    required: true,
  },
})

const form = useForm({
  judul_laporan: props.report.judul_laporan ?? '',
  surat_kepada: props.report.surat_kepada ?? '',
  surat_dari: props.report.surat_dari ?? props.defaultLetterFrom ?? '',
  surat_tembusan: props.report.surat_tembusan ?? '',
  surat_tanggal: props.report.surat_tanggal ?? '',
  surat_nomor: props.report.surat_nomor ?? '',
  surat_sifat: props.report.surat_sifat ?? '',
  surat_lampiran: props.report.surat_lampiran ?? '',
  surat_hal: props.report.surat_hal ?? props.report.judul_laporan ?? '',
  dasar_pelaksanaan: props.report.dasar_pelaksanaan ?? '',
  pendahuluan: props.report.pendahuluan ?? '',
  pelaksanaan_1: props.report.pelaksanaan_1 ?? '',
  pelaksanaan_2: props.report.pelaksanaan_2 ?? '',
  pelaksanaan_3: props.report.pelaksanaan_3 ?? '',
  pelaksanaan_4: props.report.pelaksanaan_4 ?? '',
  pelaksanaan_5: props.report.pelaksanaan_5 ?? '',
  penutup: props.report.penutup ?? '',
  lampiran_6a_foto: [],
  lampiran_6b_foto: [],
  lampiran_6d_dokumen: [],
  lampiran_6e_foto: [],
  remove_attachment_ids: [],
})

const groupedAttachments = computed(() => {
  const base = {
    '6a_photo': [],
    '6b_photo': [],
    '6d_document': [],
    '6e_photo': [],
  }

  for (const attachment of props.report.attachments ?? []) {
    if (!base[attachment.category]) {
      continue
    }

    base[attachment.category].push(attachment)
  }

  return base
})

const setFiles = (field, event) => {
  form[field] = Array.from(event.target.files || [])
}

const toggleRemoveAttachment = (id) => {
  const current = [...form.remove_attachment_ids]
  const index = current.indexOf(id)

  if (index >= 0) {
    current.splice(index, 1)
  } else {
    current.push(id)
  }

  form.remove_attachment_ids = current
}

const isMarkedForRemoval = (id) => form.remove_attachment_ids.includes(id)

const fileErrors = (prefix) => {
  return Object.entries(form.errors)
    .filter(([key]) => key === prefix || key.startsWith(`${prefix}.`))
    .map(([, value]) => value)
}

const submit = () => {
  form.transform((data) => ({
    ...data,
    _method: 'put',
  })).post(`${props.scopePrefix}/${props.report.id}`, {
    preserveScroll: true,
    forceFormData: true,
  })
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton
      :icon="mdiFileDocumentEditOutline"
      :title="`Edit Naskah Pelaporan ${scopeLabel}`"
      main
    />

    <CardBox>
      <form class="space-y-5" @submit.prevent="submit">
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Judul Laporan</label>
          <input
            v-model="form.judul_laporan"
            type="text"
            class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
          >
          <p v-if="form.errors.judul_laporan" class="mt-1 text-xs text-rose-600">{{ form.errors.judul_laporan }}</p>
        </div>

        <div class="rounded-md border border-gray-200 p-4 dark:border-slate-700">
          <h4 class="mb-3 text-sm font-semibold text-gray-900 dark:text-gray-100">Head Surat</h4>
          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Kepada</label>
              <input v-model="form.surat_kepada" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
              <p v-if="form.errors.surat_kepada" class="mt-1 text-xs text-rose-600">{{ form.errors.surat_kepada }}</p>
            </div>
            <div>
              <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Dari</label>
              <input v-model="form.surat_dari" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
              <p v-if="form.errors.surat_dari" class="mt-1 text-xs text-rose-600">{{ form.errors.surat_dari }}</p>
            </div>
            <div>
              <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Tembusan</label>
              <input v-model="form.surat_tembusan" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
              <p v-if="form.errors.surat_tembusan" class="mt-1 text-xs text-rose-600">{{ form.errors.surat_tembusan }}</p>
            </div>
            <div>
              <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Tanggal</label>
              <input v-model="form.surat_tanggal" type="date" class="w-full rounded-md border-gray-300 text-sm shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
              <p v-if="form.errors.surat_tanggal" class="mt-1 text-xs text-rose-600">{{ form.errors.surat_tanggal }}</p>
            </div>
            <div>
              <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Nomor</label>
              <input v-model="form.surat_nomor" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
              <p v-if="form.errors.surat_nomor" class="mt-1 text-xs text-rose-600">{{ form.errors.surat_nomor }}</p>
            </div>
            <div>
              <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Sifat</label>
              <input v-model="form.surat_sifat" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
              <p v-if="form.errors.surat_sifat" class="mt-1 text-xs text-rose-600">{{ form.errors.surat_sifat }}</p>
            </div>
            <div>
              <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Lampiran</label>
              <input v-model="form.surat_lampiran" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
              <p v-if="form.errors.surat_lampiran" class="mt-1 text-xs text-rose-600">{{ form.errors.surat_lampiran }}</p>
            </div>
            <div>
              <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Hal</label>
              <textarea v-model="form.surat_hal" rows="2" class="w-full rounded-md border-gray-300 text-sm shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"></textarea>
              <p v-if="form.errors.surat_hal" class="mt-1 text-xs text-rose-600">{{ form.errors.surat_hal }}</p>
            </div>
          </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">I. Dasar Pelaksanaan</label>
            <textarea v-model="form.dasar_pelaksanaan" rows="4" class="w-full rounded-md border-gray-300 text-sm shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" ></textarea>
            <p v-if="form.errors.dasar_pelaksanaan" class="mt-1 text-xs text-rose-600">{{ form.errors.dasar_pelaksanaan }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">II. Pendahuluan</label>
            <textarea v-model="form.pendahuluan" rows="4" class="w-full rounded-md border-gray-300 text-sm shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" ></textarea>
            <p v-if="form.errors.pendahuluan" class="mt-1 text-xs text-rose-600">{{ form.errors.pendahuluan }}</p>
          </div>
        </div>

        <div class="rounded-md border border-gray-200 p-4 dark:border-slate-700">
          <h4 class="mb-3 text-sm font-semibold text-gray-900 dark:text-gray-100">IV. Pelaksanaan (1-5)</h4>
          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">1. Kegiatan Pemantauan dan Pembinaan</label>
              <textarea v-model="form.pelaksanaan_1" rows="3" class="w-full rounded-md border-gray-300 text-sm shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" ></textarea>
              <p v-if="form.errors.pelaksanaan_1" class="mt-1 text-xs text-rose-600">{{ form.errors.pelaksanaan_1 }}</p>
            </div>
            <div>
              <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">2. Inovasi peningkatan keberhasilan</label>
              <textarea v-model="form.pelaksanaan_2" rows="3" class="w-full rounded-md border-gray-300 text-sm shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" ></textarea>
              <p v-if="form.errors.pelaksanaan_2" class="mt-1 text-xs text-rose-600">{{ form.errors.pelaksanaan_2 }}</p>
            </div>
            <div>
              <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">3. Evaluasi perkembangan</label>
              <textarea v-model="form.pelaksanaan_3" rows="3" class="w-full rounded-md border-gray-300 text-sm shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" ></textarea>
              <p v-if="form.errors.pelaksanaan_3" class="mt-1 text-xs text-rose-600">{{ form.errors.pelaksanaan_3 }}</p>
            </div>
            <div>
              <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">4. Pembekalan/Pelatihan kader</label>
              <textarea v-model="form.pelaksanaan_4" rows="3" class="w-full rounded-md border-gray-300 text-sm shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" ></textarea>
              <p v-if="form.errors.pelaksanaan_4" class="mt-1 text-xs text-rose-600">{{ form.errors.pelaksanaan_4 }}</p>
            </div>
            <div class="md:col-span-2">
              <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">5. Gambaran kondisi lokasi</label>
              <textarea v-model="form.pelaksanaan_5" rows="3" class="w-full rounded-md border-gray-300 text-sm shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" ></textarea>
              <p v-if="form.errors.pelaksanaan_5" class="mt-1 text-xs text-rose-600">{{ form.errors.pelaksanaan_5 }}</p>
            </div>
          </div>
        </div>

        <div class="rounded-md border border-gray-200 p-4 dark:border-slate-700">
          <h4 class="mb-3 text-sm font-semibold text-gray-900 dark:text-gray-100">Lampiran Tersimpan</h4>
          <div class="grid gap-4 md:grid-cols-2">
            <div v-for="(files, category) in groupedAttachments" :key="category" class="rounded-md border border-gray-100 p-3 dark:border-slate-800">
              <p class="mb-2 text-xs font-semibold text-gray-700 dark:text-gray-300">{{ attachmentCategories[category] || category }}</p>
              <div v-if="files.length === 0" class="text-xs text-gray-500 dark:text-gray-400">Belum ada file.</div>
              <label v-for="file in files" :key="file.id" class="mb-2 flex items-start gap-2 text-xs text-gray-700 dark:text-gray-300">
                <input
                  type="checkbox"
                  :checked="isMarkedForRemoval(file.id)"
                  class="mt-1 rounded border-gray-300"
                  @change="toggleRemoveAttachment(file.id)"
                >
                <a :href="file.file_url" target="_blank" rel="noopener" class="hover:underline">{{ file.original_name }}</a>
              </label>
            </div>
          </div>
        </div>

        <div class="rounded-md border border-gray-200 p-4 dark:border-slate-700">
          <h4 class="mb-3 text-sm font-semibold text-gray-900 dark:text-gray-100">Tambah Lampiran Baru</h4>
          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">{{ attachmentCategories['6a_photo'] }}</label>
              <input type="file" accept="image/*" multiple class="block w-full text-xs" @change="setFiles('lampiran_6a_foto', $event)">
              <p v-for="error in fileErrors('lampiran_6a_foto')" :key="`6a-${error}`" class="mt-1 text-xs text-rose-600">{{ error }}</p>
            </div>
            <div>
              <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">{{ attachmentCategories['6b_photo'] }}</label>
              <input type="file" accept="image/*" multiple class="block w-full text-xs" @change="setFiles('lampiran_6b_foto', $event)">
              <p v-for="error in fileErrors('lampiran_6b_foto')" :key="`6b-${error}`" class="mt-1 text-xs text-rose-600">{{ error }}</p>
            </div>
            <div>
              <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">{{ attachmentCategories['6d_document'] }}</label>
              <input type="file" accept=".pdf,image/*" multiple class="block w-full text-xs" @change="setFiles('lampiran_6d_dokumen', $event)">
              <p v-for="error in fileErrors('lampiran_6d_dokumen')" :key="`6d-${error}`" class="mt-1 text-xs text-rose-600">{{ error }}</p>
            </div>
            <div>
              <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">{{ attachmentCategories['6e_photo'] }}</label>
              <input type="file" accept="image/*" multiple class="block w-full text-xs" @change="setFiles('lampiran_6e_foto', $event)">
              <p v-for="error in fileErrors('lampiran_6e_foto')" :key="`6e-${error}`" class="mt-1 text-xs text-rose-600">{{ error }}</p>
            </div>
          </div>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">V. Penutup</label>
          <textarea
            v-model="form.penutup"
            rows="4"
            class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
          ></textarea>
          <p v-if="form.errors.penutup" class="mt-1 text-xs text-rose-600">{{ form.errors.penutup }}</p>
        </div>

        <div class="flex items-center justify-end gap-2">
          <Link
            :href="scopePrefix"
            class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
          >
            Batal
          </Link>
          <button
            type="submit"
            class="inline-flex rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60"
            :disabled="form.processing"
          >
            Simpan Perubahan
          </button>
        </div>
      </form>
    </CardBox>
  </SectionMain>
</template>

