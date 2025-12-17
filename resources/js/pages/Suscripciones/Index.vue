<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Card, CardContent, CardHeader, CardTitle, CardDescription, CardFooter } from '@/components/ui/card';
import { Plus, Eye, Pencil, Trash2, CalendarDays, Package, CreditCard, RefreshCw } from 'lucide-vue-next';
import { confirmAlert, successAlert } from '@/composables/useSweetAlert';
import { index as suscripcionesIndex, create as suscripcionesCreate, show as suscripcionesShow, edit as suscripcionesEdit, destroy as suscripcionesDestroy } from '@/routes/suscripciones';

const props = defineProps({
    suscripciones: Array,
    isCliente: {
        type: Boolean,
        default: false,
    },
});

const deleteSuscripcion = async (id) => {
    const result = await confirmAlert({
        title: '¿Eliminar suscripción?',
        text: 'Esta acción también eliminará todos los pagos asociados',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
    });

    if (result.isConfirmed) {
        router.delete(suscripcionesDestroy.url({ suscripcione: id }), {
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
                {{ isCliente ? 'Mis Suscripciones' : 'Suscripciones' }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Vista Cliente: Cards -->
                <template v-if="isCliente">
                    <div v-if="suscripciones.length === 0" class="text-center py-12">
                        <Package class="mx-auto h-16 w-16 text-muted-foreground mb-4" />
                        <p class="text-lg text-muted-foreground">No tienes suscripciones activas</p>
                        <p class="text-sm text-muted-foreground mt-2">Contacta a secretaría para adquirir un paquete</p>
                    </div>
                    
                    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <Card 
                            v-for="suscripcion in suscripciones" 
                            :key="suscripcion.id"
                            class="group hover:shadow-lg transition-all duration-300 hover:scale-[1.02] border-2"
                            :class="{
                                'hover:border-green-500/50 border-green-500/20': suscripcion.estado === 'activo',
                                'hover:border-yellow-500/50 border-yellow-500/20': suscripcion.estado === 'suspendido',
                                'hover:border-red-500/50 border-red-500/20': suscripcion.estado === 'vencida' || suscripcion.estado === 'cancelada',
                                'hover:border-gray-500/50 border-gray-500/20': suscripcion.estado === 'inactivo',
                            }"
                        >
                            <CardHeader class="pb-3">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 rounded-lg bg-primary/10 text-primary">
                                            <Package class="h-6 w-6" />
                                        </div>
                                        <div>
                                            <CardTitle class="text-lg">{{ suscripcion.paquete?.nombre }}</CardTitle>
                                            <CardDescription class="text-xs mt-1">
                                                {{ suscripcion.paquete?.membresia?.nombre }}
                                            </CardDescription>
                                        </div>
                                    </div>
                                    <Badge :variant="getEstadoBadge(suscripcion.estado)" class="capitalize">
                                        {{ suscripcion.estado }}
                                    </Badge>
                                </div>
                            </CardHeader>
                            <CardContent class="space-y-4">
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="flex items-center gap-2 p-3 rounded-lg bg-muted/50">
                                        <CalendarDays class="h-4 w-4 text-green-500" />
                                        <div>
                                            <p class="text-xs text-muted-foreground">Inicio</p>
                                            <p class="text-sm font-medium">{{ new Date(suscripcion.fecha_inicio).toLocaleDateString('es-ES') }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 p-3 rounded-lg bg-muted/50">
                                        <CalendarDays class="h-4 w-4 text-red-500" />
                                        <div>
                                            <p class="text-xs text-muted-foreground">Vence</p>
                                            <p class="text-sm font-medium">{{ new Date(suscripcion.fecha_fin).toLocaleDateString('es-ES') }}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-between p-3 rounded-lg bg-muted/30">
                                    <div class="flex items-center gap-2">
                                        <RefreshCw class="h-4 w-4 text-muted-foreground" />
                                        <span class="text-sm">Renovación automática</span>
                                    </div>
                                    <Badge :variant="suscripcion.renovacion_automatica ? 'default' : 'secondary'">
                                        {{ suscripcion.renovacion_automatica ? 'Sí' : 'No' }}
                                    </Badge>
                                </div>
                                
                                <div v-if="suscripcion.paquete?.precio" class="flex items-center gap-2 text-lg font-bold text-primary">
                                    <CreditCard class="h-5 w-5" />
                                    Bs {{ Number(suscripcion.paquete.precio).toFixed(2) }}
                                </div>
                            </CardContent>
                            <CardFooter class="pt-0">
                                <Button variant="outline" size="sm" class="w-full" as-child>
                                    <Link :href="suscripcionesShow.url({ suscripcione: suscripcion.id })">
                                        <Eye class="h-4 w-4 mr-2" />
                                        Ver detalles
                                    </Link>
                                </Button>
                            </CardFooter>
                        </Card>
                    </div>
                </template>

                <!-- Vista Admin: Tabla -->
                <template v-else>
                    <div class="mb-4 flex justify-end">
                        <Button as-child>
                            <Link :href="suscripcionesCreate.url()">
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
                                                    <Button v-if="suscripcion.id" variant="ghost" size="sm" as-child>
                                                        <Link :href="suscripcionesShow.url({ suscripcione: suscripcion.id })">
                                                            <Eye class="h-4 w-4" />
                                                        </Link>
                                                    </Button>
                                                    <Button v-if="suscripcion.id" variant="ghost" size="sm" as-child>
                                                        <Link :href="suscripcionesEdit.url({ suscripcione: suscripcion.id })">
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
                </template>
            </div>
        </div>
    </AppLayout>
</template>
