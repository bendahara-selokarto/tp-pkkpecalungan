<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import { mdiPackageVariantClosed } from '@mdi/js'
import { computed } from 'vue'

defineProps({
  inventaris: {
    type: Array,
    required: true,
  },
})

const page = usePage()
const flashSuccess = computed(() => page.props.flash?.success)

const hapusInventaris = (id) => {
  if (!window.confirm('Apakah Anda yakin ingin menghapus inventaris ini?')) {
    return
  }

  router.delete(`/kecamatan/inventaris/${id}`)
}

const formatCondition = (value) => value.replace('_', ' ')
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiPackageVariantClosed" title="Inventaris Kecamatan" main />

    <div
      v-if="flashSuccess"
      class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900/40 dark:bg-emerald-900/20 dark:text-emerald-300"
    >
      {{ flashSuccess }}
    </div>

    <CardBox>
      <div class="mb-4 flex items-center justify-between gap-4">
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Daftar Inventaris</h3>
        <Link
          href="/kecamatan/inventaris/create"
          class="inline-flex items-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700"
        >
          + Tambah Inventaris
        </Link>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full min-w-[760px] text-sm">
          <thead class="border-b border-gray-200 dark:border-slate-700">
            <tr class="text-left text-gray-600 dark:text-gray-300">
              <th class="px-3 py-3 font-semibold">Nama</th>
              <th class="px-3 py-3 font-semibold">Jumlah</th>
              <th class="px-3 py-3 font-semibold">Kondisi</th>
              <th class="px-3 py-3 font-semibold w-44">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="item in inventaris"
              :key="item.id"
              class="border-b border-gray-100 align-top dark:border-slate-800"
            >
              <td class="px-3 py-3 text-gray-900 dark:text-gray-100">{{ item.name }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ item.quantity }} {{ item.unit }}</td>
              <td class="px-3 py-3 capitalize text-gray-700 dark:text-gray-300">{{ formatCondition(item.condition) }}</td>
              <td class="px-3 py-3">
                <div class="flex items-center gap-2">
                  <Link
                    :href="`/kecamatan/inventaris/${item.id}`"
                    class="inline-flex rounded-md border border-sky-200 px-3 py-1.5 text-xs font-semibold text-sky-700 hover:bg-sky-50 dark:border-sky-900/50 dark:text-sky-300 dark:hover:bg-sky-900/20"
                  >
                    Lihat
                  </Link>
                  <Link
                    :href="`/kecamatan/inventaris/${item.id}/edit`"
                    class="inline-flex rounded-md border border-amber-200 px-3 py-1.5 text-xs font-semibold text-amber-700 hover:bg-amber-50 dark:border-amber-900/50 dark:text-amber-300 dark:hover:bg-amber-900/20"
                  >
                    Edit
                  </Link>
                  <button
                    type="button"
                    class="inline-flex rounded-md border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-50 dark:border-rose-900/50 dark:text-rose-300 dark:hover:bg-rose-900/20"
                    @click="hapusInventaris(item.id)"
                  >
                    Hapus
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="inventaris.length === 0">
              <td colspan="4" class="px-3 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                Data inventaris belum tersedia.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </CardBox>
  </SectionMain>
</template>
