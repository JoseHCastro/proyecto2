// Utilidad para manejar la URL base en subdirectorios
let baseUrl: string = '';

export const setBaseUrl = (url: string) => {
    baseUrl = url.endsWith('/') ? url.slice(0, -1) : url;
};

export const getBaseUrl = (): string => {
    return baseUrl;
};

export const withBaseUrl = (path: string): string => {
    if (!baseUrl) return path;
    return `${baseUrl}${path}`;
};
