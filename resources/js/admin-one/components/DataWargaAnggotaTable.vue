<script setup>
import { onMounted } from 'vue'

const props = defineProps({
  form: {
    type: Object,
    required: true,
  },
})

const createRow = () => ({
  nomor_registrasi: '',
  nomor_ktp_kk: '',
  nama: '',
  jabatan: '',
  jenis_kelamin: '',
  tempat_lahir: '',
  tanggal_lahir: '',
  umur_tahun: null,
  status_perkawinan: '',
  status_dalam_keluarga: '',
  agama: '',
  alamat: '',
  desa_kel_sejenis: '',
  pendidikan: '',
  pekerjaan: '',
  akseptor_kb: false,
  aktif_posyandu: false,
  ikut_bkb: false,
  memiliki_tabungan: false,
  ikut_kelompok_belajar: false,
  jenis_kelompok_belajar: '',
  ikut_paud: false,
  ikut_koperasi: false,
  keterangan: '',
})

const ensureArray = () => {
  if (!Array.isArray(props.form.anggota)) {
    props.form.anggota = []
  }
}

const addRow = () => {
  ensureArray()
  props.form.anggota.push(createRow())
  recomputeSummary()
}

const removeRow = (index) => {
  ensureArray()
  props.form.anggota.splice(index, 1)
  recomputeSummary()
}

const recomputeSummary = () => {
  ensureArray()
  const rows = Array.isArray(props.form.anggota) ? props.form.anggota : []

  props.form.jumlah_warga_laki_laki = rows.filter((row) => String(row.jenis_kelamin || '').toUpperCase() === 'L').length
  props.form.jumlah_warga_perempuan = rows.filter((row) => String(row.jenis_kelamin || '').toUpperCase() === 'P').length
}

onMounted(() => {
  ensureArray()
  recomputeSummary()
})
</script>

