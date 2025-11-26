<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { successAlert, errorAlert } from '@/composables/useSweetAlert';

const props = defineProps({
    user: Object,
    roles: Array,
});

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: '',
    password_confirmation: '',
    ci: props.user.ci,
    telefono: props.user.telefono,
    estado: props.user.estado,
    role: props.user.roles[0]?.name || '',
    especialidades: props.user.especialidades,
    biografia: props.user.biografia,
});

const submit = () => {
    form.put(`/users/${props.user.id}`, {
        onSuccess: () => {
            successAlert({
                title: '¡Usuario actualizado!',
                text: 'El usuario ha sido actualizado correctamente'
            }).then(() => {
                router.visit('/users');
            });
        },
        onError: () => {
            errorAlert({
                title: 'Error',
                text: 'No se pudo actualizar el usuario. Verifica los datos ingresados.'
            });
        }
    });
};
</script>

<template>

    <Head title="Editar Usuario" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-foreground">
                Editar Usuario
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-background shadow-sm sm:rounded-lg border border-border">
                    <div class="p-6 text-foreground">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <Label for="name">Nombre</Label>
                                    <Input id="name" v-model="form.name" required />
                                    <div v-if="form.errors.name" class="text-sm text-destructive">{{ form.errors.name }}
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <Label for="email">Email</Label>
                                    <Input id="email" type="email" v-model="form.email" required />
                                    <div v-if="form.errors.email" class="text-sm text-destructive">{{ form.errors.email
                                    }}</div>
                                </div>

                                <div class="space-y-2">
                                    <Label for="password">Contraseña (Dejar en blanco para mantener)</Label>
                                    <Input id="password" type="password" v-model="form.password" />
                                    <div v-if="form.errors.password" class="text-sm text-destructive">{{
                                        form.errors.password }}
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <Label for="password_confirmation">Confirmar Contraseña</Label>
                                    <Input id="password_confirmation" type="password"
                                        v-model="form.password_confirmation" />
                                </div>

                                <div class="space-y-2">
                                    <Label for="ci">CI</Label>
                                    <Input id="ci" v-model="form.ci" />
                                    <div v-if="form.errors.ci" class="text-sm text-destructive">{{ form.errors.ci }}
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <Label for="telefono">Teléfono</Label>
                                    <Input id="telefono" v-model="form.telefono" />
                                    <div v-if="form.errors.telefono" class="text-sm text-destructive">{{
                                        form.errors.telefono }}
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <Label for="role">Rol</Label>
                                    <Select v-model="form.role" required>
                                        <SelectTrigger>
                                            <SelectValue placeholder="Seleccionar rol" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem v-for="role in roles" :key="role.id" :value="role.name">
                                                {{ role.name }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <div v-if="form.errors.role" class="text-sm text-destructive">{{ form.errors.role }}
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <Label for="estado">Estado</Label>
                                    <Select v-model="form.estado" required>
                                        <SelectTrigger>
                                            <SelectValue placeholder="Seleccionar estado" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="activo">Activo</SelectItem>
                                            <SelectItem value="inactivo">Inactivo</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <div v-if="form.errors.estado" class="text-sm text-destructive">{{
                                        form.errors.estado }}
                                    </div>
                                </div>
                            </div>

                            <div v-if="form.role === 'Instructor'" class="space-y-6 border-t pt-6">
                                <h3 class="text-lg font-medium">Información de Instructor</h3>
                                <div class="grid grid-cols-1 gap-6">
                                    <div class="space-y-2">
                                        <Label for="especialidades">Especialidades</Label>
                                        <Input id="especialidades" v-model="form.especialidades"
                                            placeholder="Ej: Yoga, Pesas, Cardio" />
                                        <div v-if="form.errors.especialidades" class="text-sm text-destructive">{{
                                            form.errors.especialidades }}</div>
                                    </div>

                                    <div class="space-y-2">
                                        <Label for="biografia">Biografía</Label>
                                        <Textarea id="biografia" v-model="form.biografia" />
                                        <div v-if="form.errors.biografia" class="text-sm text-destructive">{{
                                            form.errors.biografia }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end gap-4">
                                <Button variant="outline" type="button" as-child>
                                    <Link href="/users">Cancelar</Link>
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
