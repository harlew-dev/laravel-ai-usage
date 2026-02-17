/** @type {import('vite').UserConfig} */
export default {
    build: {
        assetsDir: "",
        rollupOptions: {
            input: ["resources/js/dashboard.js", "resources/css/dashboard.css"],
            output: {
                assetFileNames: "[name][extname]",
                entryFileNames: "[name].js",
            },
        },
    },
};
