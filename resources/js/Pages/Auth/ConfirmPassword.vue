<script setup>
import { useForm, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  routes: {
    type: Object,
    required: true,
  },
  labels: {
    type: Object,
    required: true,
  },
})

const page = usePage()

const form = useForm({
  password: '',
})

const passwordErrors = computed(() => {
  const messages = page.props.errors?.password
  if (!messages) {
    return []
  }

  return Array.isArray(messages) ? messages : [messages]
})

const submit = () => {
  form.post(props.routes.passwordConfirm, {
    onFinish: () => {
      form.password = ''
    },
  })
}
</script>

<template>
  <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
    {{ labels.description }}
  </div>

  <form method="POST" :action="routes.passwordConfirm" @submit.prevent="submit">
    <div>
      <label for="password" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ labels.password }}</label>
      <input
        id="password"
        v-model="form.password"
        class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
        type="password"
        name="password"
        required
        autocomplete="current-password"
      >
      <ul v-if="passwordErrors.length" class="mt-2 text-sm text-red-600 dark:text-red-400 space-y-1">
        <li v-for="(message, index) in passwordErrors" :key="`password-${index}`">{{ message }}</li>
      </ul>
    </div>

    <div class="flex justify-end mt-4">
      <button
        type="submit"
        class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white"
        :disabled="form.processing"
      >
        {{ labels.submit }}
      </button>
    </div>
  </form>
</template>
