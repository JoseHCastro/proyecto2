<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ArrowLeft, Pencil, CalendarDays, CreditCard, User, Package, DollarSign, Eye } from 'lucide-vue-next';
import { confirmAlert, successAlert, errorAlert } from '@/composables/useSweetAlert';

const props = defineProps({
    suscripcion: Object,
});

const isDialogOpen = ref(false);
const pagoSeleccionado = ref(null);

const formPago = useForm({
    metodo: 'efectivo',
});

const abrirDialogPago = (pago) => {
    pagoSeleccionado.value = pago;
    formPago.metodo = 'efectivo';
    isDialogOpen.value = true;
};

const procesarPago = () => {
    formPago.post(`/suscripciones/pagar/${pagoSeleccionado.value.id}`, {
        onSuccess: () => {
            successAlert({
                title: '¡Pago Registrado!',
                text: 'El pago ha sido registrado correctamente',
            });
            isDialogOpen.value = false;
        },
        onError: (errors) => {
            if (errors.error) {
                errorAlert({
                    title: 'Error',
                    text: errors.error,
                });
            } else {
                errorAlert({
                    title: 'Error',
                    text: 'Hubo un problema al registrar el pago',
                });
            }
        },
    });
};

const getEstadoBadge = (estado) => {
    const variants = {
        'activo': 'default',
        'inactivo': 'secondary',
        'suspendido': 'destructive',
    };
    return variants[estado] || 'secondary';
};

const getEstadoPagoBadge = (estado) => {
    const variants = {
        'pagada': 'default',
        'impaga': 'destructive',
        'pendiente': 'secondary',
        'cancelado': 'secondary',
    };
    return variants[estado] || 'secondary';
};

const esPrimerPagoImpago = (pago) => {
    const pagosAnterioresImpagos = props.suscripcion.pagos.filter(p => 
        new Date(p.vence) < new Date(pago.vence) && p.estado === 'impaga'
    );
    return pagosAnterioresImpagos.length === 0;
};

const calcularDiasRestantes = (fechaFin) => {
    const hoy = new Date();
    const fin = new Date(fechaFin);
    const diff = Math.ceil((fin - hoy) / (1000 * 60 * 60 * 24));
    return diff;
};
</script>

