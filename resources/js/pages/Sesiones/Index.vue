<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
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
import { Eye, Pencil, Trash2, Plus, Clock } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { confirmAlert, successAlert, errorAlert } from '@/composables/useSweetAlert';

const props = defineProps({
    sesiones: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');

watch(search, (value) => {
    router.get('/sesiones', { search: value }, {
        preserveState: true,
        replace: true,
    });
});

const deleteSesion = (id) => {
    confirmAlert({
        title: '¿Estás seguro?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(`/sesiones/${id}`, {
                onSuccess: () => {
                    successAlert({
                        title: '¡Eliminado!',
                        text: 'La sesión ha sido eliminada correctamente'
                    });
                },
                onError: () => {
                    errorAlert({
                        title: 'Error',
                        text: 'No se pudo eliminar la sesión'
                    });
                }
            });
        }
    });
};

const diasSemana = {
    1: 'Lun',
    2: 'Mar',
    3: 'Mié',
    4: 'Jue',
    5: 'Vie',
    6: 'Sáb',
};
</script>

<template>

    <Head title="Gestión de Sesiones" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-foreground">
                Gestión de Sesiones
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-background shadow-sm sm:rounded-lg border border-border">
                    <div class="p-6 text-foreground">
                        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                            <div class="flex gap-4 w-full md:w-auto">
                                <Input v-model="search" placeholder="Buscar sesiones..."
                                    class="w-full md:w-64" />
                            </div>
                            <Button as-child>
                                <Link href="/sesiones/create">
                                <Plus class="mr-2 h-4 w-4" />
                                Nueva Sesión
                                </Link>
                            </Button>
                        </div>

                        <div class="rounded-md border" v-if="sesiones && sesiones.data && sesiones.data.length > 0">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Disciplina</TableHead>
                                        <TableHead>Horario</TableHead>
                                        <TableHead>Instructor</TableHead>
                                        <TableHead class="text-right">Acciones</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="sesion in sesiones.data" :key="sesion.id">
                                        <TableCell class="font-medium">{{ sesion.disciplina.nombre }}</TableCell>
                                        <TableCell>
                                            <div class="flex flex-col gap-1">
                                                <div class="flex items-center gap-2">
                                                    <Clock class="h-4 w-4 text-muted-foreground" />
                                                    <span class="text-sm">{{ sesion.horario.hora_inicio.substring(0, 5) }} - {{ sesion.horario.hora_fin.substring(0, 5) }}</span>
                                                </div>
                                                <div class="flex gap-1 flex-wrap">
                                                    <Badge variant="outline" v-for="dia in sesion.horario.dia_semana" :key="dia" class="text-xs">
                                                        {{ diasSemana[dia] }}
                                                    </Badge>
                                                </div>
                                            </div>
                                        </TableCell>
                                        <TableCell>{{ sesion.instructor.name }}</TableCell>
                                        <TableCell class="text-right">
                                            <div class="flex justify-end gap-2">
                                                <Button variant="outline" size="sm" as-child title="Ver">
                                                    <Link :href="`/sesiones/${sesion.id}`">
                                                    <Eye class="h-4 w-4" />
                                                    </Link>
                                                </Button>
                                                <Button variant="outline" size="sm" as-child title="Editar">
                                                    <Link :href="`/sesiones/${sesion.id}/edit`">
                                                    <Pencil class="h-4 w-4" />
                                                    </Link>
                                                </Button>
                                                <Button variant="destructive" size="sm" @click="deleteSesion(sesion.id)"
                                                    title="Eliminar">
                                                    <Trash2 class="h-4 w-4" />
                                                </Button>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>

                            <!-- Paginación -->
                            <div v-if="sesiones.links.length > 3"
                                class="flex flex-wrap gap-1 justify-center p-4 border-t border-border">
                                <template v-for="(link, index) in sesiones.links" :key="index">
                                    <Link v-if="link.url" :href="link.url"
                                        class="px-3 py-2 text-sm border rounded-md transition-colors"
                                        :class="{
                                            'bg-primary text-primary-foreground border-primary': link.active,
                                            'bg-background hover:bg-accent border-border': !link.active
                                        }" v-html="link.label">
                                    </Link>
                                    <span v-else
                                        class="px-3 py-2 text-sm border rounded-md bg-muted text-muted-foreground border-border"
                                        v-html="link.label">
                                    </span>
                                </template>
                            </div>
                        </div>

                        <div v-else class="text-center py-8 text-muted-foreground">
                            No hay sesiones registradas
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>

</template>
