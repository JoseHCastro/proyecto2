<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { ArrowLeft, User, Calendar, Dumbbell } from 'lucide-vue-next';

const props = defineProps({
    rutina: Object,
});
</script>

<template>

    <Head :title="`Rutina: ${rutina.ejercicio}`" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-foreground">
                Detalle de Rutina
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="mb-4">
                    <Button variant="ghost" size="sm" as-child>
                        <AppLink href="/rutinas">
                            <ArrowLeft class="h-4 w-4 mr-2" />
                            Volver
                        </AppLink>
                    </Button>
                </div>

                <div class="grid gap-6">
                    <Card>
                        <CardHeader>
                            <CardTitle>{{ rutina.ejercicio }}</CardTitle>
                            <CardDescription>Información de la rutina</CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h3 class="text-sm font-medium text-muted-foreground mb-2 flex items-center gap-2">
                                        <User class="h-4 w-4" />
                                        Socio (Cliente)
                                    </h3>
                                    <p class="text-lg font-semibold text-foreground">{{ rutina.socio.name }}</p>
                                    <p class="text-sm text-muted-foreground mt-1">{{ rutina.socio.email }}</p>
                                </div>

                                <div>
                                    <h3 class="text-sm font-medium text-muted-foreground mb-2 flex items-center gap-2">
                                        <User class="h-4 w-4" />
                                        Instructor
                                    </h3>
                                    <p class="text-lg font-semibold text-foreground">{{ rutina.instructor.name }}</p>
                                    <p class="text-sm text-muted-foreground mt-1">{{ rutina.instructor.email }}</p>
                                </div>
                            </div>

                            <div class="pt-4 border-t">
                                <h3 class="text-sm font-medium text-muted-foreground mb-3 flex items-center gap-2">
                                    <Dumbbell class="h-4 w-4" />
                                    Detalles del ejercicio
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <p class="text-sm text-muted-foreground">Ejercicio</p>
                                        <p class="text-xl font-bold text-foreground">{{ rutina.ejercicio }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-muted-foreground">Series</p>
                                        <p class="text-xl font-bold text-foreground">{{ rutina.series }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-muted-foreground">Repeticiones</p>
                                        <p class="text-xl font-bold text-foreground">{{ rutina.repeticiones }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-4 border-t">
                                <h3 class="text-sm font-medium text-muted-foreground mb-2 flex items-center gap-2">
                                    <Calendar class="h-4 w-4" />
                                    Fecha de creación
                                </h3>
                                <p class="text-foreground">{{ new Date(rutina.creada_en).toLocaleDateString('es-ES', { 
                                    year: 'numeric', 
                                    month: 'long', 
                                    day: 'numeric' 
                                }) }}</p>
                            </div>

                            <div class="flex gap-4 pt-4">
                                <Button as-child>
                                    <Link :href="`/rutinas/${rutina.id}/edit`">
                                        Editar Rutina
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
