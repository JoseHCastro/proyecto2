<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { ArrowLeft, DollarSign, Calendar } from 'lucide-vue-next';

const props = defineProps({
    paquete: Object,
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

    <Head :title="`Paquete: ${paquete.nombre}`" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-foreground">
                Detalle de Paquete
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="mb-4">
                    <Button variant="ghost" size="sm" as-child>
                        <Link href="/paquetes">
                            <ArrowLeft class="h-4 w-4 mr-2" />
                            Volver
                        </Link>
                    </Button>
                </div>

                <div class="grid gap-6">
                    <Card>
                        <CardHeader>
                            <div class="flex items-start justify-between">
                                <div>
                                    <CardTitle>{{ paquete.nombre }}</CardTitle>
                                    <CardDescription>Información del paquete</CardDescription>
                                </div>
                                <Badge :variant="paquete.activo ? 'default' : 'secondary'">
                                    {{ paquete.activo ? 'Activo' : 'Inactivo' }}
                                </Badge>
                            </div>
                        </CardHeader>
                        <CardContent class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h3 class="text-sm font-medium text-muted-foreground mb-2 flex items-center gap-2">
                                        <DollarSign class="h-4 w-4" />
                                        Precio
                                    </h3>
                                    <p class="text-2xl font-bold text-foreground">Bs {{ Number(paquete.precio).toFixed(2) }}</p>
                                </div>

                                <div>
                                    <h3 class="text-sm font-medium text-muted-foreground mb-2">Membresía</h3>
                                    <p class="text-lg font-semibold text-foreground">{{ paquete.membresia.nombre }}</p>
                                    <p class="text-sm text-muted-foreground mt-1">{{ paquete.membresia.descripcion || 'Sin descripción' }}</p>
                                </div>
                            </div>

                            <div v-if="paquete.descripcion" class="pt-4 border-t">
                                <h3 class="text-sm font-medium text-muted-foreground mb-2">Descripción</h3>
                                <p class="text-foreground">{{ paquete.descripcion }}</p>
                            </div>

                            <div class="pt-4 border-t">
                                <h3 class="text-sm font-medium text-muted-foreground mb-3">
                                    Sesiones incluidas ({{ paquete.sesiones.length }})
                                </h3>
                                <div class="space-y-3">
                                    <Card v-for="sesion in paquete.sesiones" :key="sesion.id" class="border-2">
                                        <CardContent class="p-4">
                                            <div class="flex items-start justify-between">
                                                <div class="space-y-2 flex-1">
                                                    <h4 class="font-semibold text-foreground">{{ sesion.disciplina.nombre }}</h4>
                                                    <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                                        <Calendar class="h-4 w-4" />
                                                        <span>
                                                            {{ sesion.horario.hora_inicio.substring(0, 5) }} - 
                                                            {{ sesion.horario.hora_fin.substring(0, 5) }}
                                                        </span>
                                                    </div>
                                                    <div class="flex gap-1 flex-wrap">
                                                        <Badge v-for="dia in sesion.horario.dia_semana" :key="dia" variant="secondary" class="text-xs">
                                                            {{ diasSemana[dia] }}
                                                        </Badge>
                                                    </div>
                                                    <p class="text-sm text-muted-foreground">
                                                        <span class="font-medium">Instructor:</span> {{ sesion.instructor.name }}
                                                    </p>
                                                </div>
                                            </div>
                                        </CardContent>
                                    </Card>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t">
                                <div>
                                    <h3 class="text-sm font-medium text-muted-foreground mb-1">Fecha de creación</h3>
                                    <p class="text-foreground">{{ new Date(paquete.created_at).toLocaleDateString('es-ES', { 
                                        year: 'numeric', 
                                        month: 'long', 
                                        day: 'numeric' 
                                    }) }}</p>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-muted-foreground mb-1">Última actualización</h3>
                                    <p class="text-foreground">{{ new Date(paquete.updated_at).toLocaleDateString('es-ES', { 
                                        year: 'numeric', 
                                        month: 'long', 
                                        day: 'numeric' 
                                    }) }}</p>
                                </div>
                            </div>

                            <div class="flex gap-4 pt-4">
                                <Button as-child>
                                    <Link :href="`/paquetes/${paquete.id}/edit`">
                                        Editar Paquete
                                    </Link>
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>

</template>
