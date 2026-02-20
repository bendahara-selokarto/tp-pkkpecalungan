<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { mdiAccountPlus } from '@mdi/js'

const props = defineProps({
  anggotaPokja: {
    type: Object,
    required: true,
  },
})

const form = useForm({
  nama: props.anggotaPokja.nama ?? '',
  jabatan: props.anggotaPokja.jabatan ?? '',
  jenis_kelamin: props.anggotaPokja.jenis_kelamin ?? 'L',
  tempat_lahir: props.anggotaPokja.tempat_lahir ?? '',
  tanggal_lahir: props.anggotaPokja.tanggal_lahir ?? '',
  status_perkawinan: props.anggotaPokja.status_perkawinan ?? 'kawin',
  alamat: props.anggotaPokja.alamat ?? '',
  pendidikan: props.anggotaPokja.pendidikan ?? '',
  pekerjaan: props.anggotaPokja.pekerjaan ?? '',
  keterangan: props.anggotaPokja.keterangan ?? '',
  pokja: props.anggotaPokja.pokja ?? '',
})

const submit = () => {
  form.put(`/desa/anggota-pokja/${props.anggotaPokja.id}`)
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiAccountPlus" title="Edit Anggota Pokja Desa" main />

    <CardBox class="max-w-4xl">
      <form class="space-y-5" @submit.prevent="submit">
        <div class="grid gap-5 md:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama</label>
            <input v-model="form.nama" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.nama" class="mt-1 text-xs text-rose-600">{{ form.errors.nama }}</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Jabatan</label>
            <input v-model="form.jabatan" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.jabatan" class="mt-1 text-xs text-rose-600">{{ form.errors.jabatan }}</p>
          </div>
        </div>

        <div class="grid gap-5 md:grid-cols-3">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Jenis Kelamin</label>
            <select v-model="form.jenis_kelamin" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
              <option value="L">Laki-laki</option>
              <option value="P">Perempuan</option>
            </select>
            <p v-if="form.errors.jenis_kelamin" class="mt-1 text-xs text-rose-600">{{ form.errors.jenis_kelamin }}</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Tempat Lahir</label>
            <input v-model="form.tempat_lahir" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.tempat_lahir" class="mt-1 text-xs text-rose-600">{{ form.errors.tempat_lahir }}</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Lahir</label>
            <input v-model="form.tanggal_lahir" type="text" inputmode="numeric" placeholder="DD/MM/YYYY" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.tanggal_lahir" class="mt-1 text-xs text-rose-600">{{ form.errors.tanggal_lahir }}</p>
          </div>
        </div>

        <div class="grid gap-5 md:grid-cols-3">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Status Perkawinan</label>
            <select v-model="form.status_perkawinan" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
              <option value="kawin">Kawin</option>
              <option value="tidak_kawin">Tidak Kawin</option>
            </select>
            <p v-if="form.errors.status_perkawinan" class="mt-1 text-xs text-rose-600">{{ form.errors.status_perkawinan }}</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Pendidikan</label>
            <input v-model="form.pendidikan" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.pendidikan" class="mt-1 text-xs text-rose-600">{{ form.errors.pendidikan }}</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Pekerjaan</label>
            <input v-model="form.pekerjaan" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.pekerjaan" class="mt-1 text-xs text-rose-600">{{ form.errors.pekerjaan }}</p>
          </div>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Pokja</label>
            <input v-model="form.pokja" type="text" placeholder="Contoh: Pokja I" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required>
            <p v-if="form.errors.pokja" class="mt-1 text-xs text-rose-600">{{ form.errors.pokja }}</p>
          </div>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Alamat</label>
          <textarea v-model="form.alamat" rows="3" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required></textarea>
          <p v-if="form.errors.alamat" class="mt-1 text-xs text-rose-600">{{ form.errors.alamat }}</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Keterangan</label>
          <textarea v-model="form.keterangan" rows="3" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"></textarea>
          <p v-if="form.errors.keterangan" class="mt-1 text-xs text-rose-600">{{ form.errors.keterangan }}</p>
        </div>

        <div class="flex items-center justify-end gap-2">
          <Link href="/desa/anggota-pokja" class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
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

