<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
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
import { BookOpen, Folder, LayoutGrid } from 'lucide-vue-next';
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
        icon: Folder,
    },
    {
        title: 'Usuarios',
        href: '/users',
        icon: Folder,
    },
    {
        title: 'Membresías',
        href: '/membresias',
        icon: Folder,
    },
    {
        title: 'Horarios',
        href: '/horarios',
        icon: Folder,
    },
    {
        title: 'Disciplinas',
        href: '/disciplinas',
        icon: Folder,
    },
    {
        title: 'Sesiones',
        href: '/sesiones',
        icon: Folder,
    },
    {
        title: 'Paquetes',
        href: '/paquetes',
        icon: Folder,
    },
    {
        title: 'Rutinas',
        href: '/rutinas',
        icon: Folder,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
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
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
</template>
