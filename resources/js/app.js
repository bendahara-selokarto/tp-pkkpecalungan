import './bootstrap';
import '../css/admin-one/main.css';
import Alpine from 'alpinejs';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createPinia } from 'pinia';
import { createApp, h } from 'vue';
import VueApexCharts from 'vue3-apexcharts';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import LayoutGuest from '@/admin-one/layouts/LayoutGuest.vue';
import { useDarkModeStore } from '@/admin-one/stores/darkMode';

window.Alpine = Alpine;
Alpine.start();

const appName = import.meta.env.VITE_APP_NAME || 'Akaraya PKK';
const pinia = createPinia();
const runtimeErrorEventName = 'ui-runtime-error';
let runtimeErrorReportCount = 0;
const runtimeErrorReportLimit = 5;

const reportUiRuntimeError = (message, source) => {
    if (typeof window === 'undefined') {
        return;
    }

    if (runtimeErrorReportCount >= runtimeErrorReportLimit) {
        return;
    }

    runtimeErrorReportCount += 1;

    const payload = {
        message: String(message ?? 'Unknown runtime error').slice(0, 500),
        source: String(source ?? 'runtime').slice(0, 120),
        url: String(window.location?.href ?? '').slice(0, 500),
    };

    window.axios?.post('/ui/runtime-errors', payload).catch(() => {
        // Ignore telemetry failures so runtime fallback remains non-blocking.
    });
};

const emitUiRuntimeError = (error, source = 'runtime') => {
    const message = error instanceof Error ? error.message : String(error ?? 'Unknown runtime error');

    console.error(`[${source}]`, error);
    reportUiRuntimeError(message, source);

    if (typeof window !== 'undefined') {
        window.dispatchEvent(new CustomEvent(runtimeErrorEventName, {
            detail: {
                message,
                source,
            },
        }));
    }
};

const installGlobalRuntimeGuards = () => {
    if (typeof window === 'undefined') {
        return;
    }

    if (window.__uiRuntimeGuardsInstalled === true) {
        return;
    }

    window.__uiRuntimeGuardsInstalled = true;

    window.addEventListener('error', (event) => {
        emitUiRuntimeError(event?.error ?? event?.message, 'window.error');
    });

    window.addEventListener('unhandledrejection', (event) => {
        emitUiRuntimeError(event?.reason, 'window.unhandledrejection');
    });
};

installGlobalRuntimeGuards();

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: async (name) => {
        const page = await resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        );

        page.default.layout = page.default.layout
            || (name.startsWith('Auth/') ? LayoutGuest : DashboardLayout);

        return page;
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        app.config.errorHandler = (error, _instance, info) => {
            emitUiRuntimeError(error, `vue.error:${info}`);
        };

        app
            .use(plugin)
            .use(pinia)
            .use(VueApexCharts)
            .mount(el);
    },
    progress: {
        color: '#67E8F9',
    },
});

useDarkModeStore(pinia).init();