<template>
    <Head title="Detalles de Suscripción" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-foreground">
                Detalles de Suscripción
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-6xl sm:px-6 lg:px-8 space-y-6">
                <div class="mb-4">
                    <Button variant="ghost" size="sm" as-child>
                        <AppLink href="/suscripciones">
                            <ArrowLeft class="mr-2 h-4 w-4" />
                            Volver al listado
                        </AppLink>
                    </Button>
                </div>

                <!-- Información de la suscripción -->
                <Card>
                    <CardHeader>
                        <div class="flex items-start justify-between">
                            <div>
                                <CardTitle class="text-2xl">Suscripción #{{ suscripcion.id }}</CardTitle>
                                <CardDescription class="mt-1">
                                    <Badge :variant="getEstadoBadge(suscripcion.estado)" class="mt-2">
                                        {{ suscripcion.estado }}
                                    </Badge>
                                </CardDescription>
                            </div>
                            <Button as-child>
                                <Link :href="`/suscripciones/${suscripcion.id}/edit`">
                                    <Pencil class="mr-2 h-4 w-4" />
                                    Editar
                                </AppLink>
                            </Button>
                        </div>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div class="flex items-start gap-3">
                                    <User class="mt-1 h-5 w-5 text-muted-foreground" />
                                    <div class="flex-1">
                                        <label class="text-sm font-medium text-muted-foreground">Cliente</label>
                                        <p class="text-base mt-1">{{ suscripcion.usuario?.name }}</p>
                                        <p class="text-sm text-muted-foreground">{{ suscripcion.usuario?.email }}</p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-3">
                                    <Package class="mt-1 h-5 w-5 text-muted-foreground" />
                                    <div class="flex-1">
                                        <label class="text-sm font-medium text-muted-foreground">Paquete</label>
                                        <p class="text-base mt-1">{{ suscripcion.paquete?.nombre }}</p>
                                        <p class="text-sm text-muted-foreground">
                                            {{ suscripcion.paquete?.membresia?.nombre }} - 
                                            Bs {{ Number(suscripcion.paquete?.precio).toFixed(2) }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div class="flex items-start gap-3">
                                    <CalendarDays class="mt-1 h-5 w-5 text-muted-foreground" />
                                    <div class="flex-1">
                                        <label class="text-sm font-medium text-muted-foreground">Periodo</label>
                                        <p class="text-base mt-1">
                                            {{ new Date(suscripcion.fecha_inicio).toLocaleDateString('es-ES') }} - 
                                            {{ new Date(suscripcion.fecha_fin).toLocaleDateString('es-ES') }}
                                        </p>
                                        <p class="text-sm text-muted-foreground">
                                            <template v-if="calcularDiasRestantes(suscripcion.fecha_fin) > 0">
                                                {{ calcularDiasRestantes(suscripcion.fecha_fin) }} días restantes
                                            </template>
                                            <template v-else>
                                                Vencida
                                            </template>
                                        </p>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-medium text-muted-foreground">Renovación Automática</label>
                                    <div>
                                        <Badge :variant="suscripcion.renovacion_automatica ? 'default' : 'secondary'">
                                            {{ suscripcion.renovacion_automatica ? 'Activada' : 'Desactivada' }}
                                        </Badge>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Pagos/Cuotas -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <CreditCard class="h-5 w-5" />
                            Pagos / Cuotas
                        </CardTitle>
                        <CardDescription>
                            Total de {{ suscripcion.pagos?.length || 0 }} cuota(s)
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div v-if="suscripcion.pagos && suscripcion.pagos.length > 0" class="rounded-md border">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Cuota</TableHead>
                                        <TableHead>Monto</TableHead>
                                        <TableHead>Estado</TableHead>
                                        <TableHead>Método</TableHead>
                                        <TableHead>Fecha Pago</TableHead>
                                        <TableHead>Vence</TableHead>
                                        <TableHead class="text-right">Acciones</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="(pago, index) in suscripcion.pagos" :key="pago.id">
                                        <TableCell class="font-medium">
                                            Cuota {{ index + 1 }}
                                        </TableCell>
                                        <TableCell>
                                            Bs {{ Number(pago.monto).toFixed(2) }}
                                        </TableCell>
                                        <TableCell>
                                            <Badge :variant="getEstadoPagoBadge(pago.estado)">
                                                {{ pago.estado }}
                                            </Badge>
                                        </TableCell>
                                        <TableCell>
                                            {{ pago.metodo || '-' }}
                                        </TableCell>
                                        <TableCell>
                                            {{ pago.fecha ? new Date(pago.fecha).toLocaleDateString('es-ES') : '-' }}
                                        </TableCell>
                                        <TableCell>
                                            {{ new Date(pago.vence).toLocaleDateString('es-ES') }}
                                        </TableCell>
                                        <TableCell class="text-right">
                                            <div class="flex justify-end gap-2">
                                                <Button 
                                                    v-if="pago.estado === 'impaga' && esPrimerPagoImpago(pago)"
                                                    variant="default" 
                                                    size="sm"
                                                    @click="abrirDialogPago(pago)"
                                                >
                                                    <DollarSign class="h-4 w-4 mr-1" />
                                                    Pagar
                                                </Button>
                                                <Button 
                                                    v-if="pago.estado === 'pagada'"
                                                    variant="ghost" 
                                                    size="sm"
                                                    as-child
                                                >
                                                    <Link :href="`/recibos/${pago.id}`" target="_blank">
                                                        <Eye class="h-4 w-4 mr-1" />
                                                        Ver Recibo
                                                    </AppLink>
                                                </Button>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </div>

                        <div v-else class="text-center py-8 text-muted-foreground">
                            <p>No hay pagos registrados</p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Dialog para pagar cuota -->
                <Dialog v-model:open="isDialogOpen">
                    <DialogContent class="sm:max-w-[425px]">
                        <DialogHeader>
                            <DialogTitle>Registrar Pago</DialogTitle>
                            <DialogDescription>
                                Registre el pago de la cuota seleccionada
                            </DialogDescription>
                        </DialogHeader>
                        <form @submit.prevent="procesarPago" class="space-y-4">
                            <div v-if="pagoSeleccionado" class="rounded-lg border p-4 bg-muted/50">
                                <h4 class="text-sm font-medium mb-2">Detalles del Pago</h4>
                                <div class="space-y-2 text-sm">
                                    <div>
                                        <span class="text-muted-foreground">Monto:</span>
                                        <span class="ml-2 font-medium">Bs {{ Number(pagoSeleccionado.monto).toFixed(2) }}</span>
                                    </div>
                                    <div>
                                        <span class="text-muted-foreground">Vence:</span>
                                        <span class="ml-2 font-medium">{{ new Date(pagoSeleccionado.vence).toLocaleDateString('es-ES') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label for="metodo">Método de Pago *</Label>
                                <Select v-model="formPago.metodo" required>
                                    <SelectTrigger id="metodo">
                                        <SelectValue placeholder="Selecciona un método" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="efectivo">Efectivo</SelectItem>
                                        <SelectItem value="QR">QR</SelectItem>
                                    </SelectContent>
                                </Select>
                                <span v-if="formPago.errors.metodo" class="text-sm text-destructive">
                                    {{ formPago.errors.metodo }}
                                </span>
                            </div>

                            <div class="flex justify-end gap-3">
                                <Button type="button" variant="outline" @click="isDialogOpen = false">
                                    Cancelar
                                </Button>
                                <Button type="submit" :disabled="formPago.processing">
                                    {{ formPago.processing ? 'Procesando...' : 'Registrar Pago' }}
                                </Button>
                            </div>
                        </form>
                    </DialogContent>
                </Dialog>
            </div>
        </div>
    </AppLayout>
</template>
