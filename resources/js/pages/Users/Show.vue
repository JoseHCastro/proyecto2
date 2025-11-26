<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Pencil, ArrowLeft } from 'lucide-vue-next';

const props = defineProps({
    user: Object,
});
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
                        <Link href="/users">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Volver a la lista
                        </Link>
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
                                </Link>
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
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
