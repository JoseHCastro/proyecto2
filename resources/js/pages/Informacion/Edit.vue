<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { ArrowLeft } from 'lucide-vue-next';
import { successAlert, errorAlert } from '@/composables/useSweetAlert';
import { toUrl } from '@/utils/routes';

const props = defineProps({
    informacion: Object,
});

const form = useForm({
    nombre: props.informacion?.nombre || 'Elevation Gym',
    direccion: props.informacion?.direccion || 'B/ San Lorenzo entre 4to y 5to anillo esquina Calle Cunumicita y calle El Transnochado',
    telefono: props.informacion?.telefono || '',
    correo: props.informacion?.correo || '',
});

const submit = () => {
    form.put(toUrl('/informacion'), {
        onSuccess: () => {
            successAlert({
                title: '¡Actualizado!',
                text: 'La información ha sido actualizada correctamente',
            });
        },
        onError: () => {
            errorAlert({
                title: 'Error',
                text: 'Hubo un problema al actualizar la información',
            });
        },
    });
};
</script>

<template>
    <Head title="Editar Información" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-foreground">
                Editar Información del Gimnasio
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="mb-4">
                    <Button variant="ghost" size="sm" as-child>
                        <AppLink href="/informacion">
                            <ArrowLeft class="mr-2 h-4 w-4" />
                            Volver
                        </AppLink>
                    </Button>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Información del Gimnasio</CardTitle>
                        <CardDescription>
                            Actualiza los datos de contacto y ubicación del gimnasio
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="submit" class="space-y-6">
                            <div class="space-y-2">
                                <Label for="nombre">Nombre del Gimnasio *</Label>
                                <Input
                                    id="nombre"
                                    v-model="form.nombre"
                                    type="text"
                                    required
                                    placeholder="Elevation Gym"
                                />
                                <span v-if="form.errors.nombre" class="text-sm text-destructive">
                                    {{ form.errors.nombre }}
                                </span>
                            </div>

                            <div class="space-y-2">
                                <Label for="direccion">Dirección *</Label>
                                <Textarea
                                    id="direccion"
                                    v-model="form.direccion"
                                    required
                                    placeholder="B/ San Lorenzo entre 4to y 5to anillo..."
                                    rows="3"
                                />
                                <span v-if="form.errors.direccion" class="text-sm text-destructive">
                                    {{ form.errors.direccion }}
                                </span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <Label for="telefono">Teléfono</Label>
                                    <Input
                                        id="telefono"
                                        v-model="form.telefono"
                                        type="text"
                                        placeholder="+591 3 123 4567"
                                    />
                                    <span v-if="form.errors.telefono" class="text-sm text-destructive">
                                        {{ form.errors.telefono }}
                                    </span>
                                </div>

                                <div class="space-y-2">
                                    <Label for="correo">Correo Electrónico</Label>
                                    <Input
                                        id="correo"
                                        v-model="form.correo"
                                        type="email"
                                        placeholder="info@elevationgym.com"
                                    />
                                    <span v-if="form.errors.correo" class="text-sm text-destructive">
                                        {{ form.errors.correo }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex justify-end gap-3">
                                <Button type="button" variant="outline" as-child>
                                    <AppLink href="/informacion">Cancelar</AppLink>
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
