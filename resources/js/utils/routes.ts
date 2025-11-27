import { usePage } from '@inertiajs/vue3';

/**
 * Get the full URL for a Wayfinder route
 * Prepends the APP_URL to handle subdirectory deployments
 */
export function useRouteUrl(path: string): string {
    const page = usePage();
    const appUrl = (page.props.app_url as string) || '';

    // Remove trailing slash from appUrl and leading slash from path if both exist
    const cleanAppUrl = appUrl.replace(/\/$/, '');
    const cleanPath = path.startsWith('/') ? path : '/' + path;

    return cleanAppUrl + cleanPath;
}

/**
 * Wrap a Wayfinder route function to use full URLs
 */
export function wrapRoute<T extends (...args: any[]) => any>(
    routeFn: T
): T {
    const wrapped = ((...args: any[]) => {
        const result = routeFn(...args);
        if (typeof result === 'object' && result.url) {
            return {
                ...result,
                url: useRouteUrl(result.url),
            };
        }
        return result;
    }) as T;

    // Copy all properties from the original function to the wrapped one
    Object.keys(routeFn).forEach(key => {
        const value = (routeFn as any)[key];
        if (typeof value === 'function') {
            (wrapped as any)[key] = wrapRoute(value);
        } else {
            (wrapped as any)[key] = value;
        }
    });

    return wrapped;
}
