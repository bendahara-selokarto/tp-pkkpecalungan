<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import BaseButton from '@/admin-one/components/BaseButton.vue'
import BaseButtons from '@/admin-one/components/BaseButtons.vue'
import BaseIcon from '@/admin-one/components/BaseIcon.vue'
import { mdiNotebookEditOutline, mdiFileDownloadOutline } from '@mdi/js'
import { Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
})

const page = usePage()
const moduleMode = computed(() => page.props.auth?.user?.moduleModes?.['buku-agenda-sk'])
const canUpdate = computed(() => moduleMode.value === 'read-write')

const formatDateForDisplay = (dateString) => {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'long',
    year: 'numeric',
  })
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiNotebookEditOutline" title="Detail Agenda SK" main />

    <CardBox class="max-w-3xl space-y-6">
      <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Nomor SK</p>
          <p class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ props.item.nomor_sk }}</p>
        </div>

        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Tanggal SK</p>
          <p class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ formatDateForDisplay(props.item.tanggal_sk) }}</p>
        </div>
      </div>

      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Kepada</p>
        <p class="text-base text-gray-900 dark:text-gray-100">{{ props.item.kepada }}</p>
      </div>

      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Perihal / Tentang</p>
        <p class="mt-1 text-sm text-gray-700 whitespace-pre-wrap dark:text-gray-300">{{ props.item.perihal }}</p>
      </div>

      <div>
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Tembusan</p>
        <p class="mt-1 text-sm text-gray-700 whitespace-pre-wrap dark:text-gray-300">{{ props.item.tembusan || '-' }}</p>
      </div>

      <div v-if="props.item.file_url">
        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Berkas SK</p>
        <a
          :href="props.item.file_url"
          target="_blank"
          class="inline-flex mt-1 items-center gap-1 text-emerald-600 hover:underline dark:text-emerald-400 font-medium"
        >
          <BaseIcon :path="mdiFileDownloadOutline" size="20" />
          Unduh Berkas
        </a>
      </div>

      <div class="pt-6 flex items-center justify-between border-t border-gray-100 dark:border-slate-800">
        <Link
          :href="route('kecamatan.buku-agenda-sk.index')"
          class="text-sm font-medium text-gray-600 hover:text-gray-900 dark:text-slate-400 dark:hover:text-slate-200"
        >
          &larr; Kembali ke Daftar
        </Link>

        <Link
          v-if="canUpdate"
          :href="route('kecamatan.buku-agenda-sk.edit', props.item.id)"
          class="inline-flex rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700"
        >
          Edit
        </Link>
      </div>
    </CardBox>
  </SectionMain>
</template>
