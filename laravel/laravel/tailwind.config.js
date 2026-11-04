import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],
    darkMode: 'class',
    theme: {
        extend: {
            colors: {
                brand: {
                    pink: '#ff2bd6',
                    cyan: '#19e3ff',
                    dark: '#07030d',
                    card: '#0f0716',
                    cardHover: '#170c22',
                    border: 'rgba(255, 255, 255, 0.1)',
                },
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                display: ['Unbounded', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    plugins: [],
};
