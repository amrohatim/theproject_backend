import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/animations.css',
                'resources/css/modern-landing.css',
                'resources/css/modern-auth.css',
                'resources/js/app.js',
                'resources/js/modern-interactions.js',
                'resources/js/vendor-registration.js',
                'resources/js/vendor-product-create.js',
                'resources/js/vendor-product-edit.js',
                'resources/js/merchant-registration.js',
                'resources/js/product-edit.js',
                'resources/js/product-create.js'
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        tailwindcss(),
    ],
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
        },
    },
});
