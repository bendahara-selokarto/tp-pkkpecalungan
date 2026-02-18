<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

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
});

const page = usePage();

const form = useForm({
    email: '',
});

const emailErrors = computed(() => {
    const messages = page.props.errors?.email;

    if (!messages) {
        return [];
    }

    return Array.isArray(messages) ? messages : [messages];
});

const submit = () => {
    form.post(props.routes.passwordEmail);
};
</script>

<template>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        {{ labels.description }}
    </div>

    <div v-if="status" class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
        {{ status }}
    </div>

    <form method="POST" :action="routes.passwordEmail" @submit.prevent="submit">
        <div>
            <label for="email" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ labels.email }}</label>
            <input
                id="email"
                v-model="form.email"
                class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                type="email"
                name="email"
                required
                autofocus
            >
            <ul v-if="emailErrors.length" class="mt-2 text-sm text-red-600 dark:text-red-400 space-y-1">
                <li v-for="(message, index) in emailErrors" :key="`email-${index}`">{{ message }}</li>
            </ul>
        </div>

        <div class="flex items-center justify-end mt-4">
            <button
                type="submit"
                class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
            >
                {{ labels.submit }}
            </button>
        </div>
    </form>
</template>
