<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Plus, Search, Eye, Pencil, Trash2, Dumbbell, RotateCcw, Repeat } from 'lucide-vue-next';
import { confirmAlert, successAlert } from '@/composables/useSweetAlert';
import { index as rutinasIndex, create as rutinasCreate, show as rutinasShow, edit as rutinasEdit, destroy as rutinasDestroy } from '@/routes/rutinas';

const props = defineProps({
    rutinas: Object,
    filters: Object,
    isCliente: {
        type: Boolean,
        default: false,
    },
});

const search = ref(props.filters?.search || '');

watch(
    search,
    debounce((value) => {
        router.get(rutinasIndex.url(), { search: value }, { preserveState: true });
    }, 300)
);

const deleteRutina = async (id, ejercicio) => {
    const result = await confirmAlert({
        title: '¿Eliminar rutina?',
        text: `Se eliminará la rutina "${ejercicio}"`,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
    });

    if (result.isConfirmed) {
        router.delete(rutinasDestroy.url({ rutina: id }), {
            onSuccess: () => {
                successAlert({
                    title: '¡Eliminado!',
                    text: 'La rutina ha sido eliminada correctamente',
                });
            },
        });
    }
};
</script>

<template>

    <Head title="Rutinas" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-foreground">
                {{ isCliente ? 'Mis Rutinas' : 'Gestionar Rutinas' }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Vista Cliente: Cards -->
                <template v-if="isCliente">
                    <div v-if="rutinas.data.length === 0" class="text-center py-12">
                        <Dumbbell class="mx-auto h-16 w-16 text-muted-foreground mb-4" />
                        <p class="text-lg text-muted-foreground">No tienes rutinas asignadas todavía</p>
                        <p class="text-sm text-muted-foreground mt-2">Contacta a tu instructor para comenzar</p>
                    </div>
                    
                    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <Card 
                            v-for="rutina in rutinas.data" 
                            :key="rutina.id"
                            class="group hover:shadow-lg transition-all duration-300 hover:scale-[1.02] border-2 hover:border-primary/50"
                        >
                            <CardHeader class="pb-3">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 rounded-lg bg-primary/10 text-primary">
                                            <Dumbbell class="h-6 w-6" />
                                        </div>
                                        <div>
                                            <CardTitle class="text-lg">{{ rutina.ejercicio }}</CardTitle>
                                            <CardDescription class="text-xs mt-1">
                                                Instructor: {{ rutina.instructor?.name }}
                                            </CardDescription>
                                        </div>
                                    </div>
                                </div>
                            </CardHeader>
                            <CardContent>
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div class="flex items-center gap-2 p-3 rounded-lg bg-muted/50">
                                        <RotateCcw class="h-5 w-5 text-primary" />
                                        <div>
                                            <p class="text-2xl font-bold text-primary">{{ rutina.series }}</p>
                                            <p class="text-xs text-muted-foreground">Series</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 p-3 rounded-lg bg-muted/50">
                                        <Repeat class="h-5 w-5 text-primary" />
                                        <div>
                                            <p class="text-2xl font-bold text-primary">{{ rutina.repeticiones }}</p>
                                            <p class="text-xs text-muted-foreground">Repeticiones</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div v-if="rutina.descripcion" class="mb-4 p-3 rounded-lg bg-muted/30">
                                    <p class="text-sm text-muted-foreground">{{ rutina.descripcion }}</p>
                                </div>
                                
                                <div class="flex items-center justify-between text-xs text-muted-foreground">
                                    <span>Creada: {{ new Date(rutina.creada_en).toLocaleDateString('es-ES') }}</span>
                                    <Button variant="ghost" size="sm" as-child>
                                        <Link :href="rutinasShow.url({ rutina: rutina.id })">
                                            <Eye class="h-4 w-4 mr-1" />
                                            Ver más
                                        </Link>
                                    </Button>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                    
                    <!-- Pagination para Cliente -->
                    <div class="mt-6 flex justify-center" v-if="rutinas && rutinas.links && rutinas.links.length > 3">
                        <div class="flex gap-1">
                            <Link v-for="(link, k) in rutinas.links" :key="k" :href="link.url || '#'"
                                v-html="link.label" class="px-3 py-1 border rounded text-sm"
                                :class="{ 'bg-primary text-primary-foreground': link.active, 'opacity-50 cursor-not-allowed': !link.url }" />
                        </div>
                    </div>
                </template>

                <!-- Vista Admin/Instructor: Tabla -->
                <template v-else>
                    <div class="overflow-hidden bg-card shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="mb-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                <div class="relative w-full sm:w-64">
                                    <Search class="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
                                    <Input
                                        v-model="search"
                                        placeholder="Buscar rutinas..."
                                        class="pl-8"
                                    />
                                </div>
                                <Button as-child>
                                    <Link :href="rutinasCreate.url()">
                                        <Plus class="mr-2 h-4 w-4" />
                                        Nueva Rutina
                                    </Link>
                                </Button>
                            </div>

                            <div class="overflow-x-auto">
                                <Table>
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead>Ejercicio</TableHead>
                                            <TableHead>Socio</TableHead>
                                            <TableHead>Instructor</TableHead>
                                            <TableHead>Series</TableHead>
                                            <TableHead>Repeticiones</TableHead>
                                            <TableHead>Fecha</TableHead>
                                            <TableHead class="text-right">Acciones</TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        <TableRow v-if="rutinas.data.length === 0">
                                            <TableCell colspan="7" class="text-center">
                                                No hay rutinas registradas
                                            </TableCell>
                                        </TableRow>
                                        <TableRow v-for="rutina in rutinas.data" :key="rutina.id">
                                            <TableCell class="font-medium">{{ rutina.ejercicio }}</TableCell>
                                            <TableCell>{{ rutina.socio.name }}</TableCell>
                                            <TableCell>{{ rutina.instructor.name }}</TableCell>
                                            <TableCell>{{ rutina.series }}</TableCell>
                                            <TableCell>{{ rutina.repeticiones }}</TableCell>
                                            <TableCell>{{ new Date(rutina.creada_en).toLocaleDateString('es-ES') }}</TableCell>
                                            <TableCell class="text-right">
                                                <div class="flex justify-end gap-2">
                                                    <Button variant="ghost" size="icon" as-child>
                                                        <Link :href="rutinasShow.url({ rutina: rutina.id })">
                                                            <Eye class="h-4 w-4" />
                                                        </Link>
                                                    </Button>
                                                    <Button variant="ghost" size="icon" as-child>
                                                        <Link :href="rutinasEdit.url({ rutina: rutina.id })">
                                                            <Pencil class="h-4 w-4" />
                                                        </Link>
                                                    </Button>
                                                    <Button
                                                        variant="ghost"
                                                        size="icon"
                                                        @click="deleteRutina(rutina.id, rutina.ejercicio)"
                                                    >
                                                        <Trash2 class="h-4 w-4" />
                                                    </Button>
                                                </div>
                                            </TableCell>
                                        </TableRow>
                                    </TableBody>
                                </Table>
                            </div>

                            <!-- Pagination -->
                            <div class="mt-4 flex justify-center" v-if="rutinas && rutinas.links && rutinas.links.length > 3">
                                <div class="flex gap-1">
                                    <Link v-for="(link, k) in rutinas.links" :key="k" :href="link.url || '#'"
                                        v-html="link.label" class="px-3 py-1 border rounded text-sm"
                                        :class="{ 'bg-primary text-primary-foreground': link.active, 'opacity-50 cursor-not-allowed': !link.url }" />
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </AppLayout>

</template>
