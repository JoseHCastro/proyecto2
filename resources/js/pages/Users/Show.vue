<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Pencil, ArrowLeft, Plus, Trash2, Scale, Ruler, Calendar, Clock } from 'lucide-vue-next';
import { confirmAlert, successAlert, errorAlert } from '@/composables/useSweetAlert';
import { toUrl } from '@/utils/routes';

const props = defineProps({
    user: Object,
    mediciones: Array,
    asistencias: Array,
});

const isDialogOpen = ref(false);

const form = useForm({
    socio_id: props.user.id,
    peso_kg: '',
    estatura_cm: '',
    grasa_corporal: '',
    notas: '',
});

const submitMedicion = () => {
    form.post(toUrl('/mediciones-progreso'), {
        onSuccess: () => {
            successAlert({
                title: '¡Registrado!',
                text: 'La medición de progreso ha sido registrada correctamente',
            });
            form.reset();
            isDialogOpen.value = false;
        },
        onError: () => {
            errorAlert({
                title: 'Error',
                text: 'Hubo un problema al registrar la medición',
            });
        },
    });
};

const deleteMedicion = async (id) => {
    const result = await confirmAlert({
        title: '¿Eliminar medición?',
        text: 'Esta acción no se puede deshacer',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
    });

    if (result.isConfirmed) {
        router.delete(`/mediciones-progreso/${id}`, {
            onSuccess: () => {
                successAlert({
                    title: '¡Eliminado!',
                    text: 'La medición ha sido eliminada correctamente',
                });
            },
        });
    }
};

const calcularIMC = (peso, estatura) => {
    const estaturaMetros = estatura / 100;
    return (peso / (estaturaMetros * estaturaMetros)).toFixed(2);
};
</script>

