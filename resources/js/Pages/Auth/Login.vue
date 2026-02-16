<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    status: {
        type: String,
        default: null,
    },
    canResetPassword: {
        type: Boolean,
        default: false,
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
    password: '',
    remember: false,
});

const toArray = (messages) => {
    if (!messages) {
        return [];
    }

    return Array.isArray(messages) ? messages : [messages];
};

const emailErrors = computed(() => toArray(page.props.errors?.email));
const passwordErrors = computed(() => toArray(page.props.errors?.password));

const submit = () => {
    form.post(props.routes.login, {
        onError: () => {
            form.password = '';
            form.remember = false;
        },
        onFinish: () => {
            form.password = '';
        },
    });
};
</script>

<template>
    <div v-if="status" class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
        {{ status }}
    </div>

    <form method="POST" :action="routes.login" @submit.prevent="submit">
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
                autocomplete="current-password"
            >

            <ul v-if="passwordErrors.length" class="mt-2 text-sm text-red-600 dark:text-red-400 space-y-1">
                <li v-for="(message, index) in passwordErrors" :key="`password-${index}`">{{ message }}</li>
            </ul>
        </div>

        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input
                    id="remember_me"
                    v-model="form.remember"
                    type="checkbox"
                    class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                    name="remember"
                >
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ labels.rememberMe }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            <a
                v-if="canResetPassword && routes.passwordRequest"
                class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                :href="routes.passwordRequest"
            >
                {{ labels.forgotPassword }}
            </a>

            <button
                type="submit"
                class="ms-3 inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
            >
                {{ labels.logIn }}
            </button>
        </div>
    </form>
</template>
