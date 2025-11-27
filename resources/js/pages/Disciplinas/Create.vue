<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { successAlert, errorAlert } from '@/composables/useSweetAlert';

const form = useForm({
    nombre: '',
    descripcion: '',
});

const submit = () => {
    form.post('/disciplinas', {
        onSuccess: () => {
            successAlert({
                title: '¡Disciplina creada!',
                text: 'La disciplina ha sido creada correctamente'
            }).then(() => {
                router.visit('/disciplinas');
            });
        },
        onError: () => {
            errorAlert({
                title: 'Error',
                text: 'No se pudo crear la disciplina. Verifica los datos ingresados.'
            });
        }
    });
};
</script>

<template>

    <Head title="Crear Disciplina" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-foreground">
                Crear Disciplina
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-background shadow-sm sm:rounded-lg border border-border">
                    <div class="p-6 text-foreground">
                        <form @submit.prevent="submit" class="space-y-6">
                            
                            <div class="space-y-2">
                                <Label for="nombre">Nombre *</Label>
                                <Input 
                                    id="nombre" 
                                    v-model="form.nombre" 
                                    placeholder="Ej: Karate, Taekwondo, Judo..."
                                    required 
                                />
                                <div v-if="form.errors.nombre" class="text-sm text-destructive">
                                    {{ form.errors.nombre }}
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label for="descripcion">Descripción</Label>
                                <Textarea 
                                    id="descripcion" 
                                    v-model="form.descripcion" 
                                    placeholder="Describe la disciplina..."
                                    rows="4"
                                />
                                <div v-if="form.errors.descripcion" class="text-sm text-destructive">
                                    {{ form.errors.descripcion }}
                                </div>
                            </div>

                            <div class="flex justify-end gap-4">
                                <Button type="button" variant="outline" as-child>
                                    <Link href="/disciplinas">
                                        Cancelar
                                    </Link>
                                </Button>
                                <Button type="submit" :disabled="form.processing">
                                    {{ form.processing ? 'Guardando...' : 'Crear Disciplina' }}
                                </Button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>

</template>
