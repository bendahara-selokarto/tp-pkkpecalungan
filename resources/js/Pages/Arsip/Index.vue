<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { router, useForm, usePage } from '@inertiajs/vue3'
import { mdiArchive } from '@mdi/js'
import { computed, ref } from 'vue'

const props = defineProps({
  documents: {
    type: Array,
    default: () => [],
  },
  can: {
    type: Object,
    default: () => ({ upload: false }),
  },
})

const page = usePage()
const roles = computed(() => page.props.auth?.user?.roles ?? [])
const isKecamatanSekretaris = computed(() => roles.value.includes('kecamatan-sekretaris'))

const selectedCakupan = ref('pribadi')
const form = useForm({
  title: '',
  description: '',
  document_file: null,
})

const onFileChange = (event) => {
  form.document_file = event.target.files?.[0] ?? null
}

const submit = () => {
  form.post('/arsip', {
    preserveScroll: true,
    onSuccess: () => {
      form.reset()
    },
  })
}

const deleteDocument = (id) => {
  router.delete(`/arsip/${id}`, {
    preserveScroll: true,
  })
}

const pindahCakupan = (event) => {
  const value = String(event.target.value || 'pribadi')
  selectedCakupan.value = value

  if (value === 'desa') {
    router.get('/kecamatan/desa-arsip', { per_page: 10 }, {
      preserveScroll: true,
      preserveState: false,
      replace: true,
    })
    return
  }

  router.get('/arsip', {}, {
    preserveScroll: true,
    preserveState: true,
    replace: true,
  })
}

const formatBytes = (sizeInBytes) => {
  const size = Number(sizeInBytes)
  if (!Number.isFinite(size) || size < 1024) {
    return `${Math.max(0, Math.round(size))} B`
  }

  const kiloBytes = size / 1024
  if (kiloBytes < 1024) {
    return `${kiloBytes.toFixed(1)} KB`
  }

  const megaBytes = kiloBytes / 1024
  return `${megaBytes.toFixed(1)} MB`
}

const formatDateTime = (isoDateTime) => {
  if (!isoDateTime) {
    return '-'
  }

  const parsedDate = new Date(isoDateTime)
  if (Number.isNaN(parsedDate.getTime())) {
    return '-'
  }

  return new Intl.DateTimeFormat('id-ID', {
    dateStyle: 'medium',
    timeStyle: 'short',
  }).format(parsedDate)
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiArchive" title="Arsip" main />

    <CardBox>
      <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <div>
          <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Daftar Arsip</h3>
          <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Arsip global dari super-admin dapat dilihat semua role. Arsip pribadi hanya dikelola oleh pengunggah.
          </p>
        </div>

        <div
          v-if="isKecamatanSekretaris"
          class="flex flex-wrap items-center gap-4 text-sm text-gray-700 dark:text-gray-200"
        >
          <span class="font-medium">Cakupan Arsip</span>
          <label class="inline-flex items-center gap-2">
            <input
              v-model="selectedCakupan"
              type="radio"
              name="cakupan-arsip-kecamatan"
              value="pribadi"
              class="h-4 w-4 border-gray-300 text-emerald-600 focus:ring-emerald-500 dark:border-slate-600 dark:bg-slate-900"
              @change="pindahCakupan"
            >
            Arsip Saya
          </label>
          <label class="inline-flex items-center gap-2">
            <input
              v-model="selectedCakupan"
              type="radio"
              name="cakupan-arsip-kecamatan"
              value="desa"
              class="h-4 w-4 border-gray-300 text-emerald-600 focus:ring-emerald-500 dark:border-slate-600 dark:bg-slate-900"
              @change="pindahCakupan"
            >
            Desa (Monitoring)
          </label>
        </div>
      </div>

      <form
        v-if="can.upload"
        class="mb-6 grid gap-3 rounded-md border border-gray-200 bg-gray-50 p-4 dark:border-slate-700 dark:bg-slate-900/40 md:grid-cols-2"
        @submit.prevent="submit"
      >
        <div>
          <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">
            Judul Arsip
          </label>
          <input
            v-model="form.title"
            type="text"
            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
            required
          >
          <p v-if="form.errors.title" class="mt-1 text-xs text-rose-600">{{ form.errors.title }}</p>
        </div>

        <div>
          <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">
            File Dokumen
          </label>
          <input
            type="file"
            accept=".pdf,.doc,.docx,.xls,.xlsx"
            class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
            @change="onFileChange"
          >
          <p v-if="form.errors.document_file" class="mt-1 text-xs text-rose-600">{{ form.errors.document_file }}</p>
        </div>

        <div class="md:col-span-2">
          <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">
            Keterangan (opsional)
          </label>
          <textarea
            v-model="form.description"
            rows="2"
            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
          />
          <p v-if="form.errors.description" class="mt-1 text-xs text-rose-600">{{ form.errors.description }}</p>
        </div>

        <div class="md:col-span-2 flex justify-end">
          <button
            type="submit"
            class="inline-flex rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60"
            :disabled="form.processing"
          >
            Unggah Arsip
          </button>
        </div>
      </form>

      <div class="overflow-x-auto">
        <table class="w-full min-w-[980px] text-sm">
          <thead class="border-b border-gray-200 dark:border-slate-700">
            <tr class="text-left text-gray-600 dark:text-gray-300">
              <th class="px-3 py-3 font-semibold">Judul</th>
              <th class="px-3 py-3 font-semibold">File</th>
              <th class="px-3 py-3 font-semibold">Tipe</th>
              <th class="px-3 py-3 font-semibold">Pengunggah</th>
              <th class="px-3 py-3 font-semibold">Wilayah</th>
              <th class="px-3 py-3 font-semibold">Diperbarui</th>
              <th class="w-48 px-3 py-3 font-semibold">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="document in documents"
              :key="document.id"
              class="border-b border-gray-100 align-top dark:border-slate-800"
            >
              <td class="px-3 py-3 text-gray-900 dark:text-gray-100">
                <p class="font-medium">{{ document.title }}</p>
                <p v-if="document.description" class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                  {{ document.description }}
                </p>
              </td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">
                <p>{{ document.name }}</p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ formatBytes(document.size_bytes) }}</p>
              </td>
              <td class="px-3 py-3">
                <span
                  class="inline-flex rounded border px-2 py-1 text-xs font-semibold"
                  :class="document.is_global
                    ? 'border-emerald-200 text-emerald-700 dark:border-emerald-900/50 dark:text-emerald-300'
                    : 'border-sky-200 text-sky-700 dark:border-sky-900/50 dark:text-sky-300'"
                >
                  {{ document.is_global ? 'Global' : 'Pribadi' }}
                </span>
              </td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ document.owner_name }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ document.area_name || '-' }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ formatDateTime(document.updated_at) }}</td>
              <td class="px-3 py-3">
                <div class="flex items-center gap-2">
                  <a
                    :href="document.download_url"
                    class="inline-flex items-center rounded-md border border-emerald-200 px-3 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-50 dark:border-emerald-900/50 dark:text-emerald-300 dark:hover:bg-emerald-900/20"
                  >
                    Unduh
                  </a>
                  <button
                    v-if="document.can_manage"
                    type="button"
                    class="inline-flex items-center rounded-md border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-50 dark:border-rose-900/50 dark:text-rose-300 dark:hover:bg-rose-900/20"
                    @click="deleteDocument(document.id)"
                  >
                    Hapus
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="documents.length === 0">
              <td colspan="7" class="px-3 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                Belum ada arsip yang bisa ditampilkan.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </CardBox>
  </SectionMain>
</template>
