/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/views/**/*.blade.php",
    ],
    theme: {
        extend: {
            colors: {
                background: {
                    DEFAULT: '#0a0a0a',
                    secondary: '#0f0f0f',
                    tertiary: '#141414',
                    hover: '#1a1a1a',
                },
                border: {
                    DEFAULT: '#2a2a2a',
                    secondary: '#262626',
                },
                accent: {
                    DEFAULT: '#f97316',
                    hover: '#ea580c',
                },
            },
            fontFamily: {
                sans: ['Rubik', 'sans-serif'],
                mono: ['IBM Plex Mono', 'monospace'],
            },
            animation: {
                'pulse-slow': 'pulse-slow 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                'glow': 'glow 2s ease-in-out infinite alternate',
            },
            keyframes: {
                'pulse-slow': {
                    '0%, 100%': { opacity: '1' },
                    '50%': { opacity: '0.7' },
                },
                'glow': {
                    from: { boxShadow: '0 0 5px #58a6ff' },
                    to: { boxShadow: '0 0 20px #58a6ff, 0 0 30px #58a6ff' },
                },
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/container-queries'),
    ],
};
