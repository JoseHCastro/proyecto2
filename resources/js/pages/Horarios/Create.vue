<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Clock } from 'lucide-vue-next';
import { successAlert, errorAlert } from '@/composables/useSweetAlert';
import { ref } from 'vue';
import { toUrl } from '@/utils/routes';

const diasSemana = [
    { value: 1, label: 'Lunes' },
    { value: 2, label: 'Martes' },
    { value: 3, label: 'Miércoles' },
    { value: 4, label: 'Jueves' },
    { value: 5, label: 'Viernes' },
    { value: 6, label: 'Sábado' },
];

const diasSeleccionados = ref([]);

const form = useForm({
    dia_semana: [],
    hora_inicio: '',
    hora_fin: '',
});

const isDiaChecked = (dia) => {
    return diasSeleccionados.value.includes(Number(dia));
};

const toggleDia = (dia) => {
    const diaNum = Number(dia);
    
    if (diasSeleccionados.value.includes(diaNum)) {
        diasSeleccionados.value = diasSeleccionados.value.filter(d => d !== diaNum);
    } else {
        diasSeleccionados.value = [...diasSeleccionados.value, diaNum];
    }
};

const submit = () => {
    form.dia_semana = diasSeleccionados.value;
    
    form.post(toUrl('/horarios'), {
        onSuccess: () => {
            successAlert({
                title: '¡Horario creado!',
                text: 'El horario ha sido creado correctamente'
            }).then(() => {
                router.visit(toUrl('/horarios'));
            });
        },
        onError: () => {
            errorAlert({
                title: 'Error',
                text: 'No se pudo crear el horario. Verifica los datos ingresados.'
            });
        }
    });
};
</script>

<template>

    <Head title="Crear Horario" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-foreground">
                Crear Horario
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-background shadow-sm sm:rounded-lg border border-border">
                    <div class="p-6 text-foreground">
                        <form @submit.prevent="submit" class="space-y-6">
                            
                            <!-- Días de la semana -->
                            <div class="space-y-3">
                                <Label>Días de la semana</Label>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                    <div v-for="dia in diasSemana" :key="dia.value" 
                                        class="flex items-center space-x-2 p-3 rounded-md border border-border hover:bg-accent transition-colors cursor-pointer"
                                        @click="toggleDia(dia.value)">
                                        <input 
                                            type="checkbox"
                                            :id="`dia-${dia.value}`"
                                            :checked="isDiaChecked(dia.value)"
                                            @change="toggleDia(dia.value)"
                                            class="size-4 rounded border-input text-primary focus:ring-2 focus:ring-ring cursor-pointer"
                                        />
                                        <label 
                                            :for="`dia-${dia.value}`" 
                                            class="text-sm font-medium leading-none cursor-pointer select-none flex-1"
                                        >
                                            {{ dia.label }}
                                        </label>
                                    </div>
                                </div>
                                <div v-if="form.errors.dia_semana" class="text-sm text-destructive">
                                    {{ form.errors.dia_semana }}
                                </div>
                                <p class="text-sm text-muted-foreground">
                                    Selecciona al menos un día de la semana
                                </p>
                            </div>

                            <!-- Horas -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <Label for="hora_inicio" class="flex items-center gap-2">
                                        <Clock class="h-4 w-4" />
                                        Hora de Inicio
                                    </Label>
                                    <Input 
                                        id="hora_inicio" 
                                        type="time" 
                                        v-model="form.hora_inicio" 
                                        required
                                        step="300"
                                        class="text-lg"
                                    />
                                    <div v-if="form.errors.hora_inicio" class="text-sm text-destructive">
                                        {{ form.errors.hora_inicio }}
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <Label for="hora_fin" class="flex items-center gap-2">
                                        <Clock class="h-4 w-4" />
                                        Hora de Fin
                                    </Label>
                                    <Input 
                                        id="hora_fin" 
                                        type="time" 
                                        v-model="form.hora_fin" 
                                        required
                                        step="300"
                                        class="text-lg"
                                    />
                                    <div v-if="form.errors.hora_fin" class="text-sm text-destructive">
                                        {{ form.errors.hora_fin }}
                                    </div>
                                </div>
                            </div>

                            <!-- Botones -->
                            <div class="flex justify-end gap-4">
                                <Button variant="outline" type="button" as-child>
                                    <AppLink href="/horarios">Cancelar</AppLink>
                                </Button>
                                <Button type="submit" :disabled="form.processing">Guardar</Button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
