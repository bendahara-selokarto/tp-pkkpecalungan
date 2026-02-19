<script setup>
import CardBox from '@/admin-one/components/CardBox.vue'
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

const submitDelete = () => {
  deleteForm.delete('/profile', {
    errorBag: 'userDeletion',
    preserveScroll: true,
  })
}
</script>

<template>
  <SectionMain>
    <SectionTitleLineWithButton :icon="mdiAccount" title="Profile" main />

    <div class="space-y-6">
      <CardBox class="max-w-3xl">
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Profile Information</h3>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Update your account's profile information and email address.</p>

        <form class="mt-6 space-y-5" @submit.prevent="submitProfile">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
            <input v-model="profileForm.name" type="text" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required autocomplete="name">
            <p v-if="profileForm.errors.name" class="mt-1 text-xs text-rose-600">{{ profileForm.errors.name }}</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
            <input v-model="profileForm.email" type="email" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" required autocomplete="username">
            <p v-if="profileForm.errors.email" class="mt-1 text-xs text-rose-600">{{ profileForm.errors.email }}</p>

            <div v-if="isEmailUnverified" class="mt-3 space-y-2">
              <p class="text-sm text-gray-700 dark:text-gray-300">Your email address is unverified.</p>
              <button type="button" class="text-sm font-medium text-emerald-700 hover:text-emerald-800 dark:text-emerald-400 dark:hover:text-emerald-300" @click="resendVerification">
                Click here to re-send the verification email.
              </button>
              <p v-if="verificationSent" class="text-sm text-emerald-600 dark:text-emerald-400">
                A new verification link has been sent to your email address.
              </p>
            </div>
          </div>

          <div class="flex items-center gap-3">
            <button type="submit" class="inline-flex rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60" :disabled="profileForm.processing">
              Save
            </button>
            <p v-if="profileSaved" class="text-sm text-gray-600 dark:text-gray-400">Saved.</p>
          </div>
        </form>
      </CardBox>

      <CardBox class="max-w-3xl">
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Update Password</h3>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Ensure your account is using a long, random password to stay secure.</p>

        <form class="mt-6 space-y-5" @submit.prevent="submitPassword">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Current Password</label>
            <input v-model="passwordForm.current_password" type="password" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" autocomplete="current-password">
            <p v-if="passwordForm.errors.current_password" class="mt-1 text-xs text-rose-600">{{ passwordForm.errors.current_password }}</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">New Password</label>
            <input v-model="passwordForm.password" type="password" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" autocomplete="new-password">
            <p v-if="passwordForm.errors.password" class="mt-1 text-xs text-rose-600">{{ passwordForm.errors.password }}</p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm Password</label>
            <input v-model="passwordForm.password_confirmation" type="password" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" autocomplete="new-password">
            <p v-if="passwordForm.errors.password_confirmation" class="mt-1 text-xs text-rose-600">{{ passwordForm.errors.password_confirmation }}</p>
          </div>

          <div class="flex items-center gap-3">
            <button type="submit" class="inline-flex rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60" :disabled="passwordForm.processing">
              Save
            </button>
            <p v-if="passwordSaved" class="text-sm text-gray-600 dark:text-gray-400">Saved.</p>
          </div>
        </form>
      </CardBox>

      <CardBox class="max-w-3xl">
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Delete Account</h3>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Once your account is deleted, all of its resources and data will be permanently deleted.</p>

        <button type="button" class="mt-5 inline-flex rounded-md bg-rose-600 px-4 py-2 text-sm font-medium text-white hover:bg-rose-700" @click="showDeleteConfirm = true">
          Delete Account
        </button>

        <div v-if="showDeleteConfirm" class="mt-5 rounded-lg border border-rose-200 bg-rose-50 p-4 dark:border-rose-900/40 dark:bg-rose-900/20">
          <p class="text-sm text-rose-700 dark:text-rose-300">
            Please enter your password to confirm account deletion.
          </p>

          <form class="mt-4 space-y-3" @submit.prevent="submitDelete">
            <input v-model="deleteForm.password" type="password" class="w-full rounded-md border-rose-300 text-sm shadow-sm focus:border-rose-500 focus:ring-rose-500 dark:border-rose-800 dark:bg-slate-900 dark:text-slate-100" placeholder="Password">
            <p v-if="deleteForm.errors.password" class="text-xs text-rose-600">{{ deleteForm.errors.password }}</p>
            <div class="flex items-center justify-end gap-2">
              <button type="button" class="inline-flex rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800" @click="showDeleteConfirm = false">
                Cancel
              </button>
              <button type="submit" class="inline-flex rounded-md bg-rose-600 px-4 py-2 text-sm font-medium text-white hover:bg-rose-700 disabled:cursor-not-allowed disabled:opacity-60" :disabled="deleteForm.processing">
                Delete Account
              </button>
            </div>
          </form>
        </div>
      </CardBox>
    </div>
  </SectionMain>
</template>
