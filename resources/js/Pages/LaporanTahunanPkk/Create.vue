<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { mdiFileDocumentEditOutline } from '@mdi/js'

const props = defineProps({
  scopeLabel: {
    type: String,
    required: true,
  },
  scopePrefix: {
    type: String,
    required: true,
  },
  defaultTitle: {
    type: String,
    default: '',
  },
  defaultYear: {
    type: Number,
    default: 2026,
  },
  defaultCompiledBy: {
    type: String,
    default: '',
  },
  defaultSignerRole: {
    type: String,
    default: '',
  },
  bidangOptions: {
    type: Array,
    default: () => [],
  },
  bidangLabels: {
    type: Object,
    default: () => ({}),
  },
})

const defaultBidang = props.bidangOptions[0] ?? 'sekretariat'
const emptyEntry = () => ({
  bidang: defaultBidang,
  activity_date: '',
  description: '',
})

const form = useForm({
  judul_laporan: props.defaultTitle || '',
  tahun_laporan: props.defaultYear,
  pendahuluan: '',
  keberhasilan: '',
  hambatan: '',
  kesimpulan: '',
  penutup: '',
  disusun_oleh: props.defaultCompiledBy || '',
  jabatan_penanda_tangan: props.defaultSignerRole || '',
  nama_penanda_tangan: '',
  manual_entries: [emptyEntry()],
})

const addEntry = () => {
  form.manual_entries.push(emptyEntry())
}

const removeEntry = (index) => {
  form.manual_entries.splice(index, 1)
  if (form.manual_entries.length === 0) {
    form.manual_entries.push(emptyEntry())
  }
}

const labelForBidang = (slug) => props.bidangLabels[slug] || slug

const submit = () => {
  form.post(props.scopePrefix, {
    preserveScroll: true,
  })
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton
      :icon="mdiFileDocumentEditOutline"
      :title="`Tambah Laporan Tahunan ${scopeLabel}`"
      main
    />

    <CardBox>
      <form class="space-y-5" @submit.prevent="submit">
        <div class="grid gap-4 md:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Judul Laporan</label>
            <input
              v-model="form.judul_laporan"
              type="text"
              class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
            >
            <p v-if="form.errors.judul_laporan" class="mt-1 text-xs text-rose-600">{{ form.errors.judul_laporan }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Tahun Laporan</label>
            <input
              v-model="form.tahun_laporan"
              type="number"
              min="2000"
              max="2100"
              class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
            >
            <p v-if="form.errors.tahun_laporan" class="mt-1 text-xs text-rose-600">{{ form.errors.tahun_laporan }}</p>
          </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Disusun Oleh</label>
            <input
              v-model="form.disusun_oleh"
              type="text"
              class="w-full rounded-md border-gray-300 text-sm shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
            >
            <p v-if="form.errors.disusun_oleh" class="mt-1 text-xs text-rose-600">{{ form.errors.disusun_oleh }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Jabatan Penanda Tangan</label>
            <input
              v-model="form.jabatan_penanda_tangan"
              type="text"
              class="w-full rounded-md border-gray-300 text-sm shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
            >
            <p v-if="form.errors.jabatan_penanda_tangan" class="mt-1 text-xs text-rose-600">{{ form.errors.jabatan_penanda_tangan }}</p>
          </div>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Penanda Tangan</label>
          <input
            v-model="form.nama_penanda_tangan"
            type="text"
            class="w-full rounded-md border-gray-300 text-sm shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
          >
          <p v-if="form.errors.nama_penanda_tangan" class="mt-1 text-xs text-rose-600">{{ form.errors.nama_penanda_tangan }}</p>
        </div>

        <div class="rounded-md border border-gray-200 p-4 dark:border-slate-700">
          <h4 class="mb-1 text-sm font-semibold text-gray-900 dark:text-gray-100">Isian Pelengkap Kegiatan</h4>
          <p class="mb-3 text-xs text-gray-500 dark:text-gray-400">
            Data kegiatan otomatis tetap diambil dari modul aplikasi (contoh: Buku Kegiatan dan Agenda Surat). Bagian ini untuk melengkapi kegiatan yang belum tersedia di data operasional.
          </p>

          <div class="space-y-3">
            <div
              v-for="(entry, index) in form.manual_entries"
              :key="`entry-${index}`"
              class="rounded-md border border-gray-100 p-3 dark:border-slate-800"
            >
              <div class="grid gap-3 md:grid-cols-4">
                <div>
                  <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Bidang</label>
                  <select
                    v-model="entry.bidang"
                    class="w-full rounded-md border-gray-300 text-sm shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                  >
                    <option v-for="option in bidangOptions" :key="option" :value="option">
                      {{ labelForBidang(option) }}
                    </option>
                  </select>
                  <p v-if="form.errors[`manual_entries.${index}.bidang`]" class="mt-1 text-xs text-rose-600">
                    {{ form.errors[`manual_entries.${index}.bidang`] }}
                  </p>
                </div>
                <div>
                  <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Tanggal</label>
                  <input
                    v-model="entry.activity_date"
                    type="date"
                    class="w-full rounded-md border-gray-300 text-sm shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                  >
                  <p v-if="form.errors[`manual_entries.${index}.activity_date`]" class="mt-1 text-xs text-rose-600">
                    {{ form.errors[`manual_entries.${index}.activity_date`] }}
                  </p>
                </div>
                <div class="md:col-span-2">
                  <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">Uraian Kegiatan</label>
                  <textarea
                    v-model="entry.description"
                    rows="2"
                    class="w-full rounded-md border-gray-300 text-sm shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                  ></textarea>
                  <p v-if="form.errors[`manual_entries.${index}.description`]" class="mt-1 text-xs text-rose-600">
                    {{ form.errors[`manual_entries.${index}.description`] }}
                  </p>
                </div>
              </div>
              <div class="mt-2 flex justify-end">
                <button
                  type="button"
                  class="inline-flex rounded-md border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-50 dark:border-rose-900/50 dark:text-rose-300 dark:hover:bg-rose-900/20"
                  @click="removeEntry(index)"
                >
                  Hapus Baris
                </button>
              </div>
            </div>
          </div>

          <button
            type="button"
            class="mt-3 inline-flex rounded-md border border-emerald-200 px-3 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-50 dark:border-emerald-900/50 dark:text-emerald-300 dark:hover:bg-emerald-900/20"
            @click="addEntry"
          >
            + Tambah Isian Pelengkap
          </button>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Pendahuluan</label>
          <textarea v-model="form.pendahuluan" rows="4" class="w-full rounded-md border-gray-300 text-sm shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"></textarea>
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Keberhasilan</label>
          <textarea v-model="form.keberhasilan" rows="4" class="w-full rounded-md border-gray-300 text-sm shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"></textarea>
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Hambatan</label>
          <textarea v-model="form.hambatan" rows="4" class="w-full rounded-md border-gray-300 text-sm shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"></textarea>
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Kesimpulan</label>
          <textarea v-model="form.kesimpulan" rows="4" class="w-full rounded-md border-gray-300 text-sm shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"></textarea>
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Penutup</label>
          <textarea v-model="form.penutup" rows="4" class="w-full rounded-md border-gray-300 text-sm shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"></textarea>
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
            Simpan
          </button>
        </div>
      </form>
    </CardBox>
  </SectionMain>
</template>

