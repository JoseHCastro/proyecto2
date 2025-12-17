<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle, CardDescription, CardFooter } from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

import { Plus, Search, Eye, Pencil, Trash2, Package, Clock, Zap, CreditCard, ShoppingCart } from 'lucide-vue-next';
import { confirmAlert, successAlert } from '@/composables/useSweetAlert';
import { index as paquetesIndex, create as paquetesCreate, show as paquetesShow, edit as paquetesEdit, destroy as paquetesDestroy } from '@/routes/paquetes';
import { create as suscripcionesCreate } from '@/routes/suscripciones';

const props = defineProps({
    paquetes: Object,
    filters: Object,
    isCliente: {
        type: Boolean,
        default: false,
    },
    paquetesSuscritos: {
        type: Array,
        default: () => [],
    },
});

// Verificar si el cliente ya está suscrito a un paquete
const estaSuscrito = (paqueteId) => {
    return props.paquetesSuscritos.includes(paqueteId);
};

const search = ref(props.filters?.search || '');

watch(
    search,
    debounce((value) => {
        router.get(paquetesIndex.url(), { search: value }, { preserveState: true });
    }, 300)
);

const deletePaquete = async (id, nombre) => {
    const result = await confirmAlert({
        title: '¿Eliminar paquete?',
        text: `Se eliminará el paquete "${nombre}"`,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
    });

    if (result.isConfirmed) {
        router.delete(paquetesDestroy.url({ paquete: id }), {
            onSuccess: () => {
                successAlert({
                    title: '¡Eliminado!',
                    text: 'El paquete ha sido eliminado correctamente',
                });
            },
        });
    }
};
</script>

