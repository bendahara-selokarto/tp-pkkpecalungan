<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link } from '@inertiajs/vue3'
import { mdiStore } from '@mdi/js'

const props = defineProps({
  tamanBacaan: {
    type: Object,
    required: true,
  },
})
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiStore" title="Detail Taman Bacaan Desa" main />

    <CardBox class="max-w-4xl space-y-4">
      <div class="grid gap-4 md:grid-cols-3">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Nama Taman Bacaan</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.tamanBacaan.nama_taman_bacaan }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Nama Pengelola</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.tamanBacaan.nama_pengelola }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Jumlah Buku Bacaan</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.tamanBacaan.jumlah_buku_bacaan }}</p>
        </div>
      </div>

      <div class="grid gap-4 md:grid-cols-3">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Jenis Buku</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.tamanBacaan.jenis_buku }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Kategori</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.tamanBacaan.kategori }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Jumlah</p>
          <p class="text-sm text-gray-700 dark:text-gray-300">{{ props.tamanBacaan.jumlah }}</p>
        </div>
      </div>

      <div class="flex items-center justify-end gap-2">
        <Link href="/desa/taman-bacaan" class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
          Kembali
        </Link>
        <Link :href="`/desa/taman-bacaan/${props.tamanBacaan.id}/edit`" class="inline-flex rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
          Edit
        </Link>
      </div>
    </CardBox>
  </SectionMain>
</template>