<template>

    <Head title="Ver Usuario" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-foreground">
                Detalles del Usuario
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <!-- Botón Volver arriba -->
                <div class="mb-4">
                    <Button variant="ghost" size="sm" as-child>
                        <AppLink href="/users">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Volver a la lista
                        </AppLink>
                    </Button>
                </div>

                <Card>
                    <CardHeader>
                        <div class="flex items-start justify-between">
                            <div>
                                <CardTitle class="text-2xl">{{ user.name }}</CardTitle>
                                <CardDescription class="text-base mt-1">{{ user.email }}</CardDescription>
                            </div>
                            <!-- Botón Editar en la esquina de la tarjeta -->
                            <Button as-child>
                                <Link :href="`/users/${user.id}/edit`">
                                <Pencil class="mr-2 h-4 w-4" />
                                Editar
                                </AppLink>
                            </Button>
                        </div>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-muted-foreground">Email</label>
                                <p class="text-base">{{ user.email }}</p>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-muted-foreground">CI</label>
                                <p class="text-base">{{ user.ci || 'No especificado' }}</p>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-muted-foreground">Teléfono</label>
                                <p class="text-base">{{ user.telefono || 'No especificado' }}</p>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-muted-foreground">Estado</label>
                                <div>
                                    <Badge :variant="user.estado === 'activo' ? 'default' : 'destructive'">
                                        {{ user.estado }}
                                    </Badge>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-muted-foreground">Roles</label>
                                <div class="flex gap-2">
                                    <Badge variant="outline" v-for="role in user.roles" :key="role.id">
                                        {{ role.name }}
                                    </Badge>
                                </div>
                            </div>
                        </div>

                        <div v-if="user.roles.some(r => r.name === 'Instructor')" class="border-t pt-6 space-y-4">
                            <h3 class="text-lg font-medium">Información de Instructor</h3>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-muted-foreground">Especialidades</label>
                                <p class="text-base">{{ user.especialidades || 'No especificado' }}</p>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-muted-foreground">Biografía</label>
                                <p class="text-base whitespace-pre-wrap">{{ user.biografia || 'No especificado' }}</p>
                            </div>
                        </div>

                        <!-- Sección de Progreso (solo para Cliente) -->
                        <div v-if="user.roles.some(r => r.name === 'Cliente')" class="border-t pt-6 space-y-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-medium">Progreso</h3>
                                <Dialog v-model:open="isDialogOpen">
                                    <DialogTrigger as-child>
                                        <Button>
                                            <Plus class="mr-2 h-4 w-4" />
                                            Añadir Medición
                                        </Button>
                                    </DialogTrigger>
                                    <DialogContent class="sm:max-w-[500px]">
                                        <DialogHeader>
                                            <DialogTitle>Nueva Medición de Progreso</DialogTitle>
                                            <DialogDescription>
                                                Registra las mediciones del socio
                                            </DialogDescription>
                                        </DialogHeader>
                                        <form @submit.prevent="submitMedicion" class="space-y-4">
                                            <div class="grid grid-cols-2 gap-4">
                                                <div class="space-y-2">
                                                    <Label for="peso_kg">Peso (kg) *</Label>
                                                    <Input 
                                                        id="peso_kg" 
                                                        v-model="form.peso_kg" 
                                                        type="number" 
                                                        step="0.1"
                                                        required 
                                                    />
                                                    <span v-if="form.errors.peso_kg" class="text-sm text-destructive">
                                                        {{ form.errors.peso_kg }}
                                                    </span>
                                                </div>

                                                <div class="space-y-2">
                                                    <Label for="estatura_cm">Estatura (cm) *</Label>
                                                    <Input 
                                                        id="estatura_cm" 
                                                        v-model="form.estatura_cm" 
                                                        type="number" 
                                                        step="0.1"
                                                        required 
                                                    />
                                                    <span v-if="form.errors.estatura_cm" class="text-sm text-destructive">
                                                        {{ form.errors.estatura_cm }}
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="space-y-2">
                                                <Label for="grasa_corporal">% Grasa Corporal</Label>
                                                <Input 
                                                    id="grasa_corporal" 
                                                    v-model="form.grasa_corporal" 
                                                    type="number" 
                                                    step="0.1"
                                                    min="0"
                                                    max="100"
                                                />
                                                <span v-if="form.errors.grasa_corporal" class="text-sm text-destructive">
                                                    {{ form.errors.grasa_corporal }}
                                                </span>
                                            </div>

                                            <div class="space-y-2">
                                                <Label for="notas">Notas</Label>
                                                <Textarea 
                                                    id="notas" 
                                                    v-model="form.notas" 
                                                    placeholder="Observaciones adicionales..."
                                                    rows="3"
                                                />
                                                <span v-if="form.errors.notas" class="text-sm text-destructive">
                                                    {{ form.errors.notas }}
                                                </span>
                                            </div>

                                            <div class="flex justify-end gap-3">
                                                <Button type="button" variant="outline" @click="isDialogOpen = false">
                                                    Cancelar
                                                </Button>
                                                <Button type="submit" :disabled="form.processing">
                                                    {{ form.processing ? 'Guardando...' : 'Guardar' }}
                                                </Button>
                                            </div>
                                        </form>
                                    </DialogContent>
                                </Dialog>
                            </div>

                            <!-- Lista de mediciones -->
                            <div v-if="mediciones && mediciones.length > 0" class="rounded-md border">
                                <Table>
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead>Fecha</TableHead>
                                            <TableHead>Peso</TableHead>
                                            <TableHead>Estatura</TableHead>
                                            <TableHead>IMC</TableHead>
                                            <TableHead>Grasa</TableHead>
                                            <TableHead>Notas</TableHead>
                                            <TableHead class="text-right">Acciones</TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        <TableRow v-for="medicion in mediciones" :key="medicion.id">
                                            <TableCell class="font-medium">
                                                {{ new Date(medicion.medido_en).toLocaleDateString('es-ES') }}
                                            </TableCell>
                                            <TableCell>
                                                <div class="flex items-center gap-1">
                                                    <Scale class="h-4 w-4 text-muted-foreground" />
                                                    {{ medicion.peso_kg }} kg
                                                </div>
                                            </TableCell>
                                            <TableCell>
                                                <div class="flex items-center gap-1">
                                                    <Ruler class="h-4 w-4 text-muted-foreground" />
                                                    {{ medicion.estatura_cm }} cm
                                                </div>
                                            </TableCell>
                                            <TableCell>
                                                <Badge variant="outline">
                                                    {{ calcularIMC(medicion.peso_kg, medicion.estatura_cm) }}
                                                </Badge>
                                            </TableCell>
                                            <TableCell>
                                                {{ medicion.grasa_corporal ? medicion.grasa_corporal + '%' : '-' }}
                                            </TableCell>
                                            <TableCell class="max-w-xs truncate">
                                                {{ medicion.notas || '-' }}
                                            </TableCell>
                                            <TableCell class="text-right">
                                                <Button 
                                                    variant="ghost" 
                                                    size="sm"
                                                    @click="deleteMedicion(medicion.id)"
                                                >
                                                    <Trash2 class="h-4 w-4 text-destructive" />
                                                </Button>
                                            </TableCell>
                                        </TableRow>
                                    </TableBody>
                                </Table>
                            </div>

                            <div v-else class="text-center py-8 text-muted-foreground">
                                <p>No hay mediciones de progreso registradas</p>
                            </div>
                        </div>

                        <!-- Sección de Asistencia (solo para Cliente) -->
                        <div v-if="user.roles.some(r => r.name === 'Cliente')" class="border-t pt-6 space-y-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-medium">Asistencia</h3>
                                <Badge variant="secondary">
                                    Total: {{ asistencias?.length || 0 }}
                                </Badge>
                            </div>

                            <!-- Lista de asistencias -->
                            <div v-if="asistencias && asistencias.length > 0" class="rounded-md border">
                                <Table>
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead>Fecha</TableHead>
                                            <TableHead>Hora</TableHead>
                                            <TableHead>Día de la Semana</TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        <TableRow v-for="asistencia in asistencias" :key="asistencia.id">
                                            <TableCell class="font-medium">
                                                <div class="flex items-center gap-2">
                                                    <Calendar class="h-4 w-4 text-muted-foreground" />
                                                    {{ new Date(asistencia.asistio_en).toLocaleDateString('es-ES', { 
                                                        day: '2-digit', 
                                                        month: '2-digit', 
                                                        year: 'numeric' 
                                                    }) }}
                                                </div>
                                            </TableCell>
                                            <TableCell>
                                                <div class="flex items-center gap-2">
                                                    <Clock class="h-4 w-4 text-muted-foreground" />
                                                    {{ new Date(asistencia.asistio_en).toLocaleTimeString('es-ES', { 
                                                        hour: '2-digit', 
                                                        minute: '2-digit' 
                                                    }) }}
                                                </div>
                                            </TableCell>
                                            <TableCell>
                                                {{ new Date(asistencia.asistio_en).toLocaleDateString('es-ES', { 
                                                    weekday: 'long' 
                                                }) }}
                                            </TableCell>
                                        </TableRow>
                                    </TableBody>
                                </Table>
                            </div>

                            <div v-else class="text-center py-8 text-muted-foreground">
                                <p>No hay asistencias registradas</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
