<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Camera, Mail, CheckCircle, XCircle, AlertCircle } from 'lucide-vue-next';
import { successAlert, errorAlert } from '@/composables/useSweetAlert';
import { Html5Qrcode } from 'html5-qrcode';

const videoRef = ref(null);
const scannerRef = ref(null);
const escaneando = ref(false);
const modoManual = ref(false);
const ultimoEscaneo = ref('');
const tiempoEspera = ref(0);

const form = useForm({
    email: '',
});

let html5QrCode = null;
let intervaloEscaneo = null;

const iniciarEscaner = async () => {
    try {
        escaneando.value = true;
        
        html5QrCode = new Html5Qrcode("qr-reader");
        
        await html5QrCode.start(
            { facingMode: "environment" },
            {
                fps: 10,
                qrbox: { width: 250, height: 250 }
            },
            onScanSuccess,
            onScanFailure
        );
    } catch (err) {
        console.error("Error al iniciar el escáner:", err);
        errorAlert({
            title: 'Error de Cámara',
            text: 'No se pudo acceder a la cámara. Usa el registro manual.',
        });
        modoManual.value = true;
        escaneando.value = false;
    }
};

const detenerEscaner = async () => {
    if (html5QrCode) {
        try {
            const state = html5QrCode.getState();
            if (state === 2) { // 2 = SCANNING
                await html5QrCode.stop();
            }
            html5QrCode.clear();
            html5QrCode = null;
        } catch (err) {
            console.error("Error al detener el escáner:", err);
        }
    }
    escaneando.value = false;
};

const onScanSuccess = (decodedText) => {
    // Evitar escaneos duplicados en corto tiempo
    if (decodedText === ultimoEscaneo.value && tiempoEspera.value > 0) {
        return;
    }

    ultimoEscaneo.value = decodedText;
    tiempoEspera.value = 5; // 5 segundos de espera

    // Iniciar cuenta regresiva
    const intervalo = setInterval(() => {
        tiempoEspera.value--;
        if (tiempoEspera.value <= 0) {
            clearInterval(intervalo);
        }
    }, 1000);

    // Registrar asistencia
    registrarAsistencia(decodedText);
};

const onScanFailure = (error) => {
    // Ignorar errores de no encontrar QR
};

const registrarAsistencia = async (email) => {
    try {
        console.log('Registrando asistencia para:', email);
        
        const response = await fetch('/asistencias/registrar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ email }),
        });

        console.log('Response status:', response.status);
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Error response:', errorText);
            throw new Error(`HTTP ${response.status}: ${errorText}`);
        }

        const data = await response.json();
        console.log('Response data:', data);

        if (data.success) {
            mostrarAlertaExito(data);
        } else {
            mostrarAlertaError(data);
        }
    } catch (error) {
        console.error('Error completo:', error);
        errorAlert({
            title: 'Error',
            text: 'Hubo un problema al registrar la asistencia',
        });
    }
};

const mostrarAlertaExito = (data) => {
    const mensaje = `<div class="text-center">
        <p class="text-xl font-bold mb-2">${data.nombre}</p>
        <p class="text-green-600 font-semibold">✓ Suscripción Activa</p>
        <p class="text-sm">${data.suscripcion.membresia} - ${data.suscripcion.paquete}</p>
        <p class="text-sm text-gray-600">Válida hasta: ${data.suscripcion.fecha_fin}</p>
    </div>`;

    successAlert({
        title: '¡Asistencia Registrada!',
        html: mensaje,
        timer: 3000,
    });
};

const mostrarAlertaError = (data) => {
    let mensaje = '';
    let titulo = 'Error';
    
    if (data.tipo === 'ya_marco') {
        titulo = 'Asistencia Duplicada';
        mensaje = `<div class="text-center">
            <p class="text-xl font-bold mb-2">${data.nombre}</p>
            <p class="text-orange-500">Ya marcaste asistencia hoy a las ${data.hora}</p>
        </div>`;
    } else if (data.tipo === 'no_encontrado') {
        titulo = 'Usuario no encontrado';
        mensaje = 'Ese correo no está registrado en el sistema';
    } else if (data.tipo === 'sin_suscripcion') {
        titulo = 'Sin Suscripción';
        mensaje = `<div class="text-center">
            <p class="text-xl font-bold mb-2">${data.nombre}</p>
            <p class="text-red-500">No tienes ninguna suscripción</p>
        </div>`;
    } else if (data.tipo?.startsWith('suscripcion_')) {
        titulo = 'Suscripción No Válida';
        mensaje = `<div class="text-center">
            <p class="text-xl font-bold mb-2">${data.nombre}</p>
            <p class="text-red-500">${data.mensaje}</p>
        </div>`;
    } else if (data.tipo === 'no_cliente') {
        titulo = 'Error de permisos';
        mensaje = 'El usuario no es un cliente';
    } else {
        mensaje = data.mensaje || 'Error al procesar la solicitud';
    }

    errorAlert({
        title: titulo,
        html: mensaje,
        timer: 3000,
    });
};

