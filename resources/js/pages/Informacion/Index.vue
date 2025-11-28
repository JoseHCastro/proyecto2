<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Pencil, Building2, MapPin, Phone, Mail } from 'lucide-vue-next';

const props = defineProps({
    informacion: Object,
});
</script>

<template>
    <Head title="Información del Gimnasio" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-foreground">
                Información del Gimnasio
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <Card>
                    <CardHeader>
                        <div class="flex items-start justify-between">
                            <div>
                                <CardTitle class="text-2xl">{{ informacion?.nombre || 'Elevation Gym' }}</CardTitle>
                                <CardDescription class="mt-1">Información de contacto y ubicación</CardDescription>
                            </div>
                            <Button as-child>
                                <AppLink href="/informacion/edit">
                                    <Pencil class="mr-2 h-4 w-4" />
                                    Editar
                                </AppLink>
                            </Button>
                        </div>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <div class="space-y-4">
                            <div class="flex items-start gap-3">
                                <Building2 class="mt-1 h-5 w-5 text-muted-foreground" />
                                <div class="flex-1">
                                    <label class="text-sm font-medium text-muted-foreground">Nombre</label>
                                    <p class="text-base mt-1">{{ informacion?.nombre || 'No especificado' }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <MapPin class="mt-1 h-5 w-5 text-muted-foreground" />
                                <div class="flex-1">
                                    <label class="text-sm font-medium text-muted-foreground">Dirección</label>
                                    <p class="text-base mt-1">{{ informacion?.direccion || 'No especificado' }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <Phone class="mt-1 h-5 w-5 text-muted-foreground" />
                                <div class="flex-1">
                                    <label class="text-sm font-medium text-muted-foreground">Teléfono</label>
                                    <p class="text-base mt-1">{{ informacion?.telefono || 'No especificado' }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <Mail class="mt-1 h-5 w-5 text-muted-foreground" />
                                <div class="flex-1">
                                    <label class="text-sm font-medium text-muted-foreground">Correo Electrónico</label>
                                    <p class="text-base mt-1">{{ informacion?.correo || 'No especificado' }}</p>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
