<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import ConfirmActionModal from '@/admin-one/components/ConfirmActionModal.vue'
import PaginationBar from '@/admin-one/components/PaginationBar.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { Link, router } from '@inertiajs/vue3'
import { mdiAccountGroup } from '@mdi/js'
import { computed, ref } from 'vue'

const props = defineProps({
  praKoperasiItems: {
    type: Object,
    required: true,
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
  pagination: {
    type: Object,
    default: () => ({
      perPageOptions: [10, 25, 50],
    }),
  },
})

const perPage = computed(() => props.filters.per_page ?? 10)

const updatePerPage = (event) => {
  const selectedPerPage = Number(event.target.value)

  router.get('/kecamatan/pra-koperasi-up2k', {
    ...props.filters,
    page: 1,
    per_page: selectedPerPage,
  }, {
    preserveScroll: true,
    preserveState: true,
    replace: true,
  })
}

const deleteConfirmationMessage = 'Apakah Anda yakin ingin menghapus data pra koperasi/UP2K ini?'
const isDeleteModalActive = ref(false)
const deletingId = ref(null)

const hapusPraKoperasi = (id) => {
  deletingId.value = id
  isDeleteModalActive.value = true
}

const confirmDelete = () => {
  if (deletingId.value === null) {
    return
  }

  router.delete(`/kecamatan/pra-koperasi-up2k/${deletingId.value}`, {
    onFinish: () => {
      isDeleteModalActive.value = false
      deletingId.value = null
    },
  })
}

const cancelDelete = () => {
  isDeleteModalActive.value = false
  deletingId.value = null
}

const tingkatLabel = (value) => {
  if (value === 'pemula') {
    return 'Pemula'
  }

  if (value === 'madya') {
    return 'Madya'
  }

  if (value === 'utama') {
    return 'Utama'
  }

  if (value === 'mandiri') {
    return 'Mandiri'
  }

  return value || '-'
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiAccountGroup" title="Pra Koperasi/UP2K Kecamatan" main />

    <CardBox>
      <div class="mb-4 flex items-center justify-between gap-4">
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Daftar Pra Koperasi/UP2K</h3>
        <Link
          href="/kecamatan/pra-koperasi-up2k/create"
          class="inline-flex items-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700"
        >
          + Tambah
        </Link>
      </div>

      <div class="mb-3 flex justify-end">
        <label class="text-xs text-gray-600 dark:text-gray-300">
          Per halaman
          <select
            :value="perPage"
            class="ml-2 rounded-md border border-gray-300 px-2 py-1 text-xs dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
            @change="updatePerPage"
          >
            <option v-for="option in props.pagination.perPageOptions" :key="`per-page-${option}`" :value="option">
              {{ option }}
            </option>
          </select>
        </label>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full min-w-[760px] text-sm">
          <thead class="border-b border-gray-200 dark:border-slate-700">
            <tr class="text-left text-gray-600 dark:text-gray-300">
              <th class="px-3 py-3 font-semibold text-center">No</th>
              <th class="px-3 py-3 font-semibold">Tingkat</th>
              <th class="px-3 py-3 font-semibold text-center">Jumlah Kelompok</th>
              <th class="px-3 py-3 font-semibold text-center">Jumlah Peserta</th>
              <th class="px-3 py-3 font-semibold">Keterangan</th>
              <th class="px-3 py-3 font-semibold text-center">Tahun Anggaran</th>
              <th class="px-3 py-3 font-semibold w-40">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(item, index) in props.praKoperasiItems.data"
              :key="item.id"
              class="border-b border-gray-100 align-top dark:border-slate-800"
            >
              <td class="px-3 py-3 text-center text-gray-700 dark:text-gray-300">{{ index + 1 }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ tingkatLabel(item.tingkat) }}</td>
              <td class="px-3 py-3 text-center text-gray-700 dark:text-gray-300">{{ item.jumlah_kelompok }}</td>
              <td class="px-3 py-3 text-center text-gray-700 dark:text-gray-300">{{ item.jumlah_peserta }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ item.keterangan || '-' }}</td>
              <td class="px-3 py-3 text-center text-gray-700 dark:text-gray-300">{{ item.tahun_anggaran }}</td>
              <td class="px-3 py-3">
                <div class="flex items-center gap-2">
                  <Link
                    :href="`/kecamatan/pra-koperasi-up2k/${item.id}`"
                    class="inline-flex rounded-md border border-sky-200 px-3 py-1.5 text-xs font-semibold text-sky-700 hover:bg-sky-50 dark:border-sky-900/50 dark:text-sky-300 dark:hover:bg-sky-900/20"
                  >
                    Lihat
                  </Link>
                  <Link
                    :href="`/kecamatan/pra-koperasi-up2k/${item.id}/edit`"
                    class="inline-flex rounded-md border border-amber-200 px-3 py-1.5 text-xs font-semibold text-amber-700 hover:bg-amber-50 dark:border-amber-900/50 dark:text-amber-300 dark:hover:bg-amber-900/20"
                  >
                    Edit
                  </Link>
                  <button
                    type="button"
                    class="inline-flex rounded-md border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-50 dark:border-rose-900/50 dark:text-rose-300 dark:hover:bg-rose-900/20"
                    @click="hapusPraKoperasi(item.id)"
                  >
                    Hapus
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="props.praKoperasiItems.data.length === 0">
              <td colspan="7" class="px-3 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                Data pra koperasi/UP2K belum tersedia.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <PaginationBar
        :links="props.praKoperasiItems.links"
        :from="props.praKoperasiItems.from"
        :to="props.praKoperasiItems.to"
        :total="props.praKoperasiItems.total"
      />
    </CardBox>

    <ConfirmActionModal
      v-model="isDeleteModalActive"
      title="Konfirmasi Hapus"
      :message="deleteConfirmationMessage"
      confirm-label="Ya, Hapus"
      @confirm="confirmDelete"
      @cancel="cancelDelete"
    />
  </SectionMain>
</template>
