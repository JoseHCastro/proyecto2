<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { ArrowLeft, Download, Building2, MapPin, Phone, Mail } from 'lucide-vue-next';

const props = defineProps({
    pago: Object,
    informacion: Object,
});

const descargarPDF = () => {
    window.open(`/recibos/${props.pago.id}/descargar`, '_blank');
};

const imprimir = () => {
    window.print();
};
</script>

<template>
    <Head title="Recibo de Pago" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-foreground">
                Recibo de Pago
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="mb-4 flex gap-2">
                    <Button variant="ghost" size="sm" as-child>
                        <Link :href="`/suscripciones/${pago.suscripcion_id}`">
                            <ArrowLeft class="mr-2 h-4 w-4" />
                            Volver
                        </Link>
                    </Button>
                    <Button variant="default" size="sm" @click="descargarPDF" class="print:hidden">
                        <Download class="mr-2 h-4 w-4" />
                        Descargar PDF
                    </Button>
                    <Button variant="outline" size="sm" @click="imprimir" class="print:hidden">
                        Imprimir
                    </Button>
                </div>

                <Card id="recibo" class="print:shadow-none">
                    <CardHeader class="border-b">
                        <div class="text-center space-y-4">
                            <h1 class="text-3xl font-bold text-primary">{{ informacion?.nombre || 'Elevation Gym' }}</h1>
                            
                            <div class="space-y-1 text-sm text-muted-foreground">
                                <div class="flex items-center justify-center gap-2">
                                    <MapPin class="h-4 w-4" />
                                    <span>{{ informacion?.direccion }}</span>
                                </div>
                                <div class="flex items-center justify-center gap-4">
                                    <div class="flex items-center gap-1">
                                        <Phone class="h-4 w-4" />
                                        <span>{{ informacion?.telefono }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <Mail class="h-4 w-4" />
                                        <span>{{ informacion?.correo }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-4">
                                <h2 class="text-2xl font-semibold">RECIBO DE PAGO</h2>
                                <p class="text-lg text-muted-foreground">N° {{ pago.id.toString().padStart(6, '0') }}</p>
                            </div>
                        </div>
                    </CardHeader>

                    <CardContent class="space-y-6 pt-6">
                        <!-- Información del cliente -->
                        <div class="space-y-3">
                            <h3 class="text-lg font-semibold border-b pb-2">Información del Cliente</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-muted-foreground">Nombre</p>
                                    <p class="font-medium">{{ pago.usuario?.name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-muted-foreground">Email</p>
                                    <p class="font-medium">{{ pago.usuario?.email }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-muted-foreground">Teléfono</p>
                                    <p class="font-medium">{{ pago.usuario?.telefono || 'No especificado' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-muted-foreground">CI</p>
                                    <p class="font-medium">{{ pago.usuario?.ci || 'No especificado' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Detalles del pago -->
                        <div class="space-y-3">
                            <h3 class="text-lg font-semibold border-b pb-2">Detalles del Pago</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between py-2 border-b">
                                    <span class="text-muted-foreground">Concepto</span>
                                    <span class="font-medium text-right max-w-md">{{ pago.motivo }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b">
                                    <span class="text-muted-foreground">Método de Pago</span>
                                    <span class="font-medium">{{ pago.metodo }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b">
                                    <span class="text-muted-foreground">Fecha de Pago</span>
                                    <span class="font-medium">{{ new Date(pago.fecha).toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' }) }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b">
                                    <span class="text-muted-foreground">Fecha de Vencimiento</span>
                                    <span class="font-medium">{{ new Date(pago.vence).toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' }) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Total -->
                        <div class="bg-primary/10 rounded-lg p-6">
                            <div class="flex justify-between items-center">
                                <span class="text-xl font-semibold">TOTAL PAGADO</span>
                                <span class="text-3xl font-bold text-primary">Bs {{ Number(pago.monto).toFixed(2) }}</span>
                            </div>
                        </div>

                        <!-- Pie de página -->
                        <div class="text-center pt-6 border-t space-y-2">
                            
                            <p class="text-xs text-muted-foreground">
                                Generado el {{ new Date().toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' }) }}
                            </p>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>

<style>
@media print {
    .print\:hidden {
        display: none !important;
    }
    
    .print\:shadow-none {
        box-shadow: none !important;
    }
    
    body {
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
    }
}
</style>
