<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Plus, Eye, Pencil, Trash2 } from 'lucide-vue-next';
import { confirmAlert, successAlert } from '@/composables/useSweetAlert';

const props = defineProps({
    suscripciones: Array,
});

const deleteSuscripcion = async (id) => {
    const result = await confirmAlert({
        title: '¿Eliminar suscripción?',
        text: 'Esta acción también eliminará todos los pagos asociados',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
    });

    if (result.isConfirmed) {
        router.delete(`/suscripciones/${id}`, {
            onSuccess: () => {
                successAlert({
                    title: '¡Eliminado!',
                    text: 'La suscripción ha sido eliminada correctamente',
                });
            },
        });
    }
};

const getEstadoBadge = (estado) => {
    const variants = {
        'activo': 'default',
        'inactivo': 'secondary',
        'suspendido': 'destructive',
        'vencida': 'destructive',
        'cancelada': 'secondary',
    };
    return variants[estado] || 'secondary';
};
</script>

<template>
    <Head title="Suscripciones" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-foreground">
                Suscripciones
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="mb-4 flex justify-end">
                    <Button as-child>
                        <Link href="/suscripciones/create">
                            <Plus class="mr-2 h-4 w-4" />
                            Nueva Suscripción
                        </Link>
                    </Button>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Listado de Suscripciones</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div v-if="suscripciones.length > 0" class="rounded-md border">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Cliente</TableHead>
                                        <TableHead>Paquete</TableHead>
                                        <TableHead>Membresía</TableHead>
                                        <TableHead>Estado</TableHead>
                                        <TableHead>Fecha Inicio</TableHead>
                                        <TableHead>Fecha Fin</TableHead>
                                        <TableHead>Renovación</TableHead>
                                        <TableHead class="text-right">Acciones</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="suscripcion in suscripciones" :key="suscripcion.id">
                                        <TableCell class="font-medium">
                                            {{ suscripcion.usuario?.name }}
                                        </TableCell>
                                        <TableCell>
                                            {{ suscripcion.paquete?.nombre }}
                                        </TableCell>
                                        <TableCell>
                                            {{ suscripcion.paquete?.membresia?.nombre }}
                                        </TableCell>
                                        <TableCell>
                                            <Badge :variant="getEstadoBadge(suscripcion.estado)">
                                                {{ suscripcion.estado }}
                                            </Badge>
                                        </TableCell>
                                        <TableCell>
                                            {{ new Date(suscripcion.fecha_inicio).toLocaleDateString('es-ES') }}
                                        </TableCell>
                                        <TableCell>
                                            {{ new Date(suscripcion.fecha_fin).toLocaleDateString('es-ES') }}
                                        </TableCell>
                                        <TableCell>
                                            <Badge :variant="suscripcion.renovacion_automatica ? 'default' : 'secondary'">
                                                {{ suscripcion.renovacion_automatica ? 'Sí' : 'No' }}
                                            </Badge>
                                        </TableCell>
                                        <TableCell class="text-right">
                                            <div class="flex justify-end gap-2">
                                                <Button variant="ghost" size="sm" as-child>
                                                    <Link :href="`/suscripciones/${suscripcion.id}`">
                                                        <Eye class="h-4 w-4" />
                                                    </Link>
                                                </Button>
                                                <Button variant="ghost" size="sm" as-child>
                                                    <Link :href="`/suscripciones/${suscripcion.id}/edit`">
                                                        <Pencil class="h-4 w-4" />
                                                    </Link>
                                                </Button>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </div>

                        <div v-else class="text-center py-8 text-muted-foreground">
                            <p>No hay suscripciones registradas</p>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
