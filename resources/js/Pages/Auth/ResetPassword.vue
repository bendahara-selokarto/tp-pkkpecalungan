<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    token: {
        type: String,
        required: true,
    },
    email: {
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
    token: props.token,
    email: props.email ?? '',
    password: '',
    password_confirmation: '',
});

const errorList = (field) => computed(() => {
    const messages = page.props.errors?.[field];

    if (!messages) {
        return [];
    }

    return Array.isArray(messages) ? messages : [messages];
});

const emailErrors = errorList('email');
const passwordErrors = errorList('password');
const passwordConfirmationErrors = errorList('password_confirmation');

const submit = () => {
    form.post(props.routes.passwordStore, {
        onError: () => {
            form.password = '';
            form.password_confirmation = '';
        },
        onFinish: () => {
            form.password = '';
            form.password_confirmation = '';
        },
    });
};
</script>

<template>
    <form method="POST" :action="routes.passwordStore" @submit.prevent="submit">
        <input type="hidden" name="token" :value="form.token">

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
                autocomplete="username"
            >
            <ul v-if="emailErrors.length" class="mt-2 text-sm text-red-600 dark:text-red-400 space-y-1">
                <li v-for="(message, index) in emailErrors" :key="`email-${index}`">{{ message }}</li>
            </ul>
        </div>

        <div class="mt-4">
            <label for="password" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ labels.password }}</label>
            <input
                id="password"
                v-model="form.password"
                class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                type="password"
                name="password"
                required
                autocomplete="new-password"
            >
            <ul v-if="passwordErrors.length" class="mt-2 text-sm text-red-600 dark:text-red-400 space-y-1">
                <li v-for="(message, index) in passwordErrors" :key="`password-${index}`">{{ message }}</li>
            </ul>
        </div>

        <div class="mt-4">
            <label for="password_confirmation" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ labels.passwordConfirmation }}</label>

            <input
                id="password_confirmation"
                v-model="form.password_confirmation"
                class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
            >

            <ul v-if="passwordConfirmationErrors.length" class="mt-2 text-sm text-red-600 dark:text-red-400 space-y-1">
                <li v-for="(message, index) in passwordConfirmationErrors" :key="`password-confirmation-${index}`">{{ message }}</li>
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
