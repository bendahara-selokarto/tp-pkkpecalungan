<script setup>
import NotificationBar from '@/admin-one/components/NotificationBar.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { mdiCheckCircle } from '@mdi/js';
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
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800/70">
        <NotificationBar v-if="status" color="success" :icon="mdiCheckCircle">
            {{ status }}
        </NotificationBar>

        <form method="POST" :action="routes.login" class="space-y-5" @submit.prevent="submit">
            <div>
                <label for="email" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">{{ labels.email }}</label>
                <input
                    id="email"
                    v-model="form.email"
                    class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100 dark:focus:border-emerald-400 dark:focus:ring-emerald-900/50"
                    type="email"
                    name="email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="nama@email.com"
                >
                <ul v-if="emailErrors.length" class="mt-2 space-y-1 text-sm text-rose-600 dark:text-rose-400">
                    <li v-for="(message, index) in emailErrors" :key="`email-${index}`">{{ message }}</li>
                </ul>
            </div>

            <div>
                <label for="password" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">{{ labels.password }}</label>
                <input
                    id="password"
                    v-model="form.password"
                    class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100 dark:focus:border-emerald-400 dark:focus:ring-emerald-900/50"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="Masukkan password"
                >
                <ul v-if="passwordErrors.length" class="mt-2 space-y-1 text-sm text-rose-600 dark:text-rose-400">
                    <li v-for="(message, index) in passwordErrors" :key="`password-${index}`">{{ message }}</li>
                </ul>
            </div>

            <div class="flex items-center justify-between gap-4">
                <label for="remember_me" class="inline-flex cursor-pointer items-center gap-2">
                    <input
                        id="remember_me"
                        v-model="form.remember"
                        type="checkbox"
                        class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 dark:border-slate-600 dark:bg-slate-900 dark:focus:ring-emerald-600"
                        name="remember"
                    >
                    <span class="text-sm text-slate-600 dark:text-slate-300">{{ labels.rememberMe }}</span>
                </label>

                <a
                    v-if="canResetPassword && routes.passwordRequest"
                    class="text-sm font-medium text-emerald-700 hover:text-emerald-800 dark:text-emerald-400 dark:hover:text-emerald-300"
                    :href="routes.passwordRequest"
                >
                    {{ labels.forgotPassword }}
                </a>
            </div>

            <button
                type="submit"
                class="inline-flex w-full items-center justify-center rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60 dark:focus:ring-offset-slate-800"
                :disabled="form.processing"
            >
                {{ form.processing ? 'Memproses...' : labels.logIn }}
            </button>
        </form>
    </div>
</template>
