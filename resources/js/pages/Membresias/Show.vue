<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Pencil, ArrowLeft } from 'lucide-vue-next';

const props = defineProps({
    membresia: Object,
});
</script>

<template>

    <Head title="Ver Membresía" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-foreground">
                Detalles de la Membresía
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <!-- Botón Volver arriba -->
                <div class="mb-4">
                    <Button variant="ghost" size="sm" as-child>
                        <Link href="/membresias">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Volver a la lista
                        </Link>
                    </Button>
                </div>

                <Card>
                    <CardHeader>
                        <div class="flex items-start justify-between">
                            <div>
                                <CardTitle class="text-2xl">{{ membresia.nombre }}</CardTitle>
                                <CardDescription class="text-base mt-1">
                                    Duración: {{ membresia.duracion_dias }} días
                                </CardDescription>
                            </div>
                            <!-- Botón Editar en la esquina de la tarjeta -->
                            <Button as-child>
                                <Link :href="`/membresias/${membresia.id}/edit`">
                                <Pencil class="mr-2 h-4 w-4" />
                                Editar
                                </Link>
                            </Button>
                        </div>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-muted-foreground">Nombre</label>
                                <p class="text-base">{{ membresia.nombre }}</p>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-muted-foreground">Duración</label>
                                <p class="text-base">{{ membresia.duracion_dias }} días</p>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-muted-foreground">Estado</label>
                                <div>
                                    <Badge :variant="membresia.activo ? 'default' : 'destructive'">
                                        {{ membresia.activo ? 'Activo' : 'Inactivo' }}
                                    </Badge>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-muted-foreground">Fecha de Creación</label>
                                <p class="text-base">{{ new Date(membresia.created_at).toLocaleDateString('es-ES') }}</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-muted-foreground">Descripción</label>
                            <p class="text-base whitespace-pre-wrap">{{ membresia.descripcion || 'Sin descripción' }}</p>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
