import { InertiaLinkProps } from '@inertiajs/vue3';
import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';
import { toUrl as toUrlWithBase } from '@/utils/routes';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function urlIsActive(
    urlToCheck: NonNullable<InertiaLinkProps['href']>,
    currentUrl: string,
) {
    const url = toUrl(urlToCheck);
    // Compare paths without the base prefix for active state checking
    return url === currentUrl || currentUrl.endsWith(url);
}

export function toUrl(href: NonNullable<InertiaLinkProps['href']>): string {
    const path = typeof href === 'string' ? href : href?.url || '';
    // Apply base URL prefix for subdirectory deployments
    return toUrlWithBase(path);
}
