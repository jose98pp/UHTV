import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { assetValidationPlugin } from './vite-plugins/asset-validation.js';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/browser-compatibility.css',
                'resources/css/dark-mode.css',
                'resources/css/show-dark-mode.css',
                'resources/js/app.js'
            ],
            refresh: true,
        }),
        assetValidationPlugin({
            strict: process.env.NODE_ENV === 'production',
            logLevel: 'warn'
        }),
    ],
    build: {
        cssCodeSplit: true,
        cssMinify: true,
        rollupOptions: {
            output: {
                // Optimized asset naming strategy for better caching
                assetFileNames: (assetInfo) => {
                    const info = assetInfo.name.split('.');
                    const extType = info[info.length - 1];

                    if (/png|jpe?g|svg|gif|tiff|bmp|ico/i.test(extType)) {
                        return `images/[name]-[hash:8][extname]`;
                    }
                    if (/css/i.test(extType)) {
                        if (assetInfo.name.includes('app')) return `css/core/app-[hash:8][extname]`;
                        if (assetInfo.name.includes('show-dark-mode')) return `css/pages/show-dark-mode-[hash:8][extname]`;
                        if (assetInfo.name.includes('dark-mode')) return `css/themes/dark-mode-[hash:8][extname]`;
                        if (assetInfo.name.includes('browser-compatibility')) return `css/compatibility/browser-compat-[hash:8][extname]`;
                        return `css/[name]-[hash:8][extname]`;
                    }
                    if (/js/i.test(extType)) return `js/[name]-[hash:8][extname]`;
                    return `assets/[name]-[hash:8][extname]`;
                },

                chunkFileNames: () => `js/chunks/[name]-[hash:8].js`,
                entryFileNames: () => `js/[name]-[hash:8].js`,

                manualChunks: (id) => {
                    if (id.includes('node_modules')) return 'vendor';
                    if (id.includes('show-dark-mode')) return 'show-page';
                }
            }
        },

        cssTarget: 'es2015',
        assetsInlineLimit: 4096,
        chunkSizeWarningLimit: 500
    },

    server: {
        hmr: { overlay: false }
    }
});