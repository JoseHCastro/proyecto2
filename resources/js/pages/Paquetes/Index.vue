<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
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
    paquetes: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');

watch(
    search,
    debounce((value) => {
        router.get('/paquetes', { search: value }, { preserveState: true });
    }, 300)
);

const deletePaquete = async (id, nombre) => {
    const result = await confirmAlert({
        title: '¿Eliminar paquete?',
        text: `Se eliminará el paquete "${nombre}"`,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
    });

    if (result.isConfirmed) {
        router.delete(`/paquetes/${id}`, {
            onSuccess: () => {
                successAlert({
                    title: '¡Eliminado!',
                    text: 'El paquete ha sido eliminado correctamente',
                });
            },
        });
    }
};
</script>

<template>

    <Head title="Paquetes" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-foreground">
                Gestionar Paquetes
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
                                    placeholder="Buscar paquetes..."
                                    class="pl-8"
                                />
                            </div>
                            <Button as-child>
                                <Link href="/paquetes/create">
                                    <Plus class="mr-2 h-4 w-4" />
                                    Nuevo Paquete
                                </Link>
                            </Button>
                        </div>

                        <div class="overflow-x-auto">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Nombre</TableHead>
                                        <TableHead>Membresía</TableHead>
                                        <TableHead>Sesiones</TableHead>
                                        <TableHead>Precio</TableHead>
                                        <TableHead>Estado</TableHead>
                                        <TableHead class="text-right">Acciones</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-if="paquetes.data.length === 0">
                                        <TableCell colspan="6" class="text-center">
                                            No hay paquetes registrados
                                        </TableCell>
                                    </TableRow>
                                    <TableRow v-for="paquete in paquetes.data" :key="paquete.id">
                                        <TableCell class="font-medium">{{ paquete.nombre }}</TableCell>
                                        <TableCell>{{ paquete.membresia.nombre }}</TableCell>
                                        <TableCell>
                                            <div class="flex gap-1 flex-wrap max-w-xs">
                                                <Badge v-for="sesion in paquete.sesiones.slice(0, 3)" :key="sesion.id" variant="secondary" class="text-xs">
                                                    {{ sesion.disciplina.nombre }}
                                                </Badge>
                                                <Badge v-if="paquete.sesiones.length > 3" variant="outline" class="text-xs">
                                                    +{{ paquete.sesiones.length - 3 }}
                                                </Badge>
                                            </div>
                                        </TableCell>
                                        <TableCell>Bs {{ Number(paquete.precio).toFixed(2) }}</TableCell>
                                        <TableCell>
                                            <Badge :variant="paquete.activo ? 'default' : 'secondary'">
                                                {{ paquete.activo ? 'Activo' : 'Inactivo' }}
                                            </Badge>
                                        </TableCell>
                                        <TableCell class="text-right">
                                            <div class="flex justify-end gap-2">
                                                <Button variant="ghost" size="icon" as-child>
                                                    <Link :href="`/paquetes/${paquete.id}`">
                                                        <Eye class="h-4 w-4" />
                                                    </Link>
                                                </Button>
                                                <Button variant="ghost" size="icon" as-child>
                                                    <Link :href="`/paquetes/${paquete.id}/edit`">
                                                        <Pencil class="h-4 w-4" />
                                                    </Link>
                                                </Button>
                                                <Button
                                                    variant="ghost"
                                                    size="icon"
                                                    @click="deletePaquete(paquete.id, paquete.nombre)"
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
                        <div class="mt-4 flex justify-center" v-if="paquetes && paquetes.links && paquetes.links.length > 3">
                            <div class="flex gap-1">
                                <Link v-for="(link, k) in paquetes.links" :key="k" :href="link.url || '#'"
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
