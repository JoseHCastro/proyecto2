<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ArrowLeft } from 'lucide-vue-next';
import { successAlert, errorAlert } from '@/composables/useSweetAlert';

const props = defineProps({
    socios: Array,
    instructores: Array,
});

const form = useForm({
    socio_id: null,
    instructor_id: null,
    ejercicio: '',
    series: '',
    repeticiones: '',
});

const submit = () => {
    form.post('/rutinas', {
        onSuccess: () => {
            successAlert({
                title: '¡Creado!',
                text: 'La rutina ha sido creada correctamente',
            });
        },
        onError: () => {
            errorAlert({
                title: 'Error',
                text: 'Hubo un problema al crear la rutina',
            });
        },
    });
};
</script>

<template>

    <Head title="Crear Rutina" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-foreground">
                Crear Rutina
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="mb-4">
                    <Button variant="ghost" size="sm" as-child>
                        <Link href="/rutinas">
                            <ArrowLeft class="h-4 w-4 mr-2" />
                            Volver
                        </Link>
                    </Button>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Nueva Rutina</CardTitle>
                        <CardDescription>Completa el formulario para crear una rutina</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="submit" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <Label for="socio_id">Socio (Cliente) *</Label>
                                    <Select v-model="form.socio_id">
                                        <SelectTrigger :class="{ 'border-red-500': form.errors.socio_id }">
                                            <SelectValue placeholder="Selecciona un socio" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem v-for="socio in socios" :key="socio.id" :value="socio.id">
                                                {{ socio.name }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <p v-if="form.errors.socio_id" class="text-sm text-red-500">
                                        {{ form.errors.socio_id }}
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <Label for="instructor_id">Instructor *</Label>
                                    <Select v-model="form.instructor_id">
                                        <SelectTrigger :class="{ 'border-red-500': form.errors.instructor_id }">
                                            <SelectValue placeholder="Selecciona un instructor" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem v-for="instructor in instructores" :key="instructor.id" :value="instructor.id">
                                                {{ instructor.name }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <p v-if="form.errors.instructor_id" class="text-sm text-red-500">
                                        {{ form.errors.instructor_id }}
                                    </p>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label for="ejercicio">Ejercicio *</Label>
                                <Input
                                    id="ejercicio"
                                    v-model="form.ejercicio"
                                    type="text"
                                    placeholder="Nombre del ejercicio"
                                    :class="{ 'border-red-500': form.errors.ejercicio }"
                                />
                                <p v-if="form.errors.ejercicio" class="text-sm text-red-500">
                                    {{ form.errors.ejercicio }}
                                </p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <Label for="series">Series *</Label>
                                    <Input
                                        id="series"
                                        v-model="form.series"
                                        type="number"
                                        min="1"
                                        placeholder="Número de series"
                                        :class="{ 'border-red-500': form.errors.series }"
                                    />
                                    <p v-if="form.errors.series" class="text-sm text-red-500">
                                        {{ form.errors.series }}
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <Label for="repeticiones">Repeticiones *</Label>
                                    <Input
                                        id="repeticiones"
                                        v-model="form.repeticiones"
                                        type="number"
                                        min="1"
                                        placeholder="Número de repeticiones"
                                        :class="{ 'border-red-500': form.errors.repeticiones }"
                                    />
                                    <p v-if="form.errors.repeticiones" class="text-sm text-red-500">
                                        {{ form.errors.repeticiones }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-4">
                                <Button type="submit" :disabled="form.processing">
                                    Crear Rutina
                                </Button>
                                <Button type="button" variant="outline" as-child>
                                    <Link href="/rutinas">
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
