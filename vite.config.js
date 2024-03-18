import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/top.css',
                'resources/css/farm.css',
                'resources/css/owner.css',
                'resources/js/app.js',
                'resources/js/swiper.js',
            ],
            refresh: true,
        }),
    ],
});
