<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { successAlert, errorAlert } from '@/composables/useSweetAlert';

const props = defineProps({
    membresia: Object,
});

const form = useForm({
    nombre: props.membresia.nombre,
    descripcion: props.membresia.descripcion,
    duracion_dias: props.membresia.duracion_dias,
    activo: props.membresia.activo,
});

const submit = () => {
    form.put(`/membresias/${props.membresia.id}`, {
        onSuccess: () => {
            successAlert({
                title: '¡Membresía actualizada!',
                text: 'La membresía ha sido actualizada correctamente'
            }).then(() => {
                router.visit('/membresias');
            });
        },
        onError: () => {
            errorAlert({
                title: 'Error',
                text: 'No se pudo actualizar la membresía. Verifica los datos ingresados.'
            });
        }
    });
};
</script>

<template>

    <Head title="Editar Membresía" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-foreground">
                Editar Membresía
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-background shadow-sm sm:rounded-lg border border-border">
                    <div class="p-6 text-foreground">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <Label for="nombre">Nombre</Label>
                                    <Input id="nombre" v-model="form.nombre" required />
                                    <div v-if="form.errors.nombre" class="text-sm text-destructive">{{ form.errors.nombre }}
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <Label for="duracion_dias">Duración (días)</Label>
                                    <Input id="duracion_dias" type="number" v-model="form.duracion_dias" min="1" required />
                                    <div v-if="form.errors.duracion_dias" class="text-sm text-destructive">{{ form.errors.duracion_dias }}
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <Label for="activo">Estado</Label>
                                    <Select v-model="form.activo">
                                        <SelectTrigger>
                                            <SelectValue placeholder="Seleccionar estado" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem :value="true">Activo</SelectItem>
                                            <SelectItem :value="false">Inactivo</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <div v-if="form.errors.activo" class="text-sm text-destructive">{{ form.errors.activo }}
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label for="descripcion">Descripción</Label>
                                <Textarea id="descripcion" v-model="form.descripcion" rows="4" 
                                    placeholder="Describe las características de esta membresía..." />
                                <div v-if="form.errors.descripcion" class="text-sm text-destructive">{{ form.errors.descripcion }}
                                </div>
                            </div>

                            <div class="flex justify-end gap-4">
                                <Button variant="outline" type="button" as-child>
                                    <Link href="/membresias">Cancelar</Link>
                                </Button>
                                <Button type="submit" :disabled="form.processing">Actualizar</Button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
