<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { ArrowLeft, Clock, User } from 'lucide-vue-next';

const props = defineProps({
    sesion: Object,
});

const diasSemana = {
    1: 'Lunes',
    2: 'Martes',
    3: 'Miércoles',
    4: 'Jueves',
    5: 'Viernes',
    6: 'Sábado',
};
</script>

<template>

    <Head title="Detalle de Sesión" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-foreground">
                Detalle de Sesión
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Botón Volver arriba -->
                <div class="mb-4">
                    <Button variant="ghost" size="sm" as-child>
                        <AppLink href="/sesiones">
                        <ArrowLeft class="h-4 w-4 mr-2" />
                        Volver
                        </AppLink>
                    </Button>
                </div>

                <div class="grid gap-6">
                    <Card>
                        <CardHeader>
                            <CardTitle>{{ sesion.disciplina.nombre }}</CardTitle>
                            <CardDescription>Información de la sesión</CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h3 class="text-sm font-medium text-muted-foreground mb-2">Disciplina</h3>
                                    <p class="text-lg font-semibold text-foreground">{{ sesion.disciplina.nombre }}</p>
                                    <p class="text-sm text-muted-foreground mt-1">{{ sesion.disciplina.descripcion || 'Sin descripción' }}</p>
                                </div>

                                <div>
                                    <h3 class="text-sm font-medium text-muted-foreground mb-2 flex items-center gap-2">
                                        <User class="h-4 w-4" />
                                        Instructor
                                    </h3>
                                    <p class="text-lg font-semibold text-foreground">{{ sesion.instructor.name }}</p>
                                    <p class="text-sm text-muted-foreground mt-1">{{ sesion.instructor.email }}</p>
                                </div>
                            </div>

                            <div class="pt-4 border-t">
                                <h3 class="text-sm font-medium text-muted-foreground mb-3 flex items-center gap-2">
                                    <Clock class="h-4 w-4" />
                                    Horario
                                </h3>
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-sm text-muted-foreground">Hora</p>
                                        <p class="text-lg font-semibold text-foreground">
                                            {{ sesion.horario.hora_inicio.substring(0, 5) }} - {{ sesion.horario.hora_fin.substring(0, 5) }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-muted-foreground mb-2">Días de la semana</p>
                                        <div class="flex gap-2 flex-wrap">
                                            <Badge v-for="dia in sesion.horario.dia_semana" :key="dia" variant="secondary">
                                                {{ diasSemana[dia] }}
                                            </Badge>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t">
                                <div>
                                    <h3 class="text-sm font-medium text-muted-foreground mb-1">Fecha de creación</h3>
                                    <p class="text-foreground">{{ new Date(sesion.created_at).toLocaleDateString('es-ES', { 
                                        year: 'numeric', 
                                        month: 'long', 
                                        day: 'numeric' 
                                    }) }}</p>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-muted-foreground mb-1">Última actualización</h3>
                                    <p class="text-foreground">{{ new Date(sesion.updated_at).toLocaleDateString('es-ES', { 
                                        year: 'numeric', 
                                        month: 'long', 
                                        day: 'numeric' 
                                    }) }}</p>
                                </div>
                            </div>

                            <div class="flex gap-4 pt-4">
                                <Button as-child>
                                    <Link :href="`/sesiones/${sesion.id}/edit`">
                                        Editar Sesión
                                    </AppLink>
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>

</template>