<template>
  <div class="space-y-3">
    <div class="flex items-center justify-between gap-2">
      <div>
        <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Detail Anggota Rumah Tangga (4.14.1a)</h4>
        <p class="text-xs text-gray-500 dark:text-gray-400">Isi per anggota keluarga sesuai format autentik lampiran 4.14.1a.</p>
      </div>
      <button
        type="button"
        class="inline-flex rounded-md border border-emerald-300 px-3 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-50 dark:border-emerald-900/50 dark:text-emerald-300 dark:hover:bg-emerald-900/20"
        @click="addRow"
      >
        + Tambah Anggota
      </button>
    </div>

    <p v-if="form.errors.anggota" class="text-xs text-rose-600">{{ form.errors.anggota }}</p>

    <div class="overflow-x-auto rounded-md border border-gray-200 dark:border-slate-700">
      <table class="w-full min-w-[1900px] text-xs">
        <thead class="bg-gray-50 dark:bg-slate-900/60">
          <tr class="text-left text-gray-700 dark:text-gray-200">
            <th class="px-2 py-2">No.</th>
            <th class="px-2 py-2 min-w-[8rem]">No. Registrasi</th>
            <th class="px-2 py-2 min-w-[8rem]">No. KTP/KK</th>
            <th class="px-2 py-2 min-w-[10rem]">Nama</th>
            <th class="px-2 py-2 min-w-[8rem]">Jabatan</th>
            <th class="px-2 py-2 min-w-[6rem]">JK</th>
            <th class="px-2 py-2 min-w-[8rem]">Tempat Lahir</th>
            <th class="px-2 py-2 min-w-[8rem]">Tgl Lahir</th>
            <th class="px-2 py-2 min-w-[6rem]">Umur</th>
            <th class="px-2 py-2 min-w-[8rem]">Status Kawin</th>
            <th class="px-2 py-2 min-w-[9rem]">Status Keluarga</th>
            <th class="px-2 py-2 min-w-[7rem]">Agama</th>
            <th class="px-2 py-2 min-w-[10rem]">Alamat</th>
            <th class="px-2 py-2 min-w-[8rem]">Desa/Kel</th>
            <th class="px-2 py-2 min-w-[8rem]">Pendidikan</th>
            <th class="px-2 py-2 min-w-[8rem]">Pekerjaan</th>
            <th class="px-2 py-2 text-center">KB</th>
            <th class="px-2 py-2 text-center">Posyandu</th>
            <th class="px-2 py-2 text-center">BKB</th>
            <th class="px-2 py-2 text-center">Tabungan</th>
            <th class="px-2 py-2 text-center">Kel. Belajar</th>
            <th class="px-2 py-2 min-w-[8rem]">Jenis Kel. Belajar</th>
            <th class="px-2 py-2 text-center">PAUD</th>
            <th class="px-2 py-2 text-center">Koperasi</th>
            <th class="px-2 py-2 min-w-[8rem]">Keterangan</th>
            <th class="px-2 py-2">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(anggota, index) in form.anggota"
            :key="`anggota-${index}`"
            class="border-t border-gray-200 align-top dark:border-slate-700"
          >
            <td class="px-2 py-2">{{ index + 1 }}</td>
            <td class="px-2 py-2"><input v-model="anggota.nomor_registrasi" type="text" class="w-full min-w-[8rem] rounded border-gray-300 px-2 py-1 shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"></td>
            <td class="px-2 py-2"><input v-model="anggota.nomor_ktp_kk" type="text" class="w-full min-w-[8rem] rounded border-gray-300 px-2 py-1 shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"></td>
            <td class="px-2 py-2"><input v-model="anggota.nama" type="text" class="w-full min-w-[10rem] rounded border-gray-300 px-2 py-1 shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required></td>
            <td class="px-2 py-2"><input v-model="anggota.jabatan" type="text" class="w-full min-w-[8rem] rounded border-gray-300 px-2 py-1 shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"></td>
            <td class="px-2 py-2">
              <select v-model="anggota.jenis_kelamin" class="w-full min-w-[6rem] rounded border-gray-300 px-2 py-1 shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" @change="recomputeSummary">
                <option value="">-</option>
                <option value="L">L</option>
                <option value="P">P</option>
              </select>
            </td>
            <td class="px-2 py-2"><input v-model="anggota.tempat_lahir" type="text" class="w-full min-w-[8rem] rounded border-gray-300 px-2 py-1 shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"></td>
            <td class="px-2 py-2"><input v-model="anggota.tanggal_lahir" type="date" class="w-full min-w-[8rem] rounded border-gray-300 px-2 py-1 shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"></td>
            <td class="px-2 py-2"><input v-model.number="anggota.umur_tahun" type="number" min="0" class="w-full min-w-[6rem] rounded border-gray-300 px-2 py-1 shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"></td>
            <td class="px-2 py-2"><input v-model="anggota.status_perkawinan" type="text" class="w-full min-w-[8rem] rounded border-gray-300 px-2 py-1 shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"></td>
            <td class="px-2 py-2"><input v-model="anggota.status_dalam_keluarga" type="text" class="w-full min-w-[9rem] rounded border-gray-300 px-2 py-1 shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"></td>
            <td class="px-2 py-2"><input v-model="anggota.agama" type="text" class="w-full min-w-[7rem] rounded border-gray-300 px-2 py-1 shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"></td>
            <td class="px-2 py-2"><input v-model="anggota.alamat" type="text" class="w-full min-w-[10rem] rounded border-gray-300 px-2 py-1 shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"></td>
            <td class="px-2 py-2"><input v-model="anggota.desa_kel_sejenis" type="text" class="w-full min-w-[8rem] rounded border-gray-300 px-2 py-1 shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"></td>
            <td class="px-2 py-2"><input v-model="anggota.pendidikan" type="text" class="w-full min-w-[8rem] rounded border-gray-300 px-2 py-1 shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"></td>
            <td class="px-2 py-2"><input v-model="anggota.pekerjaan" type="text" class="w-full min-w-[8rem] rounded border-gray-300 px-2 py-1 shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"></td>
            <td class="px-2 py-2 text-center"><input v-model="anggota.akseptor_kb" type="checkbox" class="rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500"></td>
            <td class="px-2 py-2 text-center"><input v-model="anggota.aktif_posyandu" type="checkbox" class="rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500"></td>
            <td class="px-2 py-2 text-center"><input v-model="anggota.ikut_bkb" type="checkbox" class="rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500"></td>
            <td class="px-2 py-2 text-center"><input v-model="anggota.memiliki_tabungan" type="checkbox" class="rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500"></td>
            <td class="px-2 py-2 text-center"><input v-model="anggota.ikut_kelompok_belajar" type="checkbox" class="rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500"></td>
            <td class="px-2 py-2"><input v-model="anggota.jenis_kelompok_belajar" type="text" class="w-full min-w-[8rem] rounded border-gray-300 px-2 py-1 shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"></td>
            <td class="px-2 py-2 text-center"><input v-model="anggota.ikut_paud" type="checkbox" class="rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500"></td>
            <td class="px-2 py-2 text-center"><input v-model="anggota.ikut_koperasi" type="checkbox" class="rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500"></td>
            <td class="px-2 py-2"><input v-model="anggota.keterangan" type="text" class="w-full min-w-[8rem] rounded border-gray-300 px-2 py-1 shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"></td>
            <td class="px-2 py-2">
              <button type="button" class="rounded border border-rose-200 px-2 py-1 text-[11px] font-semibold text-rose-700 hover:bg-rose-50 dark:border-rose-900/50 dark:text-rose-300 dark:hover:bg-rose-900/20" @click="removeRow(index)">
                Hapus
              </button>
            </td>
          </tr>
          <tr v-if="form.anggota.length === 0">
            <td colspan="26" class="px-3 py-4 text-center text-xs text-gray-500 dark:text-gray-400">
              Belum ada detail anggota. Tambahkan minimal satu anggota untuk format autentik 4.14.1a.
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
