import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'brand-blue': '#C9E7F2',
                'brand-pink': '#F29BAB',
                'brand-paspink': '#F2CED5',
                'brand-green': '#AFD9AD',
                'brand-brown': '#261A1A',
            },
        },
    },

    plugins: [forms],
};
