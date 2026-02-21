<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { mdiAccountGroup } from '@mdi/js'
import { defineAsyncComponent } from 'vue'

const DataWargaAnggotaLoading = {
  template: '<div class="rounded-md border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-700">Memuat tabel anggota...</div>',
}

const DataWargaAnggotaError = {
  template: '<div class="rounded-md border border-rose-200 bg-rose-50 px-3 py-2 text-xs text-rose-700">Komponen tabel anggota gagal dimuat. Muat ulang halaman.</div>',
}

const DataWargaAnggotaTable = defineAsyncComponent({
  loader: () => import('@/admin-one/components/DataWargaAnggotaTable.vue'),
  loadingComponent: DataWargaAnggotaLoading,
  errorComponent: DataWargaAnggotaError,
  delay: 0,
  timeout: 15000,
})

const props = defineProps({
  dataWarga: {
    type: Object,
    required: true,
  },
})

const form = useForm({
  dasawisma: props.dataWarga.dasawisma,
  nama_kepala_keluarga: props.dataWarga.nama_kepala_keluarga,
  alamat: props.dataWarga.alamat,
  jumlah_warga_laki_laki: props.dataWarga.jumlah_warga_laki_laki,
  jumlah_warga_perempuan: props.dataWarga.jumlah_warga_perempuan,
  keterangan: props.dataWarga.keterangan ?? '',
  anggota: props.dataWarga.anggota ?? [],
})

const submit = () => {
  form.put(`/desa/data-warga/${props.dataWarga.id}`)
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiAccountGroup" title="Edit Data Warga Desa" main />

    <CardBox class="max-w-4xl">
      <form class="space-y-5" @submit.prevent="submit">
        <div class="grid gap-5 md:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Dasawisma</label>
            <input v-model="form.dasawisma" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.dasawisma" class="mt-1 text-xs text-rose-600">{{ form.errors.dasawisma }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Kepala Keluarga</label>
            <input v-model="form.nama_kepala_keluarga" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.nama_kepala_keluarga" class="mt-1 text-xs text-rose-600">{{ form.errors.nama_kepala_keluarga }}</p>
          </div>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Alamat</label>
          <input v-model="form.alamat" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
          <p v-if="form.errors.alamat" class="mt-1 text-xs text-rose-600">{{ form.errors.alamat }}</p>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah Warga Laki-laki (otomatis)</label>
            <input v-model="form.jumlah_warga_laki_laki" type="number" min="0" readonly class="w-full rounded-md border-gray-300 bg-gray-50 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100" required>
            <p v-if="form.errors.jumlah_warga_laki_laki" class="mt-1 text-xs text-rose-600">{{ form.errors.jumlah_warga_laki_laki }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah Warga Perempuan (otomatis)</label>
            <input v-model="form.jumlah_warga_perempuan" type="number" min="0" readonly class="w-full rounded-md border-gray-300 bg-gray-50 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100" required>
            <p v-if="form.errors.jumlah_warga_perempuan" class="mt-1 text-xs text-rose-600">{{ form.errors.jumlah_warga_perempuan }}</p>
          </div>
        </div>

        <DataWargaAnggotaTable :form="form" />

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Keterangan</label>
          <textarea v-model="form.keterangan" rows="3" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" />
          <p v-if="form.errors.keterangan" class="mt-1 text-xs text-rose-600">{{ form.errors.keterangan }}</p>
        </div>

        <div class="flex items-center justify-end gap-2">
          <Link href="/desa/data-warga" class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
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
