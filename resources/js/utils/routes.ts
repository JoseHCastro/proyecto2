import { usePage } from '@inertiajs/vue3';

/**
 * Wrapper para las rutas de Wayfinder que agrega el prefijo de APP_URL
 */
export function useRoute() {
    const page = usePage();
    const appUrl = (page.props.app_url as string) || '';

    return (routeFn: (...args: any[]) => any) => {
        return (...args: any[]) => {
            const result = routeFn(...args);

            // Si es un objeto con url, agregar el prefijo
            if (typeof result === 'object' && result.url) {
                return {
                    ...result,
                    url: appUrl + result.url
                };
            }

            // Si es un string (solo URL), agregar el prefijo
            if (typeof result === 'string') {
                return appUrl + result;
            }

            return result;
        };
    };
}
