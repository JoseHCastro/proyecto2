<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Pencil, ArrowLeft, Clock, Calendar } from 'lucide-vue-next';

const props = defineProps({
    horario: Object,
});

const diasSemana = {
    1: 'Lunes',
    2: 'Martes',
    3: 'Miércoles',
    4: 'Jueves',
    5: 'Viernes',
    6: 'Sábado'
};
</script>

<template>

    <Head title="Ver Horario" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-foreground">
                Detalles del Horario
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <!-- Botón Volver arriba -->
                <div class="mb-4">
                    <Button variant="ghost" size="sm" as-child>
                        <Link href="/horarios">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Volver a la lista
                        </Link>
                    </Button>
                </div>

                <Card>
                    <CardHeader>
                        <div class="flex items-start justify-between">
                            <div>
                                <CardTitle class="text-2xl flex items-center gap-2">
                                    <Clock class="h-6 w-6" />
                                    Horario
                                </CardTitle>
                                <CardDescription class="text-base mt-1">
                                    {{ horario.hora_inicio }} - {{ horario.hora_fin }}
                                </CardDescription>
                            </div>
                            <!-- Botón Editar en la esquina de la tarjeta -->
                            <Button as-child>
                                <Link :href="`/horarios/${horario.id}/edit`">
                                <Pencil class="mr-2 h-4 w-4" />
                                Editar
                                </Link>
                            </Button>
                        </div>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        
                        <!-- Días de la semana -->
                        <div class="space-y-3">
                            <div class="flex items-center gap-2">
                                <Calendar class="h-5 w-5 text-muted-foreground" />
                                <label class="text-sm font-medium text-muted-foreground">Días de la semana</label>
                            </div>
                            <div class="flex gap-2 flex-wrap">
                                <Badge 
                                    v-for="dia in horario.dia_semana" 
                                    :key="dia"
                                    variant="default"
                                    class="text-base px-3 py-1"
                                >
                                    {{ diasSemana[dia] }}
                                </Badge>
                            </div>
                        </div>

                        <!-- Horarios -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-muted-foreground">Hora de Inicio</label>
                                <div class="flex items-center gap-2">
                                    <Clock class="h-5 w-5 text-muted-foreground" />
                                    <p class="text-lg font-semibold">{{ horario.hora_inicio }}</p>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-muted-foreground">Hora de Fin</label>
                                <div class="flex items-center gap-2">
                                    <Clock class="h-5 w-5 text-muted-foreground" />
                                    <p class="text-lg font-semibold">{{ horario.hora_fin }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Fecha de creación -->
                        <div class="space-y-2 pt-4 border-t">
                            <label class="text-sm font-medium text-muted-foreground">Fecha de Creación</label>
                            <p class="text-base">{{ new Date(horario.created_at).toLocaleDateString('es-ES', { 
                                year: 'numeric', 
                                month: 'long', 
                                day: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            }) }}</p>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
