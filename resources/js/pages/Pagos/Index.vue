<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { QrCode, RefreshCw, CheckCircle, XCircle, Clock } from 'lucide-vue-next';
import { successAlert, errorAlert } from '@/composables/useSweetAlert';

const props = defineProps({
    pagos: Array,
});

const page = usePage();
const qrData = ref(page.props.flash?.qr_data || null);
const loading = ref(false);
const pollingInterval = ref(null);

const generarQr = () => {
    loading.value = true;
    router.post('/pagos/generar-qr', {}, {
        onSuccess: () => {
            console.log('QR Generado con éxito', page.props.flash?.qr_data);
            loading.value = false;
            qrData.value = page.props.flash?.qr_data;
            if (qrData.value) {
                iniciarPolling(qrData.value.transactionId);
            }
        },
        onError: (errors) => {
            console.error('Error al generar QR:', errors);
            loading.value = false;
            // Intentar obtener el mensaje de error específico si viene en el objeto errors
            const errorMsg = errors.error || 'No se pudo generar el QR. Revisa la consola para más detalles.';
            errorAlert({
                title: 'Error',
                text: errorMsg
            });
        }
    });
};

const verQr = (pago) => {
    qrData.value = {
        qrBase64: pago.qr_image,
        transactionId: pago.pagofacil_transaction_id
    };
    // Scroll hacia arriba para ver el QR
    window.scrollTo({ top: 0, behavior: 'smooth' });
    iniciarPolling(pago.pagofacil_transaction_id);
};

const pagoSeleccionadoId = ref(null);

const consultarEstado = async (pago) => {
    pagoSeleccionadoId.value = pago.id;
    loading.value = true;

    try {
        const response = await axios.get(`/pagos/consultar/${pago.id}`);
        const data = response.data;

        if (data.status === 'success') {
            // Actualizar el estado en la lista local
            const nuevoEstado = data.data.paymentStatus;
            pago.estado = nuevoEstado;

            let mensaje = 'El estado se ha actualizado.';
            if (nuevoEstado == 2) mensaje = '¡Pago confirmado exitosamente!';
            if (nuevoEstado == 4) mensaje = 'El pago ha sido anulado.';
            if (nuevoEstado == 5) mensaje = 'El pago está en revisión.';

            successAlert({ title: 'Estado Actualizado', text: mensaje });

            // Si se pagó, detener polling si estaba activo para este pago
            if (nuevoEstado == 2 && qrData.value?.transactionId == pago.pagofacil_transaction_id) {
                qrData.value = null;
                detenerPolling();
            }
        } else {
            errorAlert({ title: 'Error', text: data.message || 'No se pudo consultar el estado.' });
        }
    } catch (error) {
        console.error(error);
        errorAlert({ title: 'Error', text: 'Ocurrió un error al consultar el servicio.' });
    } finally {
        loading.value = false;
        pagoSeleccionadoId.value = null;
    }
};

const getEstadoBadge = (estado) => {
    switch (parseInt(estado)) {
        case 1: return { variant: 'secondary', label: 'Pendiente', icon: Clock };
        case 2: return { variant: 'default', label: 'Pagado', icon: CheckCircle };
        case 4: return { variant: 'destructive', label: 'Anulado', icon: XCircle };
        case 5: return { variant: 'default', label: 'Pagada - Revisión', icon: CheckCircle }; // Cambiado a default (verde) y nuevo texto
        default: return { variant: 'outline', label: 'Desconocido', icon: Clock };
    }
};

// Polling para verificar estado del pago actual
const iniciarPolling = (transactionId) => {
    if (pollingInterval.value) clearInterval(pollingInterval.value);

    pollingInterval.value = setInterval(async () => {
        try {
            // Buscamos el pago en la lista local primero para ver si ya cambió
            // Pero mejor consultamos al backend
            // Necesitamos el ID local del pago, pero aquí tenemos el transactionId de PagoFácil
            // Vamos a recargar la página parcialmente para ver si el estado cambió en la lista

            router.reload({
                only: ['pagos'],
                onSuccess: () => {
                    // Verificar si el pago correspondiente cambió a estado 2
                    const pago = props.pagos.find(p => p.pagofacil_transaction_id == transactionId);
                    if (pago && pago.estado == 2) {
                        detenerPolling();
                        successAlert({ title: '¡Pago Exitoso!', text: 'Tu pago ha sido confirmado.' });
                        qrData.value = null; // Ocultar QR
                    }
                }
            });
        } catch (e) {
            console.error(e);
        }
    }, 5000); // Cada 5 segundos
};

