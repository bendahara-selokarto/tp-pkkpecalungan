<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { formatDateForDisplay } from '@/utils/dateFormatter'
import { Link, useForm } from '@inertiajs/vue3'
import { mdiAccountMultipleCheckOutline } from '@mdi/js'

const props = defineProps({
  activityOptions: {
    type: Array,
    default: () => [],
  },
})

const form = useForm({
  attendance_date: '',
  activity_id: '',
  attendee_name: '',
  institution: '',
  description: '',
})

const formatDate = (value) => formatDateForDisplay(value)

const submit = () => {
  form.post('/kecamatan/buku-daftar-hadir')
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiAccountMultipleCheckOutline" title="Tambah Buku Daftar Hadir Kecamatan" main />

    <CardBox class="max-w-3xl">
      <form class="space-y-5" @submit.prevent="submit">
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Kehadiran</label>
          <input
            v-model="form.attendance_date"
            type="date"
            class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
            required
          >
          <p v-if="form.errors.attendance_date" class="mt-1 text-xs text-rose-600">{{ form.errors.attendance_date }}</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Kegiatan</label>
          <select
            v-model="form.activity_id"
            class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
            required
          >
            <option value="" disabled>Pilih kegiatan</option>
            <option
              v-for="activity in activityOptions"
              :key="activity.id"
              :value="String(activity.id)"
            >
              {{ formatDate(activity.activity_date) }} - {{ activity.title }}
            </option>
          </select>
          <p v-if="activityOptions.length === 0" class="mt-1 text-xs text-amber-600">
            Belum ada data kegiatan pada wilayah Anda. Tambahkan data di Buku Kegiatan terlebih dahulu.
          </p>
          <p v-if="form.errors.activity_id" class="mt-1 text-xs text-rose-600">{{ form.errors.activity_id }}</p>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Peserta</label>
            <input
              v-model="form.attendee_name"
              type="text"
              class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
              required
            >
            <p v-if="form.errors.attendee_name" class="mt-1 text-xs text-rose-600">{{ form.errors.attendee_name }}</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Instansi/Unit</label>
            <input
              v-model="form.institution"
              type="text"
              class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
            >
            <p v-if="form.errors.institution" class="mt-1 text-xs text-rose-600">{{ form.errors.institution }}</p>
          </div>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Keterangan</label>
          <textarea
            v-model="form.description"
            rows="4"
            class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
          />
          <p v-if="form.errors.description" class="mt-1 text-xs text-rose-600">{{ form.errors.description }}</p>
        </div>

        <div class="flex items-center justify-end gap-2">
          <Link
            href="/kecamatan/buku-daftar-hadir"
            class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
          >
            Batal
          </Link>
          <button
            type="submit"
            class="inline-flex rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60"
            :disabled="form.processing || activityOptions.length === 0"
          >
            Simpan
          </button>
        </div>
      </form>
    </CardBox>
  </SectionMain>
</template>
