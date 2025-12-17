import '../css/app.css';

import axios from 'axios';
import { createInertiaApp, router, Link } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h, defineComponent, computed } from 'vue';
import { initializeTheme } from './composables/useAppearance';
import { setBaseUrl, getBaseUrl } from './utils/baseUrl';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Helper function to prepend base URL
const prependBaseUrl = (url: string): string => {
    const basePath = getBaseUrl();
    if (basePath && url.startsWith('/') && !url.startsWith(basePath)) {
        return basePath + url;
    }
    return url;
};

// Store the original router methods
const originalVisit = router.visit.bind(router);
const originalGet = router.get.bind(router);
const originalPost = router.post.bind(router);
const originalPut = router.put.bind(router);
const originalPatch = router.patch.bind(router);
const originalDelete = router.delete.bind(router);

// Override router methods to prepend base URL
router.visit = (url: any, options?: any) => {
    if (typeof url === 'string') {
        url = prependBaseUrl(url);
    }
    return originalVisit(url, options);
};

router.get = (url: any, data?: any, options?: any) => {
    if (typeof url === 'string') {
        url = prependBaseUrl(url);
    }
    return originalGet(url, data, options);
};

router.post = (url: any, data?: any, options?: any) => {
    if (typeof url === 'string') {
        url = prependBaseUrl(url);
    }
    return originalPost(url, data, options);
};

router.put = (url: any, data?: any, options?: any) => {
    if (typeof url === 'string') {
        url = prependBaseUrl(url);
    }
    return originalPut(url, data, options);
};

router.patch = (url: any, data?: any, options?: any) => {
    if (typeof url === 'string') {
        url = prependBaseUrl(url);
    }
    return originalPatch(url, data, options);
};

router.delete = (url: any, options?: any) => {
    if (typeof url === 'string') {
        url = prependBaseUrl(url);
    }
    return originalDelete(url, options);
};

// Create a wrapped Link component that prepends base URL
const AppLink = defineComponent({
    name: 'AppLink',
    inheritAttrs: false,
    props: {
        href: { type: String, required: true },
    },
    setup(props, { slots, attrs }) {
        const computedHref = computed(() => prependBaseUrl(props.href));
        return () => h(Link, { ...attrs, href: computedHref.value }, slots);
    },
});

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        // Configurar la URL base para las rutas usando app_url PRIMERO
        const appUrl = (props.initialPage.props as any).app_url || '';
        if (appUrl && !appUrl.includes('localhost') && !appUrl.includes('127.0.0.1')) {
            try {
                const url = new URL(appUrl);
                const basePath = url.pathname.endsWith('/') ? url.pathname.slice(0, -1) : url.pathname;
                setBaseUrl(basePath);
            } catch {
                setBaseUrl(appUrl);
            }
        }
        
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
        
        const app = createApp({ render: () => h(App, props) });
        
        // Register AppLink as 'Link' globally to override the default import
        app.component('Link', AppLink);
        
        app.use(plugin).mount(el);
    },
    progress: {
        color: '#4B5563',
    },
    id: 'app',
});

// This will set light / dark mode on page load...
initializeTheme();
