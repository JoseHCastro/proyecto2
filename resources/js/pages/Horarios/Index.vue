<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Plus, Eye, Pencil, Trash2, Clock } from 'lucide-vue-next';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { confirmAlert, successAlert, errorAlert } from '@/composables/useSweetAlert';

const props = defineProps({
    horarios: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');

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
        router.get('/horarios', params, { replace: true, preserveScroll: true });
    }, 300);
});

const diasSemana = {
    1: 'Lun',
    2: 'Mar',
    3: 'Mié',
    4: 'Jue',
    5: 'Vie',
    6: 'Sáb'
};

const formatDias = (dias) => {
    if (!dias || !Array.isArray(dias)) return '-';
    return dias.sort((a, b) => a - b).map(d => diasSemana[d]).join(', ');
};

const deleteHorario = async (id) => {
    const result = await confirmAlert({
        title: '¿Eliminar horario?',
        text: 'Esta acción no se puede deshacer',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });

    if (result.isConfirmed) {
        router.delete(`/horarios/${id}`, {
            onSuccess: () => {
                successAlert({
                    title: '¡Eliminado!',
                    text: 'El horario ha sido eliminado correctamente'
                });
            },
            onError: () => {
                errorAlert({
                    title: 'Error',
                    text: 'No se pudo eliminar el horario'
                });
            }
        });
    }
};
</script>

<template>

    <Head title="Gestión de Horarios" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-foreground">
                Gestión de Horarios
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-background shadow-sm sm:rounded-lg border border-border">
                    <div class="p-6 text-foreground">
                        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                            <div class="flex gap-4 w-full md:w-auto">
                                <Input v-model="search" placeholder="Buscar por hora..."
                                    class="w-full md:w-64" />
                            </div>
                            <Button as-child>
                                <AppLink href="/horarios/create">
                                <Plus class="mr-2 h-4 w-4" />
                                Nuevo Horario
                                </AppLink>
                            </Button>
                        </div>

                        <div class="rounded-md border" v-if="horarios && horarios.data">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Días</TableHead>
                                        <TableHead>Hora Inicio</TableHead>
                                        <TableHead>Hora Fin</TableHead>
                                        <TableHead class="text-right">Acciones</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="horario in horarios.data" :key="horario.id">
                                        <TableCell class="font-medium">
                                            <div class="flex gap-1 flex-wrap">
                                                <Badge variant="outline" v-for="dia in horario.dia_semana" :key="dia">
                                                    {{ diasSemana[dia] }}
                                                </Badge>
                                            </div>
                                        </TableCell>
                                        <TableCell>
                                            <div class="flex items-center gap-2">
                                                <Clock class="h-4 w-4 text-muted-foreground" />
                                                {{ horario.hora_inicio.substring(0, 5) }}
                                            </div>
                                        </TableCell>
                                        <TableCell>
                                            <div class="flex items-center gap-2">
                                                <Clock class="h-4 w-4 text-muted-foreground" />
                                                {{ horario.hora_fin.substring(0, 5) }}
                                            </div>
                                        </TableCell>
                                        <TableCell class="text-right">
                                            <div class="flex justify-end gap-2">
                                                <Button variant="outline" size="sm" as-child title="Ver">
                                                    <Link :href="`/horarios/${horario.id}`">
                                                    <Eye class="h-4 w-4" />
                                                    </AppLink>
                                                </Button>
                                                <Button variant="outline" size="sm" as-child title="Editar">
                                                    <Link :href="`/horarios/${horario.id}/edit`">
                                                    <Pencil class="h-4 w-4" />
                                                    </AppLink>
                                                </Button>
                                                <Button variant="destructive" size="sm" @click="deleteHorario(horario.id)"
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
                        <div class="mt-4 flex justify-center" v-if="horarios && horarios.links && horarios.links.length > 3">
                            <div class="flex gap-1">
                                <Link v-for="(link, k) in horarios.links" :key="k" :href="link.url || '#'"
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
