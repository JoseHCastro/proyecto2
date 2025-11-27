<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ArrowLeft } from 'lucide-vue-next';
import { successAlert, errorAlert } from '@/composables/useSweetAlert';

const props = defineProps({
    suscripcion: Object,
});

const form = useForm({
    estado: props.suscripcion.estado,
    renovacion_automatica: props.suscripcion.renovacion_automatica,
});

const submit = () => {
    form.put(`/suscripciones/${props.suscripcion.id}`, {
        onSuccess: () => {
            successAlert({
                title: '¡Actualizado!',
                text: 'La suscripción ha sido actualizada correctamente',
            });
        },
        onError: () => {
            errorAlert({
                title: 'Error',
                text: 'Hubo un problema al actualizar la suscripción',
            });
        },
    });
};
</script>

<template>
    <Head title="Editar Suscripción" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-foreground">
                Editar Suscripción
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="mb-4">
                    <Button variant="ghost" size="sm" as-child>
                        <Link href="/suscripciones">
                            <ArrowLeft class="mr-2 h-4 w-4" />
                            Volver al listado
                        </Link>
                    </Button>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Editar Suscripción #{{ suscripcion.id }}</CardTitle>
                        <CardDescription>
                            Cliente: {{ suscripcion.usuario?.name }} - Paquete: {{ suscripcion.paquete?.nombre }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="submit" class="space-y-6">
                            <div class="rounded-lg border p-4 bg-muted/50">
                                <h4 class="text-sm font-medium mb-3">Información de la Suscripción</h4>
                                <div class="grid grid-cols-2 gap-3 text-sm">
                                    <div>
                                        <span class="text-muted-foreground">Cliente:</span>
                                        <span class="ml-2 font-medium">{{ suscripcion.usuario?.name }}</span>
                                    </div>
                                    <div>
                                        <span class="text-muted-foreground">Paquete:</span>
                                        <span class="ml-2 font-medium">{{ suscripcion.paquete?.nombre }}</span>
                                    </div>
                                    <div>
                                        <span class="text-muted-foreground">Inicio:</span>
                                        <span class="ml-2 font-medium">{{ new Date(suscripcion.fecha_inicio).toLocaleDateString('es-ES') }}</span>
                                    </div>
                                    <div>
                                        <span class="text-muted-foreground">Fin:</span>
                                        <span class="ml-2 font-medium">{{ new Date(suscripcion.fecha_fin).toLocaleDateString('es-ES') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label for="estado">Estado *</Label>
                                <Select v-model="form.estado" required>
                                    <SelectTrigger id="estado">
                                        <SelectValue placeholder="Selecciona un estado" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="activo">Activo</SelectItem>
                                        <SelectItem value="inactivo">Inactivo</SelectItem>
                                        <SelectItem value="suspendido">Suspendido</SelectItem>
                                        <SelectItem value="vencida">Vencida</SelectItem>
                                        <SelectItem value="cancelada">Cancelada</SelectItem>
                                    </SelectContent>
                                </Select>
                                <span v-if="form.errors.estado" class="text-sm text-destructive">
                                    {{ form.errors.estado }}
                                </span>
                                <p v-if="form.estado === 'cancelada'" class="text-sm text-orange-600 dark:text-orange-400">
                                    Al cancelar, todos los pagos impagos se marcarán como cancelados
                                </p>
                            </div>

                            <div class="flex items-center space-x-2">
                                <input
                                    id="renovacion_automatica"
                                    type="checkbox"
                                    v-model="form.renovacion_automatica"
                                    class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary"
                                />
                                <Label for="renovacion_automatica" class="cursor-pointer">
                                    Renovación automática
                                </Label>
                            </div>

                            <div class="flex justify-end gap-3">
                                <Button type="button" variant="outline" as-child>
                                    <Link href="/suscripciones">Cancelar</Link>
                                </Button>
                                <Button type="submit" :disabled="form.processing">
                                    {{ form.processing ? 'Guardando...' : 'Guardar Cambios' }}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
