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
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Información',
        href: informacionIndex.url(),
        icon: Info,
    },
    {
        title: 'Usuarios',
        href: usersIndex.url(),
        icon: Users,
    },
    {
        title: 'Membresías',
        href: membresiasIndex.url(),
        icon: CreditCard,
    },
    {
        title: 'Horarios',
        href: horariosIndex.url(),
        icon: Clock,
    },
    {
        title: 'Disciplinas',
        href: disciplinasIndex.url(),
        icon: Dumbbell,
    },
    {
        title: 'Sesiones',
        href: sesionesIndex.url(),
        icon: CalendarDays,
    },
    {
        title: 'Paquetes',
        href: paquetesIndex.url(),
        icon: Package,
    },
    {
        title: 'Rutinas',
        href: rutinasIndex.url(),
        icon: ClipboardList,
    },
    {
        title: 'Suscripciones',
        href: suscripcionesIndex.url(),
        icon: FileCheck,
    },
    {
        title: 'Pagos QR',
        href: pagosQrIndex.url(),
        icon: QrCode,
    },
    {
        title: 'Mi QR',
        href: miQr.url(),
        icon: QrCode,
    },
    {
        title: 'Registrar Asistencia',
        href: asistenciasRegistrar.url(),
        icon: ScanLine,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link href="/dashboard">
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
