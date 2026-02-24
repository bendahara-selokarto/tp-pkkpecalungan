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
        createApp({ render: () => h(App, props) })
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
