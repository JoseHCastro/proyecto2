import { usePage } from '@inertiajs/vue3';

/**
 * Extract the base path from APP_URL for subdirectory deployments
 */
export function getBasePath(): string {
    const page = usePage();
    const appUrl = (page.props.app_url as string) || '';
    
    if (!appUrl) return '';
    
    try {
        const url = new URL(appUrl);
        // Return the pathname without trailing slash
        return url.pathname.replace(/\/$/, '');
    } catch {
        // If it's not a valid URL, return empty
        return '';
    }
}

/**
 * Convert a relative path to full path with base prefix
 * Use this for all internal links in subdirectory deployments
 */
export function toUrl(path: string): string {
    const basePath = getBasePath();
    const cleanPath = path.startsWith('/') ? path : '/' + path;
    
    // If no base path, return the original path
    if (!basePath) return cleanPath;
    
    // Avoid double prefixing
    if (cleanPath.startsWith(basePath)) return cleanPath;
    
    return basePath + cleanPath;
}

/**
 * Get the full URL for a Wayfinder route
 * Prepends the APP_URL to handle subdirectory deployments
 * @deprecated Use toUrl() instead for simpler path handling
 */
export function useRouteUrl(path: string): string {
    return toUrl(path);
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
                url: toUrl(result.url),
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