const registrarManual = async () => {
    if (!form.email) {
        errorAlert({
            title: 'Error',
            text: 'Por favor ingresa un correo electrónico',
        });
        return;
    }

    await registrarAsistencia(form.email);
    form.reset();
};

const cambiarModo = async () => {
    if (modoManual.value) {
        // Cambiar a modo cámara
        await detenerEscaner(); // Asegurar que está limpio
        modoManual.value = false;
        // Pequeña espera para asegurar que la cámara se liberó
        await new Promise(resolve => setTimeout(resolve, 500));
        await iniciarEscaner();
    } else {
        // Cambiar a modo manual
        await detenerEscaner();
        modoManual.value = true;
    }
};

onMounted(() => {
    iniciarEscaner();
});

onUnmounted(() => {
    detenerEscaner();
});
</script>

<template>
    <Head title="Registrar Asistencia" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-foreground">
                Registrar Asistencia
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="mb-4 flex justify-end">
                    <Button @click="cambiarModo" variant="outline">
                        <component :is="modoManual ? Camera : Mail" class="mr-2 h-4 w-4" />
                        {{ modoManual ? 'Usar Cámara' : 'Registro Manual' }}
                    </Button>
                </div>

                <!-- Modo Cámara -->
                <Card v-if="!modoManual">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Camera class="h-5 w-5" />
                            Escaneo de Código QR
                        </CardTitle>
                        <CardDescription>
                            Coloca el código QR frente a la cámara para registrar la asistencia
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="flex flex-col items-center gap-4">
                            <div id="qr-reader" class="w-full max-w-md"></div>
                            
                            <div v-if="tiempoEspera > 0" class="text-center">
                                <p class="text-sm text-muted-foreground">
                                    Espera {{ tiempoEspera }} segundos antes del siguiente escaneo
                                </p>
                            </div>

                            <div class="text-center text-sm text-muted-foreground mt-4">
                                <p class="font-medium">Estado: 
                                    <span :class="escaneando ? 'text-green-600' : 'text-red-600'">
                                        {{ escaneando ? 'Escaneando...' : 'Detenido' }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Modo Manual -->
                <Card v-else>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Mail class="h-5 w-5" />
                            Registro Manual
                        </CardTitle>
                        <CardDescription>
                            Ingresa el correo electrónico del cliente para registrar su asistencia
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="registrarManual" class="space-y-4">
                            <div class="space-y-2">
                                <Label for="email">Correo Electrónico</Label>
                                <Input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    placeholder="cliente@ejemplo.com"
                                    required
                                />
                            </div>

                            <Button type="submit" class="w-full" :disabled="form.processing">
                                <CheckCircle class="mr-2 h-4 w-4" />
                                Registrar Asistencia
                            </Button>
                        </form>
                    </CardContent>
                </Card>

                <!-- Instrucciones -->
                <Card class="mt-6">
                    <CardHeader>
                        <CardTitle class="text-base">Información</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-2 text-sm text-muted-foreground">
                            <div class="flex items-start gap-2">
                                <CheckCircle class="h-4 w-4 text-green-600 mt-0.5" />
                                <p>La asistencia se registra automáticamente al escanear el QR</p>
                            </div>
                            <div class="flex items-start gap-2">
                                <AlertCircle class="h-4 w-4 text-orange-500 mt-0.5" />
                                <p>Solo se permite un registro por día por cliente</p>
                            </div>
                            <div class="flex items-start gap-2">
                                <XCircle class="h-4 w-4 text-blue-600 mt-0.5" />
                                <p>Se mostrará el estado de la suscripción al registrar</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
#qr-reader {
    border: 2px solid hsl(var(--primary));
    border-radius: 0.5rem;
}
</style>
