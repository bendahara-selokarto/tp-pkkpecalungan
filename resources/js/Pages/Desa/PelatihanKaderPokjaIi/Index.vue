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
  pelatihanKaderItems: {
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

  router.get('/desa/pelatihan-kader-pokja-ii', {
    ...props.filters,
    page: 1,
    per_page: selectedPerPage,
  }, {
    preserveScroll: true,
    preserveState: true,
    replace: true,
  })
}

const deleteConfirmationMessage = 'Apakah Anda yakin ingin menghapus data pelatihan kader Pokja II ini?'
const isDeleteModalActive = ref(false)
const deletingId = ref(null)

const hapusPelatihan = (id) => {
  deletingId.value = id
  isDeleteModalActive.value = true
}

const confirmDelete = () => {
  if (deletingId.value === null) {
    return
  }

  router.delete(`/desa/pelatihan-kader-pokja-ii/${deletingId.value}`, {
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

const kategoriLabel = (value) => {
  if (value === 'lp3') {
    return 'LP3 PKK'
  }

  if (value === 'tpk_3_pkk') {
    return 'TPK 3 PKK'
  }

  if (value === 'damas_pkk') {
    return 'Damas PKK'
  }

  return value || '-'
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiAccountGroup" title="Pelatihan Kader Pokja II Desa" main />

    <CardBox>
      <div class="mb-4 flex items-center justify-between gap-4">
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Daftar Pelatihan Kader Pokja II</h3>
        <Link
          href="/desa/pelatihan-kader-pokja-ii/create"
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
        <table class="w-full min-w-[700px] text-sm">
          <thead class="border-b border-gray-200 dark:border-slate-700">
            <tr class="text-left text-gray-600 dark:text-gray-300">
              <th class="px-3 py-3 font-semibold text-center">No</th>
              <th class="px-3 py-3 font-semibold">Kategori Pelatihan</th>
              <th class="px-3 py-3 font-semibold text-center">Jumlah Kader</th>
              <th class="px-3 py-3 font-semibold">Keterangan</th>
              <th class="px-3 py-3 font-semibold text-center">Tahun Anggaran</th>
              <th class="px-3 py-3 font-semibold w-40">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(item, index) in props.pelatihanKaderItems.data"
              :key="item.id"
              class="border-b border-gray-100 align-top dark:border-slate-800"
            >
              <td class="px-3 py-3 text-center text-gray-700 dark:text-gray-300">{{ index + 1 }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ kategoriLabel(item.kategori_pelatihan) }}</td>
              <td class="px-3 py-3 text-center text-gray-700 dark:text-gray-300">{{ item.jumlah_kader }}</td>
              <td class="px-3 py-3 text-gray-700 dark:text-gray-300">{{ item.keterangan || '-' }}</td>
              <td class="px-3 py-3 text-center text-gray-700 dark:text-gray-300">{{ item.tahun_anggaran }}</td>
              <td class="px-3 py-3">
                <div class="flex items-center gap-2">
                  <Link
                    :href="`/desa/pelatihan-kader-pokja-ii/${item.id}`"
                    class="inline-flex rounded-md border border-sky-200 px-3 py-1.5 text-xs font-semibold text-sky-700 hover:bg-sky-50 dark:border-sky-900/50 dark:text-sky-300 dark:hover:bg-sky-900/20"
                  >
                    Lihat
                  </Link>
                  <Link
                    :href="`/desa/pelatihan-kader-pokja-ii/${item.id}/edit`"
                    class="inline-flex rounded-md border border-amber-200 px-3 py-1.5 text-xs font-semibold text-amber-700 hover:bg-amber-50 dark:border-amber-900/50 dark:text-amber-300 dark:hover:bg-amber-900/20"
                  >
                    Edit
                  </Link>
                  <button
                    type="button"
                    class="inline-flex rounded-md border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-50 dark:border-rose-900/50 dark:text-rose-300 dark:hover:bg-rose-900/20"
                    @click="hapusPelatihan(item.id)"
                  >
                    Hapus
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="props.pelatihanKaderItems.data.length === 0">
              <td colspan="6" class="px-3 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                Data pelatihan kader Pokja II belum tersedia.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <PaginationBar
        :links="props.pelatihanKaderItems.links"
        :from="props.pelatihanKaderItems.from"
        :to="props.pelatihanKaderItems.to"
        :total="props.pelatihanKaderItems.total"
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
