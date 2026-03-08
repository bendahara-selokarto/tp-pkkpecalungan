import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import tailwindcss from '@tailwindcss/vite';
import { fileURLToPath, URL } from 'node:url';

const shouldAnalyzeBundle = process.env.VITE_ANALYZE === 'true';

const getAnalyzePlugin = async () => {
    if (!shouldAnalyzeBundle) {
        return null;
    }

    try {
        const { visualizer } = await import('rollup-plugin-visualizer');

        return visualizer({
            filename: 'reports/vite-bundle-analysis.html',
            template: 'treemap',
            gzipSize: true,
            brotliSize: true,
            open: false,
        });
    } catch (error) {
        console.warn(
            '[vite] Bundle analysis skipped because rollup-plugin-visualizer is not installed.',
        );

        return null;
    }
};

const getNodeModulePackageName = (id) => {
    const [modulePath] = id.split('?');
    const nodeModulesSegment = modulePath.split(/node_modules[\\/]/).at(1);

    if (!nodeModulesSegment) {
        return null;
    }

    const packageParts = nodeModulesSegment.split(/[\\/]/).filter(Boolean);
    if (packageParts.length === 0) {
        return null;
    }

    if (packageParts[0].startsWith('@') && packageParts.length > 1) {
        return `${packageParts[0].slice(1)}-${packageParts[1]}`;
    }

    return packageParts[0];
};

export default defineConfig(async () => {
    const analyzePlugin = await getAnalyzePlugin();

    return {
        plugins: [
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.js'],
                refresh: true,
            }),
            vue(),
            tailwindcss(),
            analyzePlugin,
        ].filter(Boolean),
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

                        if (/[\\/]node_modules[\\/]apexcharts[\\/]dist[\\/]core\./.test(id)) {
                            return 'vendor-apex-core';
                        }

                        if (/[\\/]node_modules[\\/]apexcharts[\\/]dist[\\/]bar\./.test(id)) {
                            return 'vendor-apex-bar';
                        }

                        if (/[\\/]node_modules[\\/]apexcharts[\\/]dist[\\/]pie\./.test(id)) {
                            return 'vendor-apex-pie';
                        }

                        if (/[\\/]node_modules[\\/]vue-router[\\/]/.test(id)) {
                            return 'vendor-vue-router';
                        }

                        if (/[\\/]node_modules[\\/](@inertiajs|vue|@vue|pinia)[\\/]/.test(id)) {
                            return 'vendor-vue';
                        }

                        if (/[\\/]node_modules[\\/]@mdi[\\/]/.test(id)) {
                            return 'vendor-mdi';
                        }

                        const packageName = getNodeModulePackageName(id);
                        if (packageName && ['axios', 'lodash-es', 'qs'].includes(packageName)) {
                            return `vendor-${packageName.replace(/[^a-z0-9-]/gi, '').toLowerCase()}`;
                        }

                        return 'vendor-misc';
                    },
                },
            },
        },
    };
});
