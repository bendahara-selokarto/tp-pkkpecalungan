<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { mdiClipboardList } from '@mdi/js'

const props = defineProps({
  jobGroup: {
    type: String,
    required: true,
  },
})

const form = useForm({
  title: '',
  nama_petugas: '',
  jabatan_petugas: '',
  tempat_kegiatan: '',
  uraian: '',
  tanda_tangan: '',
  activity_date: '',
  image_upload: null,
  document_upload: null,
  additional_info: {
    // Shared / Pokja I
    program_category: '',
    volume: 1,
    sasaran: '',
    sasaran_jumlah: 0,
    metode: '',
    // Pokja II
    jenis_literasi: '',
    jenis_kejar_paket: '',
    jenis_koperasi: '',
    // Pokja III
    kategori_pangan: '',
    kategori_sandang: '',
    kategori_perumahan: '',
    // Pokja IV
    jenis_layanan_kesehatan: '',
    nama_posyandu: '',
    perencanaan_sehat: '',
  },
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
    }))
    .post('/desa/activities', {
      forceFormData: true,
    })
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiClipboardList" title="Tambah Buku Kegiatan Desa" main />

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

        <div class="grid gap-5 md:grid-cols-2">
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
        </div>

        <div v-if="['pokja-i', 'pokja-ii', 'pokja-iii', 'pokja-iv'].includes(jobGroup)" class="rounded-md border border-gray-200 bg-gray-50/70 p-4 dark:border-slate-700 dark:bg-slate-900/40">
          <h3 class="mb-3 text-sm font-bold text-gray-800 dark:text-gray-100">Capaian Kegiatan</h3>
          <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-4">
            <div>
              <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Volume</label>
              <input v-model="form.additional_info.volume" type="number" min="1" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
            </div>
            <div>
              <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Sasaran (Narasi)</label>
              <input v-model="form.additional_info.sasaran" type="text" placeholder="Contoh: 25 warga" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
            </div>
            <div>
              <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Sasaran (Angka)</label>
              <input v-model="form.additional_info.sasaran_jumlah" type="number" min="0" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
            </div>
            <div>
              <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Metode</label>
              <input v-model="form.additional_info.metode" type="text" placeholder="Contoh: Penyuluhan" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
            </div>
          </div>
        </div>

        <!-- Pokja I Section -->
        <div v-if="jobGroup === 'pokja-i'" class="rounded-md border border-emerald-100 bg-emerald-50/30 p-4 dark:border-emerald-900/20 dark:bg-emerald-900/10">
          <h3 class="mb-3 text-sm font-bold text-emerald-800 dark:text-emerald-400">Informasi Khusus Pokja I (Hukum & Karakter)</h3>
          <div class="grid gap-5 md:grid-cols-2">
            <div>
              <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Kategori Program</label>
              <select v-model="form.additional_info.program_category" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                <option value="">-- Pilih Program --</option>
                <option value="pkbn">PKBN (Penghayatan & Pengamalan Pancasila)</option>
                <option value="kisah">KISAH (Keluarga Indonesia Sejahtera Anti Narkoba)</option>
                <option value="krisan">KRISAN (Keluarga Indonesia Sadar Administrasi Kependudukan)</option>
                <option value="kilas">KILAS (Keluarga Indonesia Lindungi Anak dari Kekerasan Seksual)</option>
                <option value="ktiat">KTIAT (Keluarga Indonesia Tangguh Ekonomi dengan Administrasi Teratur)</option>
                <option value="kisak">KISAK (Keluarga Indonesia Sadar Adab dan Karakter)</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Pokja II Section -->
        <div v-if="jobGroup === 'pokja-ii'" class="rounded-md border border-blue-100 bg-blue-50/30 p-4 dark:border-blue-900/20 dark:bg-blue-900/10">
          <h3 class="mb-3 text-sm font-bold text-blue-800 dark:text-blue-400">Informasi Khusus Pokja II (Pendidikan & Ekonomi)</h3>
          <div class="grid gap-5 md:grid-cols-2">
            <div>
              <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Layanan Literasi/Pendidikan</label>
              <select v-model="form.additional_info.jenis_literasi" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                <option value="">-- Pilih Layanan --</option>
                <option value="Taman Bacaan">Taman Bacaan</option>
                <option value="BKB">Bina Keluarga Balita (BKB)</option>
                <option value="PAUD">PAUD</option>
              </select>
            </div>
            <div>
              <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Koperasi/Ekonomi</label>
              <input v-model="form.additional_info.jenis_koperasi" type="text" placeholder="Misal: UP2K, Koperasi Berbadan Hukum" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
            </div>
          </div>
        </div>

        <!-- Pokja III Section -->
        <div v-if="jobGroup === 'pokja-iii'" class="rounded-md border border-orange-100 bg-orange-50/30 p-4 dark:border-orange-900/20 dark:bg-orange-900/10">
          <h3 class="mb-3 text-sm font-bold text-orange-800 dark:text-orange-400">Informasi Khusus Pokja III (Pangan, Sandang, Perumahan)</h3>
          <div class="grid gap-5 md:grid-cols-2">
            <div>
              <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Kategori Pangan/Sandang</label>
              <input v-model="form.additional_info.kategori_pangan" type="text" placeholder="Misal: Hatinya PKK, Sandang" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
            </div>
            <div>
              <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Kategori Perumahan</label>
              <input v-model="form.additional_info.kategori_perumahan" type="text" placeholder="Misal: Rumah Sehat, Tata Laksana" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
            </div>
          </div>
        </div>

        <!-- Pokja IV Section -->
        <div v-if="jobGroup === 'pokja-iv'" class="rounded-md border border-rose-100 bg-rose-50/30 p-4 dark:border-rose-900/20 dark:bg-rose-900/10">
          <h3 class="mb-3 text-sm font-bold text-rose-800 dark:text-rose-400">Informasi Khusus Pokja IV (Kesehatan & Lingkungan)</h3>
          <div class="grid gap-5 md:grid-cols-2">
            <div>
              <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Layanan Kesehatan</label>
              <select v-model="form.additional_info.jenis_layanan_kesehatan" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-rose-500 focus:ring-rose-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                <option value="">-- Pilih Layanan --</option>
                <option value="Posyandu">Posyandu</option>
                <option value="KB">Keluarga Berencana (KB)</option>
                <option value="Imunisasi">Imunisasi</option>
              </select>
            </div>
            <div>
              <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Lingkungan Hidup</label>
              <input v-model="form.additional_info.perencanaan_sehat" type="text" placeholder="Misal: Jamban, PHBS" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-rose-500 focus:ring-rose-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
            </div>
          </div>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Uraian</label>
          <textarea v-model="form.uraian" rows="4" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" />
          <p v-if="form.errors.uraian" class="mt-1 text-xs text-rose-600">{{ form.errors.uraian }}</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Tanda Tangan</label>
          <input v-model="form.tanda_tangan" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
          <p v-if="form.errors.tanda_tangan" class="mt-1 text-xs text-rose-600">{{ form.errors.tanda_tangan }}</p>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Upload Gambar</label>
            <input
              type="file"
              accept="image/*"
              class="block w-full text-sm text-gray-700 file:mr-4 file:rounded-md file:border-0 file:bg-emerald-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-emerald-700 hover:file:bg-emerald-100 dark:text-slate-200 dark:file:bg-emerald-900/30 dark:file:text-emerald-300"
              @change="setFile('image_upload', $event)"
            >
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Maksimum 5MB. Format gambar umum (jpg, png, webp).</p>
            <p v-if="form.errors.image_upload" class="mt-1 text-xs text-rose-600">{{ form.errors.image_upload }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Upload Berkas</label>
            <input
              type="file"
              accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,image/*"
              class="block w-full text-sm text-gray-700 file:mr-4 file:rounded-md file:border-0 file:bg-sky-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-sky-700 hover:file:bg-sky-100 dark:text-slate-200 dark:file:bg-sky-900/30 dark:file:text-sky-300"
              @change="setFile('document_upload', $event)"
            >
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Maksimum 10MB. Format: PDF, Office, atau gambar.</p>
            <p v-if="form.errors.document_upload" class="mt-1 text-xs text-rose-600">{{ form.errors.document_upload }}</p>
          </div>
        </div>

        <div class="flex items-center justify-end gap-2">
          <Link href="/desa/activities" class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
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
