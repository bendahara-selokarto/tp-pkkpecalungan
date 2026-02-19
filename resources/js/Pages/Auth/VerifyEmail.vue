<script setup>
import { router } from '@inertiajs/vue3'

const props = defineProps({
  status: {
    type: String,
    default: null,
  },
  routes: {
    type: Object,
    required: true,
  },
  labels: {
    type: Object,
    required: true,
  },
})

const resend = () => {
  router.post(props.routes.verificationSend)
}

const logout = () => {
  router.post(props.routes.logout)
}
</script>

<template>
  <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
    {{ labels.description }}
  </div>

  <div v-if="status === 'verification-link-sent'" class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
    {{ labels.resent }}
  </div>

  <div class="mt-4 flex items-center justify-between">
    <button
      type="button"
      class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white"
      @click="resend"
    >
      {{ labels.resend }}
    </button>

    <button type="button" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100" @click="logout">
      {{ labels.logout }}
    </button>
  </div>
</template>
