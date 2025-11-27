<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

import { ArrowLeft } from 'lucide-vue-next';
import { successAlert, errorAlert } from '@/composables/useSweetAlert';

const props = defineProps({
    paquete: Object,
    membresias: Array,
    sesiones: Array,
});

const form = useForm({
    nombre: props.paquete.nombre,
    descripcion: props.paquete.descripcion || '',
    precio: props.paquete.precio,
    membresia_id: props.paquete.membresia_id,
    activo: props.paquete.activo,
    sesiones: [],
});

const sesionesSeleccionadas = ref(props.paquete.sesiones.map(s => s.id));

const isSesionChecked = (sesionId) => {
    return sesionesSeleccionadas.value.includes(sesionId);
};

const toggleSesion = (sesionId) => {
    const index = sesionesSeleccionadas.value.indexOf(sesionId);
    if (index > -1) {
        sesionesSeleccionadas.value.splice(index, 1);
    } else {
        sesionesSeleccionadas.value.push(sesionId);
    }
};

const formatSesion = (sesion) => {
    const dias = {
        1: 'L', 2: 'M', 3: 'X', 4: 'J', 5: 'V', 6: 'S'
    };
    const diasStr = sesion.horario.dia_semana.map(d => dias[d]).join(',');
    return `${sesion.disciplina.nombre} - ${sesion.horario.hora_inicio.substring(0, 5)}-${sesion.horario.hora_fin.substring(0, 5)} (${diasStr})`;
};

const submit = () => {
    form.sesiones = sesionesSeleccionadas.value;
    form.put(`/paquetes/${props.paquete.id}`, {
        onSuccess: () => {
            successAlert({
                title: '¡Actualizado!',
                text: 'El paquete ha sido actualizado correctamente',
            });
        },
        onError: () => {
            errorAlert({
                title: 'Error',
                text: 'Hubo un problema al actualizar el paquete',
            });
        },
    });
};
</script>

<template>

    <Head title="Editar Paquete" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-foreground">
                Editar Paquete
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

                <Card>
                    <CardHeader>
                        <CardTitle>Editar Paquete</CardTitle>
                        <CardDescription>Actualiza la información del paquete</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="submit" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <Label for="nombre">Nombre *</Label>
                                    <Input
                                        id="nombre"
                                        v-model="form.nombre"
                                        type="text"
                                        placeholder="Nombre del paquete"
                                        :class="{ 'border-red-500': form.errors.nombre }"
                                    />
                                    <p v-if="form.errors.nombre" class="text-sm text-red-500">
                                        {{ form.errors.nombre }}
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <Label for="precio">Precio *</Label>
                                    <Input
                                        id="precio"
                                        v-model="form.precio"
                                        type="number"
                                        step="0.01"
                                        min="0.01"
                                        placeholder="0.00"
                                        :class="{ 'border-red-500': form.errors.precio }"
                                    />
                                    <p v-if="form.errors.precio" class="text-sm text-red-500">
                                        {{ form.errors.precio }}
                                    </p>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label for="membresia_id">Membresía *</Label>
                                <Select v-model="form.membresia_id">
                                    <SelectTrigger :class="{ 'border-red-500': form.errors.membresia_id }">
                                        <SelectValue placeholder="Selecciona una membresía" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="membresia in membresias" :key="membresia.id" :value="membresia.id">
                                            {{ membresia.nombre }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <p v-if="form.errors.membresia_id" class="text-sm text-red-500">
                                    {{ form.errors.membresia_id }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="descripcion">Descripción</Label>
                                <Textarea
                                    id="descripcion"
                                    v-model="form.descripcion"
                                    placeholder="Descripción del paquete"
                                    rows="3"
                                    :class="{ 'border-red-500': form.errors.descripcion }"
                                />
                                <p v-if="form.errors.descripcion" class="text-sm text-red-500">
                                    {{ form.errors.descripcion }}
                                </p>
                            </div>

                            <div class="flex items-center space-x-3 p-3 border rounded-md">
                                <input
                                    type="checkbox"
                                    id="activo"
                                    v-model="form.activo"
                                    class="h-5 w-5 rounded border-gray-300 text-primary focus:ring-primary"
                                />
                                <Label for="activo" class="cursor-pointer font-medium">
                                    {{ form.activo ? 'Activo' : 'Inactivo' }}
                                </Label>
                            </div>

                            <div class="space-y-2">
                                <Label>Sesiones * (Selecciona al menos una)</Label>
                                <div class="border rounded-md p-4 max-h-64 overflow-y-auto space-y-2">
                                    <div v-for="sesion in sesiones" :key="sesion.id" class="flex items-start space-x-2">
                                        <input
                                            type="checkbox"
                                            :id="`sesion-${sesion.id}`"
                                            :checked="isSesionChecked(sesion.id)"
                                            @change="toggleSesion(sesion.id)"
                                            class="mt-1 h-4 w-4 rounded border-gray-300"
                                        />
                                        <label :for="`sesion-${sesion.id}`" class="text-sm cursor-pointer flex-1">
                                            {{ formatSesion(sesion) }}
                                        </label>
                                    </div>
                                </div>
                                <p v-if="form.errors.sesiones" class="text-sm text-red-500">
                                    {{ form.errors.sesiones }}
                                </p>
                            </div>

                            <div class="flex gap-4">
                                <Button type="submit" :disabled="form.processing">
                                    Actualizar Paquete
                                </Button>
                                <Button type="button" variant="outline" as-child>
                                    <Link href="/paquetes">
                                        Cancelar
                                    </Link>
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>

</template>
