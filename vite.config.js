import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/appfarm.css',
                'resources/css/top.css',
                'resources/css/topshow.css',
                'resources/css/farm.css',
                'resources/css/owner.css',
                'resources/js/app.js',
                'resources/js/swiper.js',
                'resources/js/swipershow.js',
            ],
            refresh: true,
        }),
    ],
});
