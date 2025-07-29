import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from "@vitejs/plugin-vue";

export default defineConfig({
    plugins: [
        vue(),
        laravel({
            input: ['resources/js/app.js'],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            vue: "vue/dist/vue.esm-bundler.js",
        }
    },
    server: {
        host: '0.0.0.0',  // Allows access from the network (not just localhost)
        port: 8000,  // Default port Vite uses, you can change if needed
        hmr: {
            host: '192.168.1.6',  // Replace this with your local IP
        },
    },
});
