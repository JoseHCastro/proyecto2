<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Building2, Download } from 'lucide-vue-next';

const props = defineProps({
    usuario: Object,
});

const descargarQR = () => {
    if (!props.usuario.url_qr) return;
    
    const link = document.createElement('a');
    link.href = `/storage/${props.usuario.url_qr}`;
    link.download = `qr_${props.usuario.name.replace(/\s+/g, '_')}.png`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};
</script>

<template>
    <Head title="Mi Código QR" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-foreground">
                Mi Código QR
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <Card>
                    <CardHeader class="text-center">
                        <CardTitle class="flex items-center justify-center gap-2">
                            <Building2 class="h-6 w-6" />
                            Código QR de Asistencia
                        </CardTitle>
                        <CardDescription>
                            Presenta este código QR al ingresar al gimnasio para registrar tu asistencia
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="flex flex-col items-center gap-6">
                        <div v-if="usuario.url_qr" class="rounded-lg border-4 border-primary p-4 bg-white">
                            <img 
                                :src="`/storage/${usuario.url_qr}`" 
                                :alt="`QR de ${usuario.name}`"
                                class="h-64 w-64"
                            />
                        </div>
                        <div v-else class="text-center text-muted-foreground">
                            <p>No tienes un código QR generado.</p>
                            <p class="text-sm mt-2">Contacta con el administrador.</p>
                        </div>

                        <div class="text-center space-y-2">
                            <p class="text-lg font-semibold">{{ usuario.name }}</p>
                            <p class="text-sm text-muted-foreground">{{ usuario.email }}</p>
                        </div>

                        <div class="text-center text-sm text-muted-foreground max-w-md">
                            <p class="font-medium mb-2">Instrucciones:</p>
                            <ol class="text-left space-y-1">
                                <li>1. Presenta este código al llegar al gimnasio</li>
                                <li>2. El código será escaneado automáticamente</li>
                                <li>3. Recibirás confirmación de tu asistencia</li>
                            </ol>
                        </div>                        
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
