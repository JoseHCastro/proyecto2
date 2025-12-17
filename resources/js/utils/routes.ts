import { usePage } from '@inertiajs/vue3';

/**
 * Check if a URL is already absolute (starts with http:// or https://)
 */
function isAbsoluteUrl(url: string): boolean {
    return /^(https?:)?\/\//.test(url);
}

/**
 * Get the full URL for a Wayfinder route
 * Prepends the APP_URL to handle subdirectory deployments
 * If the URL is already absolute, returns just the path portion
 */
export function useRouteUrl(path: string): string {
    // If already absolute URL, extract just the path
    if (isAbsoluteUrl(path)) {
        try {
            // Handle malformed URLs like "//http://..."
            const cleanedPath = path.replace(/^\/\//, '');
            const url = new URL(cleanedPath);
            return url.pathname + url.search + url.hash;
        } catch {
            // If URL parsing fails, return as-is
            return path;
        }
    }
    
    // For relative paths, no need to prepend anything
    // Wayfinder should generate paths starting with /
    const cleanPath = path.startsWith('/') ? path : '/' + path;
    return cleanPath;
}

/**
 * Wrap a Wayfinder route function to normalize URLs
 * Ensures URLs are relative paths for proper subdirectory handling
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
