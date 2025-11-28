<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Plus, Search, Eye, Pencil, Trash2 } from 'lucide-vue-next';
import { confirmAlert, successAlert } from '@/composables/useSweetAlert';

const props = defineProps({
    rutinas: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');

watch(
    search,
    debounce((value) => {
        router.get('/rutinas', { search: value }, { preserveState: true });
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
        router.delete(`/rutinas/${id}`, {
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
                Gestionar Rutinas
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
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
                                <AppLink href="/rutinas/create">
                                    <Plus class="mr-2 h-4 w-4" />
                                    Nueva Rutina
                                </AppLink>
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
                                                    <Link :href="`/rutinas/${rutina.id}`">
                                                        <Eye class="h-4 w-4" />
                                                    </AppLink>
                                                </Button>
                                                <Button variant="ghost" size="icon" as-child>
                                                    <Link :href="`/rutinas/${rutina.id}/edit`">
                                                        <Pencil class="h-4 w-4" />
                                                    </AppLink>
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
            </div>
        </div>
    </AppLayout>

</template>
