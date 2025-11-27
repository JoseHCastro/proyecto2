<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { ArrowLeft } from 'lucide-vue-next';

const props = defineProps({
    disciplina: Object,
});
</script>

<template>

    <Head :title="`Disciplina: ${disciplina.nombre}`" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-foreground">
                Detalle de Disciplina
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Botón Volver arriba -->
                <div class="mb-4">
                    <Button variant="ghost" size="sm" as-child>
                        <Link href="/disciplinas">
                        <ArrowLeft class="h-4 w-4 mr-2" />
                        Volver
                        </Link>
                    </Button>
                </div>

                <div class="grid gap-6">
                    <Card>
                        <CardHeader>
                            <CardTitle>{{ disciplina.nombre }}</CardTitle>
                            <CardDescription>Información de la disciplina</CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div>
                                <h3 class="text-sm font-medium text-muted-foreground mb-1">Descripción</h3>
                                <p class="text-foreground">{{ disciplina.descripcion || 'Sin descripción' }}</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t">
                                <div>
                                    <h3 class="text-sm font-medium text-muted-foreground mb-1">Fecha de creación</h3>
                                    <p class="text-foreground">{{ new Date(disciplina.created_at).toLocaleDateString('es-ES', { 
                                        year: 'numeric', 
                                        month: 'long', 
                                        day: 'numeric' 
                                    }) }}</p>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-muted-foreground mb-1">Última actualización</h3>
                                    <p class="text-foreground">{{ new Date(disciplina.updated_at).toLocaleDateString('es-ES', { 
                                        year: 'numeric', 
                                        month: 'long', 
                                        day: 'numeric' 
                                    }) }}</p>
                                </div>
                            </div>

                            <div class="flex gap-4 pt-4">
                                <Button as-child>
                                    <Link :href="`/disciplinas/${disciplina.id}/edit`">
                                        Editar Disciplina
                                    </Link>
                                </Button>
                                <Button variant="outline" as-child>
                                    <Link href="/disciplinas">
                                        Volver al listado
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
