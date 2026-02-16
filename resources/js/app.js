import './bootstrap';
import '../css/admin-one/main.css';
import Alpine from 'alpinejs';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createPinia } from 'pinia';
import { createApp, h } from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { useDarkModeStore } from '@/admin-one/stores/darkMode';

window.Alpine = Alpine;
Alpine.start();

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
const pinia = createPinia();

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: async (name) => {
        const page = await resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        );

        if (!name.startsWith('Auth/')) {
            page.default.layout = page.default.layout || DashboardLayout;
        }

        return page;
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(pinia)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

useDarkModeStore(pinia).init();
