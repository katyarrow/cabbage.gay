import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import tailwindcss from '@tailwindcss/vite'
import { resolve } from 'path';

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
            vue: 'vue/dist/vue.esm-bundler.js',
            '#root': resolve(__dirname) + '/resources/js',
        },
    },
    server: {
        watch: {
            usePolling: true
        },
    },
    "compilerOptions": {
        "baseUrl": ".",
        "paths": {
            "root/*": ["*"]
        }
    }
})