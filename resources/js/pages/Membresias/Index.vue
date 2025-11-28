<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Plus, Eye, Pencil, Trash2 } from 'lucide-vue-next';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';
import { confirmAlert, successAlert, errorAlert } from '@/composables/useSweetAlert';

const props = defineProps({
    membresias: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const activo = ref(props.filters?.activo || '');

let searchTimeout = null;

watch(search, (value) => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }

    searchTimeout = setTimeout(() => {
        const params = {};
        if (value) {
            params.search = value;
        }
        if (activo.value) {
            params.activo = activo.value;
        }
        router.get('/membresias', params, { replace: true, preserveScroll: true });
    }, 300);
});

watch(activo, (value) => {
    const params = {};
    if (search.value) {
        params.search = search.value;
    }
    if (value) {
        params.activo = value;
    }
    router.get('/membresias', params, { replace: true, preserveScroll: true });
});

const deleteMembresia = async (id) => {
    const result = await confirmAlert({
        title: '¿Eliminar membresía?',
        text: 'Esta acción no se puede deshacer',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });

    if (result.isConfirmed) {
        router.delete(`/membresias/${id}`, {
            onSuccess: () => {
                successAlert({
                    title: '¡Eliminado!',
                    text: 'La membresía ha sido eliminada correctamente'
                });
            },
            onError: () => {
                errorAlert({
                    title: 'Error',
                    text: 'No se pudo eliminar la membresía'
                });
            }
        });
    }
};
</script>

<template>

    <Head title="Gestión de Membresías" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-foreground">
                Gestión de Membresías
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-background shadow-sm sm:rounded-lg border border-border">
                    <div class="p-6 text-foreground">
                        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                            <div class="flex gap-4 w-full md:w-auto">
                                <Input v-model="search" placeholder="Buscar por nombre o descripción..."
                                    class="w-full md:w-64" />
                                <Select v-model="activo">
                                    <SelectTrigger class="w-[180px]">
                                        <SelectValue placeholder="Filtrar por estado" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="">Todos</SelectItem>
                                        <SelectItem value="true">Activos</SelectItem>
                                        <SelectItem value="false">Inactivos</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <Button as-child>
                                <AppLink href="/membresias/create">
                                <Plus class="mr-2 h-4 w-4" />
                                Nueva Membresía
                                </AppLink>
                            </Button>
                        </div>

                        <div class="rounded-md border" v-if="membresias && membresias.data">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Nombre</TableHead>
                                        <TableHead>Descripción</TableHead>
                                        <TableHead>Duración (días)</TableHead>
                                        <TableHead>Estado</TableHead>
                                        <TableHead class="text-right">Acciones</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="membresia in membresias.data" :key="membresia.id">
                                        <TableCell class="font-medium">{{ membresia.nombre }}</TableCell>
                                        <TableCell>{{ membresia.descripcion || '-' }}</TableCell>
                                        <TableCell>{{ membresia.duracion_dias }}</TableCell>
                                        <TableCell>
                                            <Badge :variant="membresia.activo ? 'default' : 'destructive'">
                                                {{ membresia.activo ? 'Activo' : 'Inactivo' }}
                                            </Badge>
                                        </TableCell>
                                        <TableCell class="text-right">
                                            <div class="flex justify-end gap-2">
                                                <Button variant="outline" size="sm" as-child title="Ver">
                                                    <Link :href="`/membresias/${membresia.id}`">
                                                    <Eye class="h-4 w-4" />
                                                    </AppLink>
                                                </Button>
                                                <Button variant="outline" size="sm" as-child title="Editar">
                                                    <Link :href="`/membresias/${membresia.id}/edit`">
                                                    <Pencil class="h-4 w-4" />
                                                    </AppLink>
                                                </Button>
                                                <Button variant="destructive" size="sm" @click="deleteMembresia(membresia.id)"
                                                    title="Eliminar">
                                                    <Trash2 class="h-4 w-4" />
                                                </Button>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4 flex justify-center" v-if="membresias && membresias.links && membresias.links.length > 3">
                            <div class="flex gap-1">
                                <Link v-for="(link, k) in membresias.links" :key="k" :href="link.url || '#'"
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
