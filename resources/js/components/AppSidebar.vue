<script setup lang="ts">
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { 
    LayoutGrid, 
    Info, 
    Users, 
    CreditCard, 
    Clock, 
    Dumbbell,
    CalendarDays,
    Package,
    ClipboardList,
    FileCheck,
    QrCode,
    ScanLine
} from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { useRouteUrl } from '@/utils/routes';

// Importaciones de rutas Wayfinder
import { index as informacionIndex } from '@/routes/informacion';
import { index as usersIndex } from '@/routes/users';
import { index as membresiasIndex } from '@/routes/membresias';
import { index as horariosIndex } from '@/routes/horarios';
import { index as disciplinasIndex } from '@/routes/disciplinas';
import { index as sesionesIndex } from '@/routes/sesiones';
import { index as paquetesIndex } from '@/routes/paquetes';
import { index as rutinasIndex } from '@/routes/rutinas';
import { index as suscripcionesIndex } from '@/routes/suscripciones';
import { index as pagosQrIndex } from '@/routes/pagofacil';
import { miQr } from '@/routes/qr';
import { registrar as asistenciasRegistrar } from '@/routes/asistencias';

const page = usePage();
const userRoles = computed(() => (page.props.auth as any)?.roles || []);

// Helper para verificar roles
const hasRole = (role: string | string[]) => {
    if (Array.isArray(role)) {
        return role.some(r => userRoles.value.includes(r));
    }
    return userRoles.value.includes(role);
};

// Items de navegación filtrados por rol
const mainNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [];

    // Dashboard - Solo Propietario y Secretaria
    if (hasRole(['Propietario', 'Secretaria'])) {
        items.push({
            title: 'Dashboard',
            href: useRouteUrl('/dashboard'),
            icon: LayoutGrid,
        });
    }

    // Información - Solo Propietario
    if (hasRole('Propietario')) {
        items.push({
            title: 'Información',
            href: useRouteUrl(informacionIndex.url()),
            icon: Info,
        });
    }

    // Usuarios - Solo Propietario y Secretaria
    if (hasRole(['Propietario', 'Secretaria'])) {
        items.push({
            title: 'Usuarios',
            href: useRouteUrl(usersIndex.url()),
            icon: Users,
        });
    }

    // Membresías - Solo Propietario y Secretaria
    if (hasRole(['Propietario', 'Secretaria'])) {
        items.push({
            title: 'Membresías',
            href: useRouteUrl(membresiasIndex.url()),
            icon: CreditCard,
        });
    }

    // Horarios - Solo Propietario y Secretaria
    if (hasRole(['Propietario', 'Secretaria'])) {
        items.push({
            title: 'Horarios',
            href: useRouteUrl(horariosIndex.url()),
            icon: Clock,
        });
    }

    // Disciplinas - Solo Propietario y Secretaria
    if (hasRole(['Propietario', 'Secretaria'])) {
        items.push({
            title: 'Disciplinas',
            href: useRouteUrl(disciplinasIndex.url()),
            icon: Dumbbell,
        });
    }

    // Sesiones - Solo Propietario y Secretaria
    if (hasRole(['Propietario', 'Secretaria'])) {
        items.push({
            title: 'Sesiones',
            href: useRouteUrl(sesionesIndex.url()),
            icon: CalendarDays,
        });
    }

    // Paquetes - Todos (pero vista diferente para clientes)
    items.push({
        title: 'Paquetes',
        href: useRouteUrl(paquetesIndex.url()),
        icon: Package,
    });

    // Rutinas - Todos (pero vista diferente para clientes)
    items.push({
        title: 'Rutinas',
        href: useRouteUrl(rutinasIndex.url()),
        icon: ClipboardList,
    });

    // Suscripciones - Todos (pero vista diferente para clientes)
    items.push({
        title: 'Suscripciones',
        href: useRouteUrl(suscripcionesIndex.url()),
        icon: FileCheck,
    });

    // Pagos QR - Todos
    items.push({
        title: 'Pagos QR',
        href: useRouteUrl(pagosQrIndex.url()),
        icon: QrCode,
    });

    // Mi QR - Todos
    items.push({
        title: 'Mi QR',
        href: useRouteUrl(miQr.url()),
        icon: QrCode,
    });

    // Registrar Asistencia - Solo Propietario, Secretaria e Instructor
    if (hasRole(['Propietario', 'Secretaria', 'Instructor'])) {
        items.push({
            title: 'Registrar Asistencia',
            href: useRouteUrl(asistenciasRegistrar.url()),
            icon: ScanLine,
        });
    }

    return items;
});

const dashboardUrl = useRouteUrl('/dashboard');
const homeUrl = computed(() => {
    if (hasRole('Cliente')) return useRouteUrl(miQr.url());
    if (hasRole('Instructor')) return useRouteUrl(rutinasIndex.url());
    return dashboardUrl;
});
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="homeUrl">
                        <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
</template>
