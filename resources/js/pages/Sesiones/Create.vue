<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { successAlert, errorAlert } from '@/composables/useSweetAlert';
import { toUrl } from '@/utils/routes';

const props = defineProps({
    disciplinas: Array,
    horarios: Array,
    instructores: Array,
});

const form = useForm({
    disciplina_id: null,
    horario_id: null,
    instructor_id: null,
});

const submit = () => {
    form.post(toUrl('/sesiones'), {
        onSuccess: () => {
            successAlert({
                title: '¡Sesión creada!',
                text: 'La sesión ha sido creada correctamente'
            }).then(() => {
                router.visit(toUrl('/sesiones'));
            });
        },
        onError: () => {
            errorAlert({
                title: 'Error',
                text: 'No se pudo crear la sesión. Verifica los datos ingresados.'
            });
        }
    });
};

const diasSemana = {
    1: 'Lun',
    2: 'Mar',
    3: 'Mié',
    4: 'Jue',
    5: 'Vie',
    6: 'Sáb',
};

const formatHorario = (horario) => {
    const dias = horario.dia_semana.map(d => diasSemana[d]).join(', ');
    const horas = `${horario.hora_inicio.substring(0, 5)} - ${horario.hora_fin.substring(0, 5)}`;
    return `${dias} | ${horas}`;
};
</script>

<template>

    <Head title="Crear Sesión" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-foreground">
                Crear Sesión
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-background shadow-sm sm:rounded-lg border border-border">
                    <div class="p-6 text-foreground">
                        <form @submit.prevent="submit" class="space-y-6">
                            
                            <div class="space-y-2">
                                <Label for="disciplina_id">Disciplina *</Label>
                                <Select v-model="form.disciplina_id">
                                    <SelectTrigger id="disciplina_id">
                                        <SelectValue placeholder="Selecciona una disciplina" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="disciplina in disciplinas" :key="disciplina.id" :value="disciplina.id">
                                            {{ disciplina.nombre }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <div v-if="form.errors.disciplina_id" class="text-sm text-destructive">
                                    {{ form.errors.disciplina_id }}
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label for="horario_id">Horario *</Label>
                                <Select v-model="form.horario_id">
                                    <SelectTrigger id="horario_id">
                                        <SelectValue placeholder="Selecciona un horario" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="horario in horarios" :key="horario.id" :value="horario.id">
                                            {{ formatHorario(horario) }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <div v-if="form.errors.horario_id" class="text-sm text-destructive">
                                    {{ form.errors.horario_id }}
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label for="instructor_id">Instructor *</Label>
                                <Select v-model="form.instructor_id">
                                    <SelectTrigger id="instructor_id">
                                        <SelectValue placeholder="Selecciona un instructor" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="instructor in instructores" :key="instructor.id" :value="instructor.id">
                                            {{ instructor.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <div v-if="form.errors.instructor_id" class="text-sm text-destructive">
                                    {{ form.errors.instructor_id }}
                                </div>
                            </div>

                            <div class="flex justify-end gap-4">
                                <Button type="button" variant="outline" as-child>
                                    <AppLink href="/sesiones">
                                        Cancelar
                                    </AppLink>
                                </Button>
                                <Button type="submit" :disabled="form.processing">
                                    {{ form.processing ? 'Guardando...' : 'Crear Sesión' }}
                                </Button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>

</template>
