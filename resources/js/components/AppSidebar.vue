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

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Información',
        href: '/informacion',
        icon: Info,
    },
    {
        title: 'Usuarios',
        href: '/users',
        icon: Users,
    },
    {
        title: 'Membresías',
        href: '/membresias',
        icon: CreditCard,
    },
    {
        title: 'Horarios',
        href: '/horarios',
        icon: Clock,
    },
    {
        title: 'Disciplinas',
        href: '/disciplinas',
        icon: Dumbbell,
    },
    {
        title: 'Sesiones',
        href: '/sesiones',
        icon: CalendarDays,
    },
    {
        title: 'Paquetes',
        href: '/paquetes',
        icon: Package,
    },
    {
        title: 'Rutinas',
        href: '/rutinas',
        icon: ClipboardList,
    },
    {
        title: 'Suscripciones',
        href: '/suscripciones',
        icon: FileCheck,
    },
    {
        title: 'Pagos QR',
        href: '/pagos/qr',
        icon: QrCode,
    },
    {
        title: 'Mi QR',
        href: '/mi-qr',
        icon: QrCode,
    },
    {
        title: 'Registrar Asistencia',
        href: '/asistencias/registrar',
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
