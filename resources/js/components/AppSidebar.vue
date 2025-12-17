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
import { Link } from '@inertiajs/vue3';
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

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: useRouteUrl('/dashboard'),
        icon: LayoutGrid,
    },
    {
        title: 'Información',
        href: useRouteUrl(informacionIndex.url()),
        icon: Info,
    },
    {
        title: 'Usuarios',
        href: useRouteUrl(usersIndex.url()),
        icon: Users,
    },
    {
        title: 'Membresías',
        href: useRouteUrl(membresiasIndex.url()),
        icon: CreditCard,
    },
    {
        title: 'Horarios',
        href: useRouteUrl(horariosIndex.url()),
        icon: Clock,
    },
    {
        title: 'Disciplinas',
        href: useRouteUrl(disciplinasIndex.url()),
        icon: Dumbbell,
    },
    {
        title: 'Sesiones',
        href: useRouteUrl(sesionesIndex.url()),
        icon: CalendarDays,
    },
    {
        title: 'Paquetes',
        href: useRouteUrl(paquetesIndex.url()),
        icon: Package,
    },
    {
        title: 'Rutinas',
        href: useRouteUrl(rutinasIndex.url()),
        icon: ClipboardList,
    },
    {
        title: 'Suscripciones',
        href: useRouteUrl(suscripcionesIndex.url()),
        icon: FileCheck,
    },
    {
        title: 'Pagos QR',
        href: useRouteUrl(pagosQrIndex.url()),
        icon: QrCode,
    },
    {
        title: 'Mi QR',
        href: useRouteUrl(miQr.url()),
        icon: QrCode,
    },
    {
        title: 'Registrar Asistencia',
        href: useRouteUrl(asistenciasRegistrar.url()),
        icon: ScanLine,
    },
];

const dashboardUrl = useRouteUrl('/dashboard');
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboardUrl">
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
