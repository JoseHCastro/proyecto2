import { usePage } from '@inertiajs/vue3';

/**
 * Get the base path from APP_URL for subdirectory deployments
 * Extracts just the path portion (e.g., "/inf513/grupo23sc/proyecto2")
 */
export function getBasePath(): string {
    const page = usePage();
    const appUrl = (page.props.app_url as string) || '';
    
    if (!appUrl) return '';
    
    try {
        const url = new URL(appUrl);
        // Return the pathname, removing trailing slash
        return url.pathname.replace(/\/$/, '');
    } catch {
        return '';
    }
}

/**
 * Get the home URL (base path or "/" if no subdirectory)
 */
export function getHomeUrl(): string {
    const basePath = getBasePath();
    return basePath || '/';
}

/**
 * Check if a URL is already absolute (starts with http:// or https://)
 */
function isAbsoluteUrl(url: string): boolean {
    return /^(https?:)?\/\//.test(url);
}

/**
 * Get the full URL for a Wayfinder route
 * Prepends the base path from APP_URL to handle subdirectory deployments
 */
export function useRouteUrl(path: string): string {
    // If already absolute URL, extract just the path
    if (isAbsoluteUrl(path)) {
        try {
            const cleanedPath = path.replace(/^\/\//, '');
            const url = new URL(cleanedPath);
            // Return base path + the URL's path
            return getBasePath() + url.pathname + url.search + url.hash;
        } catch {
            return path;
        }
    }
    
    // For relative paths, prepend base path
    const basePath = getBasePath();
    const cleanPath = path.startsWith('/') ? path : '/' + path;
    
    return basePath + cleanPath;
}

/**
 * Wrap a Wayfinder route function to normalize URLs
 * Ensures URLs include the correct base path for subdirectory deployments
 */
export function wrapRoute<T extends (...args: any[]) => any>(
    routeFn: T
): T {
    const wrapped = ((...args: any[]) => {
        const result = routeFn(...args);
        if (typeof result === 'object') {
            const newResult = { ...result };
            // Handle regular route definitions with 'url'
            if (result.url) {
                newResult.url = useRouteUrl(result.url);
            }
            // Handle form definitions with 'action'
            if (result.action) {
                newResult.action = useRouteUrl(result.action);
            }
            return newResult;
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
