/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
          './app/Livewire/**/*.php',
    ],
    theme: {
        extend: {
            colors: {
                // Palet utama Bajuku: pink lembut
                brand: {
                    50:  '#fff5f8',
                    100: '#ffe8f0',
                    200: '#ffd0e2',
                    300: '#ffa8c9',
                    400: '#ff78aa',
                    500: '#f0508a',
                    600: '#d93573',
                    700: '#b32660',
                    800: '#8f2150',
                    900: '#73203f',
                },
            },
            fontFamily: {
                sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                display: ['"Playfair Display"', 'ui-serif', 'Georgia', 'serif'],
            },
        },
    },
    plugins: [],
};