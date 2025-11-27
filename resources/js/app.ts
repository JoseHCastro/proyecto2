import '../css/app.css';

import axios from 'axios';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { initializeTheme } from './composables/useAppearance';
import { setBaseUrl, getBaseUrl } from './utils/baseUrl';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        // Configurar la URL base para las rutas
        const baseUrlProp = (props.initialPage.props as any).base_url || '';
        if (baseUrlProp && !baseUrlProp.includes('localhost')) {
            try {
                const url = new URL(baseUrlProp);
                const basePath = url.pathname.endsWith('/') ? url.pathname.slice(0, -1) : url.pathname;
                setBaseUrl(basePath);
                
                // Interceptor de Axios para agregar el prefijo de URL a todas las peticiones
                axios.interceptors.request.use((config) => {
                    const currentBasePath = getBaseUrl();
                    if (currentBasePath && config.url) {
                        // Solo agregar prefijo a URLs relativas que empiecen con /
                        if (config.url.startsWith('/') && !config.url.startsWith(currentBasePath)) {
                            config.url = currentBasePath + config.url;
                        }
                    }
                    return config;
                });
            } catch {
                setBaseUrl(baseUrlProp);
            }
        }
        
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
    id: 'app',
});

// This will set light / dark mode on page load...
initializeTheme();
