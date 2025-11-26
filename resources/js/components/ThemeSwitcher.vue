<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Moon, Sun, Monitor, Palette } from 'lucide-vue-next';
import { useAppearance } from '@/composables/useAppearance';

const { appearance, updateAppearance } = useAppearance();

const theme = ref('teens');

const themes = [
    { value: 'kids', label: 'Niños (Kids)' },
    { value: 'teens', label: 'Jóvenes (Teens)' },
    { value: 'adults', label: 'Adultos (Adults)' },
];

const modes = [
    { value: 'light', label: 'Día', icon: Sun },
    { value: 'dark', label: 'Noche', icon: Moon },
    { value: 'system', label: 'Automático', icon: Monitor },
];

const applyTheme = () => {
    document.documentElement.setAttribute('data-theme', theme.value);
    localStorage.setItem('app-theme', theme.value);
    document.cookie = `app-theme=${theme.value};path=/;max-age=31536000;SameSite=Lax`;
};

watch(theme, applyTheme);

onMounted(() => {
    const savedTheme = localStorage.getItem('app-theme');
    if (savedTheme && themes.some(t => t.value === savedTheme)) {
        theme.value = savedTheme;
    }
    applyTheme();
});
</script>

<template>
    <div class="flex items-center gap-2">
        <DropdownMenu>
            <DropdownMenuTrigger as-child>
                <Button variant="ghost" size="icon" class="h-8 w-8" title="Cambiar Tema">
                    <Palette class="h-4 w-4" />
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
                <DropdownMenuItem v-for="t in themes" :key="t.value" @click="theme = t.value">
                    {{ t.label }}
                </DropdownMenuItem>
            </DropdownMenuContent>
        </DropdownMenu>

        <DropdownMenu>
            <DropdownMenuTrigger as-child>
                <Button variant="ghost" size="icon" class="h-8 w-8" title="Modo Día/Noche">
                    <component :is="modes.find(m => m.value === appearance)?.icon" class="h-4 w-4" />
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
                <DropdownMenuItem v-for="m in modes" :key="m.value" @click="updateAppearance(m.value)">
                    <component :is="m.icon" class="mr-2 h-4 w-4" />
                    {{ m.label }}
                </DropdownMenuItem>
            </DropdownMenuContent>
        </DropdownMenu>
    </div>
</template>
