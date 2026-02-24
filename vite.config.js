import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import tailwindcss from '@tailwindcss/vite';
import { fileURLToPath, URL } from 'node:url';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue(),
        tailwindcss(),
    ],
    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./resources/js', import.meta.url)),
        },
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (!id.includes('node_modules')) {
                        return;
                    }

                    if (/[\\/]node_modules[\\/](apexcharts|vue3-apexcharts)[\\/]/.test(id)) {
                        return 'vendor-apexcharts';
                    }

                    if (/[\\/]node_modules[\\/](@inertiajs|vue|@vue|pinia)[\\/]/.test(id)) {
                        return 'vendor-vue';
                    }

                    if (/[\\/]node_modules[\\/]@mdi[\\/]/.test(id)) {
                        return 'vendor-mdi';
                    }

                    return 'vendor';
                },
            },
        },
    },
});
