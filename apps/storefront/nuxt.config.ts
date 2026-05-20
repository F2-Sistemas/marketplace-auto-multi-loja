import tailwindcss from '@tailwindcss/vite';

// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
    compatibilityDate: '2025-07-15',
    devtools: { enabled: true },
    css: ['~/assets/css/main.css'],
    router: {
        middleware: ['storeAuth'],
    },
    vue: {
        compilerOptions: {
            isCustomElement: (tag) => tag === 'iconify-icon',
        },
    },
    vite: {
        plugins: [tailwindcss()],
        server: {
            // @ts-ignore
            port: 3000,
            strictPort: false,

            // host: '0.0.0.0',
            host: true,

            // allowedHosts: [
            //     // 'app.domain.com',
            // ],
            allowedHosts: true,
            proxy: {
                // env: OPEN_IN_EDITOR_URL='http://host.docker.internal:3001'
                /** @url https://rederevenda.com/_nuxt/__open-in-editor?file=%2Fapp%2Fapp%2Fcomponents%2Fsearch%2FVehicleFilters.vue%3A197%3A10 */
                '/__open-in-editor': {
                    target:
                        (process.env.OPEN_IN_EDITOR_URL || 'http://localhost:3001') +
                        [
                            true ? '?open=true' : '?open=false',
                            process.env.PROJECT_DIR ? `&project_dir=${process.env.PROJECT_DIR}` : '',
                        ].join(''),
                    changeOrigin: true,
                },
                '/_nuxt/__open-in-editor': {
                    target:
                        (process.env.OPEN_IN_EDITOR_URL || 'http://localhost:3001') +
                        [
                            true ? '?open=true' : '?open=false',
                            process.env.PROJECT_DIR ? `&project_dir=${process.env.PROJECT_DIR}` : '',
                        ].join(''),
                    changeOrigin: true,
                },
            },
        },
    },
    app: {
        pageTransition: { name: 'page', mode: 'out-in' },
        head: {
            htmlAttrs: {
                lang: 'pt-BR',
            },
            title: 'AutoHub Storefront',
            script: [{ src: 'https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js' }],
            meta: [
                { charset: 'utf-8' },
                { name: 'viewport', content: 'width=device-width, initial-scale=1' },
                { name: 'description', content: 'AutoHub Storefront - Sua vitrine digital premium de veículos.' },
            ],
            link: [
                { rel: 'preconnect', href: 'https://fonts.googleapis.com' },
                { rel: 'preconnect', href: 'https://fonts.gstatic.com', crossorigin: '' },
                {
                    rel: 'stylesheet',
                    href: 'https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap',
                },
            ],
        },
    },
});