<template>

    <Head title="Paquetes" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-foreground">
                {{ isCliente ? 'Paquetes Disponibles' : 'Gestionar Paquetes' }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Vista Cliente: Cards -->
                <template v-if="isCliente">
                    <div v-if="paquetes.data.length === 0" class="text-center py-12">
                        <Package class="mx-auto h-16 w-16 text-muted-foreground mb-4" />
                        <p class="text-lg text-muted-foreground">No hay paquetes disponibles</p>
                    </div>
                    
                    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <Card 
                            v-for="paquete in paquetes.data" 
                            :key="paquete.id"
                            class="group hover:shadow-xl transition-all duration-300 hover:scale-[1.02] border-2 hover:border-primary/50 overflow-hidden"
                            :class="{ 'opacity-60': !paquete.activo }"
                        >
                            <div class="h-2 bg-gradient-to-r from-primary to-primary/50"></div>
                            <CardHeader class="pb-2">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="p-3 rounded-xl bg-primary/10 text-primary">
                                            <Package class="h-8 w-8" />
                                        </div>
                                        <div>
                                            <CardTitle class="text-xl">{{ paquete.nombre }}</CardTitle>
                                            <CardDescription class="flex items-center gap-1 mt-1">
                                                <Clock class="h-3 w-3" />
                                                {{ paquete.membresia.nombre }}
                                            </CardDescription>
                                        </div>
                                    </div>
                                    <Badge v-if="!paquete.activo" variant="secondary">
                                        No disponible
                                    </Badge>
                                </div>
                            </CardHeader>
                            <CardContent class="space-y-4">
                                <!-- Precio destacado -->
                                <div class="flex items-baseline gap-1 py-4">
                                    <span class="text-sm text-muted-foreground">Bs</span>
                                    <span class="text-4xl font-bold text-primary">{{ Number(paquete.precio).toFixed(0) }}</span>
                                    <span class="text-sm text-muted-foreground">.{{ (Number(paquete.precio) % 1).toFixed(2).slice(2) }}</span>
                                </div>
                                
                                <!-- Sesiones incluidas -->
                                <div class="space-y-2">
                                    <p class="text-sm font-medium flex items-center gap-2">
                                        <Zap class="h-4 w-4 text-primary" />
                                        Sesiones incluidas:
                                    </p>
                                    <div class="flex flex-wrap gap-2">
                                        <Badge 
                                            v-for="sesion in paquete.sesiones.slice(0, 4)" 
                                            :key="sesion.id" 
                                            variant="outline" 
                                            class="bg-muted/50"
                                        >
                                            {{ sesion.disciplina.nombre }}
                                        </Badge>
                                        <Badge v-if="paquete.sesiones.length > 4" variant="secondary">
                                            +{{ paquete.sesiones.length - 4 }} más
                                        </Badge>
                                    </div>
                                </div>
                                
                                <!-- Descripción si existe -->
                                <div v-if="paquete.descripcion" class="p-3 rounded-lg bg-muted/30 text-sm text-muted-foreground">
                                    {{ paquete.descripcion }}
                                </div>
                            </CardContent>
                            <CardFooter class="flex gap-2 pt-0">
                                <Button variant="outline" class="flex-1" as-child>
                                    <Link :href="paquetesShow.url({ paquete: paquete.id })">
                                        <Eye class="h-4 w-4 mr-2" />
                                        Ver detalles
                                    </Link>
                                </Button>
                                <Button 
                                    v-if="paquete.activo && !estaSuscrito(paquete.id)"
                                    variant="default" 
                                    class="flex-1" 
                                    as-child
                                >
                                    <Link :href="`${suscripcionesCreate.url()}?paquete_id=${paquete.id}`">
                                        <ShoppingCart class="h-4 w-4 mr-2" />
                                        Suscribirme
                                    </Link>
                                </Button>
                                <Badge v-else-if="estaSuscrito(paquete.id)" variant="secondary" class="flex-1 justify-center py-2">
                                    Ya suscrito
                                </Badge>
                            </CardFooter>
                        </Card>
                    </div>
                    
                    <!-- Pagination para Cliente -->
                    <div class="mt-6 flex justify-center" v-if="paquetes && paquetes.links && paquetes.links.length > 3">
                        <div class="flex gap-1">
                            <Link v-for="(link, k) in paquetes.links" :key="k" :href="link.url || '#'"
                                v-html="link.label" class="px-3 py-1 border rounded text-sm"
                                :class="{ 'bg-primary text-primary-foreground': link.active, 'opacity-50 cursor-not-allowed': !link.url }" />
                        </div>
                    </div>
                </template>

                <!-- Vista Admin: Tabla -->
                <template v-else>
                    <div class="overflow-hidden bg-card shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="mb-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                <div class="relative w-full sm:w-64">
                                    <Search class="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
                                    <Input
                                        v-model="search"
                                        placeholder="Buscar paquetes..."
                                        class="pl-8"
                                    />
                                </div>
                                <Button as-child>
                                    <Link :href="paquetesCreate.url()">
                                        <Plus class="mr-2 h-4 w-4" />
                                        Nuevo Paquete
                                    </Link>
                                </Button>
                            </div>

                            <div class="overflow-x-auto">
                                <Table>
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead>Nombre</TableHead>
                                            <TableHead>Membresía</TableHead>
                                            <TableHead>Sesiones</TableHead>
                                            <TableHead>Precio</TableHead>
                                            <TableHead>Estado</TableHead>
                                            <TableHead class="text-right">Acciones</TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        <TableRow v-if="paquetes.data.length === 0">
                                            <TableCell colspan="6" class="text-center">
                                                No hay paquetes registrados
                                            </TableCell>
                                        </TableRow>
                                        <TableRow v-for="paquete in paquetes.data" :key="paquete.id">
                                            <TableCell class="font-medium">{{ paquete.nombre }}</TableCell>
                                            <TableCell>{{ paquete.membresia.nombre }}</TableCell>
                                            <TableCell>
                                                <div class="flex gap-1 flex-wrap max-w-xs">
                                                    <Badge v-for="sesion in paquete.sesiones.slice(0, 3)" :key="sesion.id" variant="secondary" class="text-xs">
                                                        {{ sesion.disciplina.nombre }}
                                                    </Badge>
                                                    <Badge v-if="paquete.sesiones.length > 3" variant="outline" class="text-xs">
                                                        +{{ paquete.sesiones.length - 3 }}
                                                    </Badge>
                                                </div>
                                            </TableCell>
                                            <TableCell>Bs {{ Number(paquete.precio).toFixed(2) }}</TableCell>
                                            <TableCell>
                                                <Badge :variant="paquete.activo ? 'default' : 'secondary'">
                                                    {{ paquete.activo ? 'Activo' : 'Inactivo' }}
                                                </Badge>
                                            </TableCell>
                                            <TableCell class="text-right">
                                                <div class="flex justify-end gap-2">
                                                    <Button variant="ghost" size="icon" as-child>
                                                        <Link :href="paquetesShow.url({ paquete: paquete.id })">
                                                            <Eye class="h-4 w-4" />
                                                        </Link>
                                                    </Button>
                                                    <Button variant="ghost" size="icon" as-child>
                                                        <Link :href="paquetesEdit.url({ paquete: paquete.id })">
                                                            <Pencil class="h-4 w-4" />
                                                        </Link>
                                                    </Button>
                                                    <Button
                                                        variant="ghost"
                                                        size="icon"
                                                        @click="deletePaquete(paquete.id, paquete.nombre)"
                                                    >
                                                        <Trash2 class="h-4 w-4" />
                                                    </Button>
                                                </div>
                                            </TableCell>
                                        </TableRow>
                                    </TableBody>
                                </Table>
                            </div>

                            <!-- Pagination -->
                            <div class="mt-4 flex justify-center" v-if="paquetes && paquetes.links && paquetes.links.length > 3">
                                <div class="flex gap-1">
                                    <Link v-for="(link, k) in paquetes.links" :key="k" :href="link.url || '#'"
                                        v-html="link.label" class="px-3 py-1 border rounded text-sm"
                                        :class="{ 'bg-primary text-primary-foreground': link.active, 'opacity-50 cursor-not-allowed': !link.url }" />
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </AppLayout>

</template>
