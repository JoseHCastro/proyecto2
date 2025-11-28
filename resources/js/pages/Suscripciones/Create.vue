<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ArrowLeft } from 'lucide-vue-next';
import { successAlert, errorAlert } from '@/composables/useSweetAlert';
import { toUrl } from '@/utils/routes';

const props = defineProps({
    clientes: Array,
    paquetes: Array,
});

const form = useForm({
    usuario_id: '',
    paquete_id: '',
    renovacion_automatica: false,
    cuotas: 1,
});

const paqueteSeleccionado = computed(() => {
    if (!form.paquete_id) return null;
    return props.paquetes.find(p => p.id === form.paquete_id);
});

const maxCuotas = computed(() => {
    if (!paqueteSeleccionado.value) return 1;
    return Math.floor(paqueteSeleccionado.value.membresia.duracion_dias / 2);
});

const montoPorCuota = computed(() => {
    if (!paqueteSeleccionado.value || form.cuotas < 1) return 0;
    return (paqueteSeleccionado.value.precio / form.cuotas).toFixed(2);
});

const diasEntrePagos = computed(() => {
    if (!paqueteSeleccionado.value || form.cuotas < 1) return 0;
    return Math.floor(paqueteSeleccionado.value.membresia.duracion_dias / form.cuotas);
});

const submit = () => {
    form.post(toUrl('/suscripciones'), {
        onSuccess: () => {
            successAlert({
                title: '¡Creado!',
                text: 'La suscripción ha sido creada correctamente',
            });
        },
        onError: () => {
            errorAlert({
                title: 'Error',
                text: 'Hubo un problema al crear la suscripción',
            });
        },
    });
};
</script>

<template>
    <Head title="Nueva Suscripción" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-foreground">
                Nueva Suscripción
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="mb-4">
                    <Button variant="ghost" size="sm" as-child>
                        <Link :href="toUrl('/suscripciones')">
                            <ArrowLeft class="mr-2 h-4 w-4" />
                            Volver al listado
                        </AppLink>
                    </Button>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Crear Nueva Suscripción</CardTitle>
                        <CardDescription>
                            Complete los datos para crear una nueva suscripción. Los pagos se generarán automáticamente.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="submit" class="space-y-6">
                            <div class="space-y-2">
                                <Label for="usuario_id">Cliente *</Label>
                                <Select v-model="form.usuario_id" required>
                                    <SelectTrigger id="usuario_id">
                                        <SelectValue placeholder="Selecciona un cliente" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="cliente in clientes" :key="cliente.id" :value="cliente.id">
                                            {{ cliente.name }} - {{ cliente.email }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <span v-if="form.errors.usuario_id" class="text-sm text-destructive">
                                    {{ form.errors.usuario_id }}
                                </span>
                            </div>

                            <div class="space-y-2">
                                <Label for="paquete_id">Paquete *</Label>
                                <Select v-model="form.paquete_id" required>
                                    <SelectTrigger id="paquete_id">
                                        <SelectValue placeholder="Selecciona un paquete" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="paquete in paquetes" :key="paquete.id" :value="paquete.id">
                                            {{ paquete.nombre }} - Bs {{ Number(paquete.precio).toFixed(2) }} ({{ paquete.membresia.nombre }})
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <span v-if="form.errors.paquete_id" class="text-sm text-destructive">
                                    {{ form.errors.paquete_id }}
                                </span>
                            </div>

                            <div v-if="paqueteSeleccionado" class="rounded-lg border p-4 bg-muted/50">
                                <h4 class="text-sm font-medium mb-2">Detalles del Paquete</h4>
                                <div class="grid grid-cols-2 gap-2 text-sm">
                                    <div>
                                        <span class="text-muted-foreground">Precio:</span>
                                        <span class="ml-2 font-medium">Bs {{ Number(paqueteSeleccionado.precio).toFixed(2) }}</span>
                                    </div>
                                    <div>
                                        <span class="text-muted-foreground">Duración:</span>
                                        <span class="ml-2 font-medium">{{ paqueteSeleccionado.membresia.duracion_dias }} días</span>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label for="cuotas">Número de Cuotas *</Label>
                                <Input
                                    id="cuotas"
                                    v-model.number="form.cuotas"
                                    type="number"
                                    min="1"
                                    :max="maxCuotas"
                                    required
                                />
                                <span v-if="form.errors.cuotas" class="text-sm text-destructive">
                                    {{ form.errors.cuotas }}
                                </span>
                                <p class="text-sm text-muted-foreground">
                                    Mínimo 1 cuota, máximo {{ maxCuotas }} cuotas (duración en días / 2)
                                </p>
                            </div>

                            <div v-if="paqueteSeleccionado && form.cuotas >= 1" class="rounded-lg border p-4 bg-blue-50 dark:bg-blue-950">
                                <h4 class="text-sm font-medium mb-2">Cálculo de Pagos</h4>
                                <div class="space-y-2 text-sm">
                                    <div>
                                        <span class="text-muted-foreground">Monto por cuota:</span>
                                        <span class="ml-2 font-medium text-blue-600 dark:text-blue-400">Bs {{ montoPorCuota }}</span>
                                    </div>
                                    <div>
                                        <span class="text-muted-foreground">Días entre pagos:</span>
                                        <span class="ml-2 font-medium">{{ diasEntrePagos }} días</span>
                                    </div>
                                    <div>
                                        <span class="text-muted-foreground">Total de cuotas:</span>
                                        <span class="ml-2 font-medium">{{ form.cuotas }}</span>
                                    </div>
                                    <div class="pt-2 border-t">
                                        <p class="text-xs text-muted-foreground">
                                            Se crearán {{ form.cuotas }} pago(s) con estado "impaga". El primer pago vence mañana.
                                        </p>
                                    </div>
                                </div>
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
                                    <Link :href="toUrl('/suscripciones')">Cancelar</AppLink>
                                </Button>
                                <Button type="submit" :disabled="form.processing">
                                    {{ form.processing ? 'Creando...' : 'Crear Suscripción' }}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
