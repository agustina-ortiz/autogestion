import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    safelist: [
        'bg-purple-50',
        'border-purple-400',
        'text-purple-700',
        'bg-purple-100',
        'text-purple-800',

        'bg-orange-50',
        'border-orange-400',
        'text-orange-700',
        'bg-orange-100',
        'text-orange-800',

        'bg-pink-50',
        'border-pink-400',
        'text-pink-700',
        'bg-pink-100',
        'text-pink-800',

        'bg-indigo-50',
        'border-indigo-400',
        'text-indigo-700',
        'bg-indigo-100',
        'text-indigo-800',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
