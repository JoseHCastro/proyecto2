<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const pageViews = computed(() => page.props.page_views);
const currentYear = new Date().getFullYear();
</script>

<template>
    <footer class="bg-muted/50 border-t py-6 mt-auto">
        <div
            class="container mx-auto px-4 flex flex-col md:flex-row justify-between items-center text-sm text-muted-foreground">
            <div class="mb-4 md:mb-0">
                &copy; {{ currentYear }} Todos los derechos reservados.
            </div>

            <div class="flex items-center space-x-4">
                <div v-if="pageViews !== undefined"
                    class="flex items-center bg-background border rounded-full px-3 py-1 text-xs shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-green-500 mr-2 animate-pulse"></span>
                    <span>Vistas de esta página: <strong>{{ pageViews }}</strong></span>
                </div>

                <div class="flex space-x-4">
                    <a href="#" class="hover:text-foreground transition-colors">Términos</a>
                    <a href="#" class="hover:text-foreground transition-colors">Privacidad</a>
                </div>
            </div>
        </div>
    </footer>
</template>
