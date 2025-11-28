<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
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
import { Eye, Pencil, Trash2, Plus } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { confirmAlert, successAlert, errorAlert } from '@/composables/useSweetAlert';

const props = defineProps({
    disciplinas: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');

watch(search, (value) => {
    router.get('/disciplinas', { search: value }, {
        preserveState: true,
        replace: true,
    });
});

const deleteDisciplina = (id) => {
    confirmAlert({
        title: '¿Estás seguro?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(`/disciplinas/${id}`, {
                onSuccess: () => {
                    successAlert({
                        title: '¡Eliminado!',
                        text: 'La disciplina ha sido eliminada correctamente'
                    });
                },
                onError: () => {
                    errorAlert({
                        title: 'Error',
                        text: 'No se pudo eliminar la disciplina'
                    });
                }
            });
        }
    });
};
</script>

<template>

    <Head title="Gestión de Disciplinas" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-foreground">
                Gestión de Disciplinas
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-background shadow-sm sm:rounded-lg border border-border">
                    <div class="p-6 text-foreground">
                        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                            <div class="flex gap-4 w-full md:w-auto">
                                <Input v-model="search" placeholder="Buscar disciplinas..."
                                    class="w-full md:w-64" />
                            </div>
                            <Button as-child>
                                <AppLink href="/disciplinas/create">
                                <Plus class="mr-2 h-4 w-4" />
                                Nueva Disciplina
                                </AppLink>
                            </Button>
                        </div>

                        <div class="rounded-md border" v-if="disciplinas && disciplinas.data">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Nombre</TableHead>
                                        <TableHead>Descripción</TableHead>
                                        <TableHead class="text-right">Acciones</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="disciplina in disciplinas.data" :key="disciplina.id">
                                        <TableCell class="font-medium">{{ disciplina.nombre }}</TableCell>
                                        <TableCell class="max-w-md truncate">{{ disciplina.descripcion || 'Sin descripción' }}</TableCell>
                                        <TableCell class="text-right">
                                            <div class="flex justify-end gap-2">
                                                <Button variant="outline" size="sm" as-child title="Ver">
                                                    <Link :href="`/disciplinas/${disciplina.id}`">
                                                    <Eye class="h-4 w-4" />
                                                    </AppLink>
                                                </Button>
                                                <Button variant="outline" size="sm" as-child title="Editar">
                                                    <Link :href="`/disciplinas/${disciplina.id}/edit`">
                                                    <Pencil class="h-4 w-4" />
                                                    </AppLink>
                                                </Button>
                                                <Button variant="destructive" size="sm" @click="deleteDisciplina(disciplina.id)"
                                                    title="Eliminar">
                                                    <Trash2 class="h-4 w-4" />
                                                </Button>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>

                            <!-- Paginación -->
                            <div v-if="disciplinas.links.length > 3"
                                class="flex flex-wrap gap-1 justify-center p-4 border-t border-border">
                                <template v-for="(link, index) in disciplinas.links" :key="index">
                                    <Link v-if="link.url" :href="link.url"
                                        class="px-3 py-2 text-sm border rounded-md transition-colors"
                                        :class="{
                                            'bg-primary text-primary-foreground border-primary': link.active,
                                            'bg-background hover:bg-accent border-border': !link.active
                                        }" v-html="link.label">
                                    </AppLink>
                                    <span v-else
                                        class="px-3 py-2 text-sm border rounded-md bg-muted text-muted-foreground border-border"
                                        v-html="link.label">
                                    </span>
                                </template>
                            </div>
                        </div>

                        <div v-else class="text-center py-8 text-muted-foreground">
                            No hay disciplinas registradas
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>

</template>