const detenerPolling = () => {
    if (pollingInterval.value) {
        clearInterval(pollingInterval.value);
        pollingInterval.value = null;
    }
};

onUnmounted(() => {
    detenerPolling();
});
</script>

<template>

    <Head title="Pagos QR" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-foreground">
                Pagos con QR
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">

                <!-- Sección Generar QR -->
                <!-- Mostrar QR Generado -->
                <div v-if="qrData" class="flex justify-center mb-6">
                    <Card class="w-full max-w-md border-primary border-2">
                        <CardHeader>
                            <CardTitle class="text-center text-primary">¡Escanea para Pagar!</CardTitle>
                            <CardDescription class="text-center">Usa tu aplicación bancaria favorita</CardDescription>
                        </CardHeader>
                        <CardContent class="flex flex-col items-center justify-center space-y-4">
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <img :src="`data:image/png;base64,${qrData.qrBase64}`" alt="QR de Pago"
                                    class="w-64 h-64 object-contain" />
                            </div>
                            <p class="text-sm text-muted-foreground text-center">
                                ID Transacción: <span class="font-mono">{{ qrData.transactionId }}</span>
                            </p>
                            <div class="flex items-center text-sm text-yellow-600 animate-pulse">
                                <Clock class="mr-2 h-4 w-4" />
                                Esperando confirmación de pago...
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Historial de Pagos -->
                <Card>
                    <CardHeader>
                        <CardTitle>Historial de Pagos</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="rounded-md border">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-muted text-muted-foreground">
                                    <tr>
                                        <th class="p-3 font-medium">ID Pedido</th>
                                        <th class="p-3 font-medium">Fecha</th>
                                        <th class="p-3 font-medium">Monto</th>
                                        <th class="p-3 font-medium">Estado</th>
                                        <th class="p-3 font-medium">QR</th>
                                        <th class="p-3 font-medium text-right">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="pago in pagos" :key="pago.id" class="border-t hover:bg-muted/50">
                                        <td class="p-3 font-mono">{{ pago.pedido_id }}</td>
                                        <td class="p-3">{{ new Date(pago.created_at).toLocaleString() }}</td>
                                        <td class="p-3 font-bold">Bs {{ pago.monto }}</td>
                                        <td class="p-3">
                                            <Badge :variant="getEstadoBadge(pago.estado).variant">
                                                <component :is="getEstadoBadge(pago.estado).icon"
                                                    class="mr-1 h-3 w-3" />
                                                {{ getEstadoBadge(pago.estado).label }}
                                            </Badge>
                                        </td>
                                        <td class="p-3">
                                            <Button v-if="pago.estado == 1 && pago.qr_image" variant="outline" size="sm"
                                                @click="verQr(pago)">
                                                <QrCode class="h-4 w-4 mr-1" /> Ver QR
                                            </Button>
                                        </td>
                                        <td class="p-3 text-right">
                                            <!-- Botón para consultar estado manualmente -->
                                            <Button variant="ghost" size="sm" :disabled="loading"
                                                @click="consultarEstado(pago)">
                                                <RefreshCw class="h-4 w-4"
                                                    :class="{ 'animate-spin': loading && pagoSeleccionadoId === pago.id }" />
                                            </Button>
                                        </td>
                                    </tr>
                                    <tr v-if="pagos.length === 0">
                                        <td colspan="6" class="p-6 text-center text-muted-foreground">
                                            No hay pagos registrados.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </CardContent>
                </Card>

            </div>
        </div>
    </AppLayout>
</template>
