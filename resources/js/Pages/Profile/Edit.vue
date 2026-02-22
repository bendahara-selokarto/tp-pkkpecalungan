<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
import CardBoxModal from '@/admin-one/components/CardBoxModal.vue'
import SectionMain from '@/admin-one/components/SectionMain.vue'
import SectionTitleLineWithButton from '@/admin-one/components/SectionTitleLineWithButton.vue'
import { router, useForm } from '@inertiajs/vue3'
import { mdiAccount } from '@mdi/js'
import { computed, ref } from 'vue'

const props = defineProps({
  user: {
    type: Object,
    required: true,
  },
  mustVerifyEmail: {
    type: Boolean,
    default: false,
  },
  status: {
    type: String,
    default: null,
  },
})

const profileForm = useForm({
  name: props.user.name ?? '',
  email: props.user.email ?? '',
})

const passwordForm = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
})

const deleteForm = useForm({
  password: '',
})

const showDeleteConfirm = ref(false)

const isEmailUnverified = computed(() => props.mustVerifyEmail && !props.user.email_verified_at)
const profileSaved = computed(() => props.status === 'profile-updated')
const passwordSaved = computed(() => props.status === 'password-updated')
const verificationSent = computed(() => props.status === 'verification-link-sent')

const submitProfile = () => {
  profileForm.patch('/profile')
}

const submitPassword = () => {
  passwordForm.put('/password', {
    errorBag: 'updatePassword',
    preserveScroll: true,
    onSuccess: () => {
      passwordForm.reset()
    },
  })
}

const resendVerification = () => {
  router.post('/email/verification-notification', {}, { preserveScroll: true })
}

const openDeleteConfirm = () => {
  deleteForm.reset()
  deleteForm.clearErrors()
  showDeleteConfirm.value = true
}

const cancelDeleteConfirm = () => {
  showDeleteConfirm.value = false
  deleteForm.reset()
  deleteForm.clearErrors()
}

const submitDelete = () => {
  deleteForm.delete('/profile', {
    errorBag: 'userDeletion',
    preserveScroll: true,
    onSuccess: () => {
      cancelDeleteConfirm()
    },
  })
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiAccount" title="Profil" main />

    <div class="space-y-6">
      <CardBox class="max-w-3xl">
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Informasi Profil</h3>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Perbarui informasi profil akun dan alamat email.</p>

        <form class="mt-6 space-y-5" @submit.prevent="submitProfile">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama</label>
            <input v-model="profileForm.name" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required autocomplete="name">
            <p v-if="profileForm.errors.name" class="mt-1 text-xs text-rose-600">{{ profileForm.errors.name }}</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
            <input v-model="profileForm.email" type="email" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required autocomplete="username">
            <p v-if="profileForm.errors.email" class="mt-1 text-xs text-rose-600">{{ profileForm.errors.email }}</p>

            <div v-if="isEmailUnverified" class="mt-3 space-y-2">
              <p class="text-sm text-gray-700 dark:text-gray-300">Alamat email Anda belum terverifikasi.</p>
              <button type="button" class="text-sm font-medium text-emerald-700 hover:text-emerald-800 dark:text-emerald-400 dark:hover:text-emerald-300" @click="resendVerification">
                Klik di sini untuk mengirim ulang email verifikasi.
              </button>
              <p v-if="verificationSent" class="text-sm text-emerald-600 dark:text-emerald-400">
                Tautan verifikasi baru telah dikirim ke alamat email Anda.
              </p>
            </div>
          </div>

          <div class="flex items-center gap-3">
            <button type="submit" class="inline-flex rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60" :disabled="profileForm.processing">
              Simpan
            </button>
            <p v-if="profileSaved" class="text-sm text-gray-600 dark:text-gray-400">Tersimpan.</p>
          </div>
        </form>
      </CardBox>

      <CardBox class="max-w-3xl">
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Perbarui Kata Sandi</h3>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Pastikan akun Anda menggunakan kata sandi yang panjang dan acak agar tetap aman.</p>

        <form class="mt-6 space-y-5" @submit.prevent="submitPassword">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Kata Sandi Saat Ini</label>
            <input v-model="passwordForm.current_password" type="password" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" autocomplete="current-password">
            <p v-if="passwordForm.errors.current_password" class="mt-1 text-xs text-rose-600">{{ passwordForm.errors.current_password }}</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Kata Sandi Baru</label>
            <input v-model="passwordForm.password" type="password" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" autocomplete="new-password">
            <p v-if="passwordForm.errors.password" class="mt-1 text-xs text-rose-600">{{ passwordForm.errors.password }}</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Konfirmasi Kata Sandi</label>
            <input v-model="passwordForm.password_confirmation" type="password" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" autocomplete="new-password">
            <p v-if="passwordForm.errors.password_confirmation" class="mt-1 text-xs text-rose-600">{{ passwordForm.errors.password_confirmation }}</p>
          </div>

          <div class="flex items-center gap-3">
            <button type="submit" class="inline-flex rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60" :disabled="passwordForm.processing">
              Simpan
            </button>
            <p v-if="passwordSaved" class="text-sm text-gray-600 dark:text-gray-400">Tersimpan.</p>
          </div>
        </form>
      </CardBox>

      <CardBox class="max-w-3xl">
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Hapus Akun</h3>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Setelah akun dihapus, seluruh sumber daya dan data akan dihapus secara permanen.</p>

        <button type="button" class="mt-5 inline-flex rounded-md bg-rose-600 px-4 py-2 text-sm font-medium text-white hover:bg-rose-700" @click="openDeleteConfirm">
          Hapus Akun
        </button>
      </CardBox>
    </div>

    <CardBoxModal
      v-model="showDeleteConfirm"
      title="Hapus Akun"
      button="danger"
      button-label="Hapus Akun"
      cancel-label="Batal"
      :is-processing="deleteForm.processing"
      has-cancel
      is-form
      @confirm="submitDelete"
      @cancel="cancelDeleteConfirm"
    >
      <p class="mb-4 text-sm text-gray-700 dark:text-gray-200">
        Masukkan kata sandi untuk mengonfirmasi penghapusan akun.
      </p>
      <input
        v-model="deleteForm.password"
        type="password"
        class="w-full rounded-md border-rose-300 text-sm shadow-sm focus:border-rose-500 focus:ring-rose-500 dark:border-rose-800 dark:bg-slate-900 dark:text-slate-100"
        placeholder="Kata Sandi"
      >
      <p v-if="deleteForm.errors.password" class="mt-2 text-xs text-rose-600">{{ deleteForm.errors.password }}</p>
    </CardBoxModal>
  </SectionMain>
</template>
