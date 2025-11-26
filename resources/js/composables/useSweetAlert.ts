import Swal, { type SweetAlertOptions } from 'sweetalert2';

// Función para obtener el tema actual
const getCurrentTheme = (): 'kids' | 'teens' | 'adults' => {
    const theme = document.documentElement.getAttribute('data-theme');
    if (theme === 'kids' || theme === 'teens' || theme === 'adults') {
        return theme;
    }
    return 'teens';
};

// Configuración de temas para SweetAlert2
const themeConfigs = {
    kids: {
        confirmButtonColor: '#4DA3FF',
        cancelButtonColor: '#A07CFF',
        background: '#F0F8FF',
        color: '#1E3A8A',
        iconColor: '#4DA3FF',
        customClass: {
            popup: 'swal-kids-popup',
            confirmButton: 'swal-kids-confirm',
            cancelButton: 'swal-kids-cancel',
        }
    },
    teens: {
        confirmButtonColor: '#5BE37D',
        cancelButtonColor: '#2F353B',
        background: '#FFFFFF',
        color: '#1F2937',
        iconColor: '#5BE37D',
        customClass: {
            popup: 'swal-teens-popup',
            confirmButton: 'swal-teens-confirm',
            cancelButton: 'swal-teens-cancel',
        }
    },
    adults: {
        confirmButtonColor: '#B71C1C',
        cancelButtonColor: '#6B7280',
        background: '#FFFFFF',
        color: '#1C1C1C',
        iconColor: '#B71C1C',
        customClass: {
            popup: 'swal-adults-popup',
            confirmButton: 'swal-adults-confirm',
            cancelButton: 'swal-adults-cancel',
        }
    }
};

// Función para obtener la configuración del tema actual
const getThemeConfig = () => {
    const theme = getCurrentTheme();
    return themeConfigs[theme];
};

// Alerta de confirmación
export const confirmAlert = async (options: SweetAlertOptions = {}) => {
    const themeConfig = getThemeConfig();

    return await Swal.fire({
        title: options.title || '¿Estás seguro?',
        text: options.text || 'Esta acción no se puede deshacer',
        icon: options.icon || 'warning',
        showCancelButton: true,
        confirmButtonText: options.confirmButtonText || 'Sí, continuar',
        cancelButtonText: options.cancelButtonText || 'Cancelar',
        reverseButtons: true,
        ...themeConfig,
        ...options
    });
};

// Alerta de éxito
export const successAlert = async (options: SweetAlertOptions = {}) => {
    const themeConfig = getThemeConfig();

    return await Swal.fire({
        title: options.title || '¡Éxito!',
        text: options.text || 'La operación se completó correctamente',
        icon: 'success',
        confirmButtonText: options.confirmButtonText || 'Aceptar',
        ...themeConfig,
        ...options
    });
};

// Alerta de error
export const errorAlert = async (options: SweetAlertOptions = {}) => {
    const themeConfig = getThemeConfig();

    return await Swal.fire({
        title: options.title || 'Error',
        text: options.text || 'Ocurrió un error al procesar la solicitud',
        icon: 'error',
        confirmButtonText: options.confirmButtonText || 'Aceptar',
        ...themeConfig,
        ...options
    });
};

// Alerta de información
export const infoAlert = async (options: SweetAlertOptions = {}) => {
    const themeConfig = getThemeConfig();

    return await Swal.fire({
        title: options.title || 'Información',
        text: options.text || '',
        icon: 'info',
        confirmButtonText: options.confirmButtonText || 'Aceptar',
        ...themeConfig,
        ...options
    });
};

// Toast notification
export const toast = (options: SweetAlertOptions = {}) => {
    const themeConfig = getThemeConfig();

    const Toast = Swal.mixin({
        toast: true,
        position: (options.position as any) || 'top-end',
        showConfirmButton: false,
        timer: options.timer || 3000,
        timerProgressBar: true,
        ...themeConfig,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    return Toast.fire({
        icon: options.icon || 'success',
        title: options.title || 'Operación exitosa',
        ...options
    });
};
