<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { mdiAccountGroup } from '@mdi/js'

const props = defineProps({
  koperasi: {
    type: Object,
    required: true,
  },
})

const form = useForm({
  nama_koperasi: props.koperasi.nama_koperasi,
  jenis_usaha: props.koperasi.jenis_usaha,
  status_hukum: props.koperasi.berbadan_hukum ? 'berbadan_hukum' : 'belum_berbadan_hukum',
  jumlah_anggota_l: props.koperasi.jumlah_anggota_l,
  jumlah_anggota_p: props.koperasi.jumlah_anggota_p,
})

const payloadTransformer = (data) => {
  const { status_hukum, ...rest } = data

  return {
    ...rest,
    berbadan_hukum: status_hukum === 'berbadan_hukum',
    belum_berbadan_hukum: status_hukum === 'belum_berbadan_hukum',
  }
}

const submit = () => {
  form.transform(payloadTransformer).put(`/desa/koperasi/${props.koperasi.id}`)
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiAccountGroup" title="Edit Koperasi Desa" main />

    <CardBox class="max-w-4xl">
      <form class="space-y-5" @submit.prevent="submit">
        <div class="grid gap-5 md:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Koperasi</label>
            <input v-model="form.nama_koperasi" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.nama_koperasi" class="mt-1 text-xs text-rose-600">{{ form.errors.nama_koperasi }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Jenis Usaha</label>
            <input v-model="form.jenis_usaha" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.jenis_usaha" class="mt-1 text-xs text-rose-600">{{ form.errors.jenis_usaha }}</p>
          </div>
        </div>

        <div>
          <p class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Status Hukum</p>
          <div class="flex flex-wrap items-center gap-4">
            <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
              <input v-model="form.status_hukum" type="radio" value="berbadan_hukum" class="border-gray-300 text-emerald-600 focus:ring-emerald-500">
              Berbadan Hukum
            </label>
            <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
              <input v-model="form.status_hukum" type="radio" value="belum_berbadan_hukum" class="border-gray-300 text-emerald-600 focus:ring-emerald-500">
              Belum Berbadan Hukum
            </label>
          </div>
          <p v-if="form.errors.berbadan_hukum" class="mt-1 text-xs text-rose-600">{{ form.errors.berbadan_hukum }}</p>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah Anggota L</label>
            <input v-model="form.jumlah_anggota_l" type="number" min="0" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.jumlah_anggota_l" class="mt-1 text-xs text-rose-600">{{ form.errors.jumlah_anggota_l }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah Anggota P</label>
            <input v-model="form.jumlah_anggota_p" type="number" min="0" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.jumlah_anggota_p" class="mt-1 text-xs text-rose-600">{{ form.errors.jumlah_anggota_p }}</p>
          </div>
        </div>

        <div class="flex items-center justify-end gap-2">
          <Link href="/desa/koperasi" class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
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
